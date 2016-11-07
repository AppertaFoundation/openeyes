<?php
/**
 * OpenEyes
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * Written by Antony Penn
 * 
 * This is the main API end point for the document management functions.
 */

class Document //extends BaseActiveRecord
{
    
    public $docsetid;
	public $event_id;
    
    public function __construct($docsetid=null)
    {
        $this->docsetid = $docsetid;
    }


	/**
	 * Pull the record values from this AR data
	 */
	private function getValues($data)
	{
		$array = array();
		if (is_array($data) and isset($data[0]) and is_array($data[0]['originalAttributes'])) {
			foreach ($data as $idx=>$d) {
				$rec = $d->getAttributes();
				foreach ($rec as $fld=>$val) {
				//foreach ($d['originalAttributes'] as $fld=>$val) {
					$array[$idx][$fld] = $val;
				}
			}
		}
		return $array;
	}

	
	public function createNew($eventId)
	{
		$ds = new DocumentSet;
		$ds->event_id = $eventId;
		$ds->save();
		return ($ds->id);
	}


	/**
	 * Load the entire document set in to an object, for sending to the UI
	 */	
	public function ajaxGetDocSet($event_id, $jsonOutput=1)
	{
		$array['status']['err']=0;
		$array['status']['errmsg'] = 'success';

		//  GET THE DOCUMENT SET
		$data = DocumentSet::model()->findAll('event_id=?',array($event_id));

		if(!$data) {
			$array['status']['err']=1;
			$array['status']['errmsg'] = 'not found';
			return json_encode($array);
		}
		
		$array['data']['docset'] = $this->getValues($data);

		//  GET THE DOCUMENT INSTANCES
		//  TODO: fix to retrieve MAX(version) for each document_instance_id
		
		$document_set_id = $array['data']['docset'][0]['id'];
		$data = DocumentInstance::model()->findAll('document_set_id=?',array($document_set_id));
		$array['data']['docinst'] = $this->getValues($data);

		$document_instance_ids = array();
		foreach ($array['data']['docinst'] as $idx=>$d) { $document_instance_ids[] = $d['id']; }
		$document_instance_ids_csv = implode(',' ,$document_instance_ids);
		$data = DocumentInstanceData::model()->findAllByAttributes(array('document_instance_id'=>$document_instance_ids));
		$array['data']['docinstversion'] = $this->getValues($data);

		//  GET THE DOCUMENT TARGETS
		$data = $this->getDocumentTargets($document_instance_ids);
		$array['data'] = array_merge($array['data'],$data);
                
                $data['document_set_id'] = $document_set_id;
                $array['data']['document_set_id'] = $document_set_id;
		
//		$contacts[0] = array('type'=>'PATIENT')
		if($jsonOutput) {
                    $json = json_encode($array);
                    return $json;
		}else{
                    return $data;
		}
	}
	

	public function getDocumentTargets($document_instance_ids)
	{
		$array = array();
		$data = DocumentTarget::model()->findAllByAttributes(array('document_instance_id'=>$document_instance_ids));
		$array['doctargets'] = $this->getValues($data);

		//  GET THE DOCUMENT OUTPUTS
		$document_target_ids = array();
		foreach ($array['doctargets'] as $idx=>$d) {
			$document_target_ids[] = $d['id'];
		}
		$data = DocumentOutput::model()->findAllByAttributes(array('document_target_id'=>$document_target_ids));
		$array['docoutputs'] = $this->getValues($data);
		return $array;
	}

	public function getDocumentTargetsStructured($event_id)
    {
        $array = array();
        $return = array();

        $instance = DocumentInstance::model()->findByAttributes(array('event_id'=>$event_id));
        $data = DocumentTarget::model()->findAllByAttributes(array('document_instance_id'=>$instance->id));

        $array['doctargets'] = $this->getValues($data);

        foreach($array['doctargets'] as $target)
        {
            $output_data = DocumentOutput::model()->findAllByAttributes(array('document_target_id'=>$target->id));
            if($output_data->ToCc == 'to')
            {
                $return["to"]["contact_type"] = $target->contact_name;
                $return["to"]["contact_id"] = $target->contact_id;
                $return["to"]["contact_name"] = $target->contact_name;
                $return["to"]["address"] = $target->address;
            }else
            {
                $return["cc"][]["contact_type"] = $target->contact_name;
                $return["cc"][]["contact_id"] = $target->contact_id;
                $return["cc"][]["contact_name"] = $target->contact_name;
                $return["cc"][]["address"] = $target->address;
            }
        }

        return $return;
    }
	
	/**
	 * Load the entire document set in to an object, for sending to the UI as JSON array
	 */	
	public function ajaxGetMacros()
	{
		$data = $this->getMacros();
		$json = json_encode($data);
		return $json;
	}

	public function getMacros()
	{
		$element_letter = new ElementLetter();

		$data = $element_letter->getLetter_macros();
		return $data;
	}
	
	
		
	// Need new document version.
	// This will:
	// Copy the current _version record to a new version number
	
	public function updateDocument($macroId, $eventId, $target, $outputs, $data=null)
	{
	}

	/**
	 * Dispatches all the documents associated with this DocSet, that have not already been sent.
	 */
	public function dispatchAll()
	{
        //  Loop through all documents in this set
        $filenames = array();
	    $ids = DocumentInstance::getByDocsetId($this->docsetid);
	    foreach($ids as $id)
        {
            // If the status is not pending, skip
            $status = DocumentTarget::getStatusById($id);
            if($status<>'PENDING') continue;
            
            // Dispatch the object
            $docInstance = DocumentInstance::getById($id);
            $filename = $docInstance->dispatch();
            if($filename) { $filenames[] = $filename; }
        }
        
        
        // If any objects were of type LOCALPRINT, then their filenames will be in the
        // filenames[] array. Use the pdf command line tool to join them in to one
        // PDF file, append the print() javascript function, and send to the display
        
        if($filenames)
        {
            // TODO
        }
	}

	public function createNewDocSet()
	{
                $doc_set = null;
                if( isset($_POST['document_set_id']) ){
                    $doc_set = DocumentSet::model()->findByPk($_POST['document_set_id']);
                }
		$doc_set = $doc_set ? $doc_set : new DocumentSet();
                
		$doc_set->event_id = $this->event_id;
		// TODO: check errors here!
		$doc_set->save();

                
                $doc_instance = null;
                if( isset($_POST['document_instance_id']) ){
                    $doc_instance = DocumentInstance::model()->findByPk($_POST['document_instance_id']);
                }
		$doc_instance = $doc_instance ? $doc_instance : new DocumentInstance();
      
		$doc_instance->document_set_id = $doc_set->id;
		$doc_instance->correspondence_event_id = $this->event_id;
		$doc_instance->save();

                
                $doc_instance_version = null;
                if( isset($_POST['document_instance_data_id']) ){
                    $doc_instance_version = DocumentInstanceData::model()->findByPk($_POST['document_instance_data_id']);
                }
		$doc_instance_version = $doc_instance_version ? $doc_instance_version : new DocumentInstanceData();
                
		$doc_instance_version->document_instance_id = $doc_instance->id;
		$doc_instance_version->macro_id = $_POST['macro_id'];
                
		$doc_instance_version->save();

		if(is_array(@$_POST['target_type']))
		{
			foreach($_POST['target_type'] as $key => $val)
			{
				$data = array(
                                    'contact_type' => $_POST['contact_type'][$key],
                                    'contact_id' => $_POST['contact_id'][$key],
                                    'address' => $_POST['address'][$key]);
                                
                                if(isset($_POST['document_target_id'][$key])){
                                    $data['id'] = $_POST['document_target_id'][$key];
                                }
				$doc_target = $this->createNewDocTarget($doc_instance, $data);

				if(@$_POST['print'][$key])
				{
					$data = array(
                                            'ToCc'=>$val,
                                            'output_type'=>'Print');
                                        
                                        if(isset($_POST['id'][$key])){
                                            $data['id'] = $_POST['id'][$key];
                                        }
					$this->createNewDocOutput($doc_target, $doc_instance_version, $data);
				}
				if(@$_POST['docman'][$key])
				{
					$data = array(
                                            'ToCc'=>$val,
                                            'output_type'=>'Docman');
                                        if(isset($_POST['id'][$key])){
                                            $data['id'] = $_POST['id'][$key];
                                        }

					$this->createNewDocOutput($doc_target, $doc_instance_version, $data);
				}
			}
		}
	}

	public function createNewDocTarget( $doc_instance, $data )
	{
            $doc_target = null;

            if( isset($data['id'])){
                $doc_target = DocumentTarget::model()->findByPk($data['id']);
            }
            $doc_target = $doc_target ? $doc_target : new DocumentTarget();

            $doc_target->document_instance_id = $doc_instance->id;
            $doc_target->contact_type = $data['contact_type'];
            $doc_target->contact_id = $data['contact_id'];
            if(is_numeric($data['contact_id']))
            {
                $doc_target->contact_name = Contact::model()->findByPk($data['contact_id'])->getFullName();
            }else
            {
                $doc_target->contact_name = $data['contact_id'];
                $data['contact_id'] = null;
            }
            $doc_target->address = $data['address'];
            $doc_target->save();
            return $doc_target;
	}

	public function createNewDocOutput($doc_target, $doc_instance_version, $data)
	{
                $doc_output = null;
          
                if( isset($data['id'])){
                    $doc_output = DocumentOutput::model()->findByPk($data['id']);
                }
                $doc_output = $doc_output ? $doc_output : new DocumentOutput();
                
		$doc_output->document_target_id=$doc_target->id;
		$doc_output->document_instance_data_id = $doc_instance_version->id;
		$doc_output->ToCc = $data['ToCc'];
		$doc_output->output_type = $data['output_type'];
		$doc_output->requestor_id = 'OE';
		$doc_output->save();

	}

}
