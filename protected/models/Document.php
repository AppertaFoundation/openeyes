<?php

/**
 * OpenEyes
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
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
    public $is_draft;

    public function __construct($docsetid = null)
    {
        $this->docsetid = $docsetid;
    }

    /**
     * returns the default contact types, Internal referral excluded as it is optional
     */
    public static function getContactTypes()
    {
        return [
            'GP' => \Yii::app()->params['gp_label'],
            'PATIENT' => 'Patient',
            'DRSS' => 'DRSS',
            'OPTOMETRIST' => 'Optometrist',
            'OTHER' => 'Other',
        ];
    }

    public static function getInternalReferralContactType()
    {
        return [
            'INTERNALREFERRAL' => 'Booking'
        ];
    }

    /**
     * Pull the record values from this AR data
     */
    private function getValues($data)
    {
        $array = array();
        if (is_array($data) and isset($data[0]) and is_array($data[0]['originalAttributes'])) {
            foreach ($data as $idx => $d) {
                $rec = $d->getAttributes();
                foreach ($rec as $fld => $val) {
                    //foreach ($d['originalAttributes'] as $fld=>$val) {
                    $array[$idx][$fld] = $val;
                }
            }
        }

        return $array;
    }


    public function createNew($eventId)
    {
        $ds = new DocumentSet();
        $ds->event_id = $eventId;
        $ds->save();

        return ($ds->id);
    }


    /**
     * Load the entire document set in to an object, for sending to the UI
     */
    public function ajaxGetDocSet($event_id, $jsonOutput = 1)
    {
        $array['status']['err'] = 0;
        $array['status']['errmsg'] = 'success';

        //  GET THE DOCUMENT SET
        $data = DocumentSet::model()->findAll('event_id=?', array($event_id));

        if (!$data) {
            $array['status']['err'] = 1;
            $array['status']['errmsg'] = 'not found';

            return json_encode($array);
        }

        $array['data']['docset'] = $this->getValues($data);

        //  GET THE DOCUMENT INSTANCES
        //  TODO: fix to retrieve MAX(version) for each document_instance_id

        $document_set_id = $array['data']['docset'][0]['id'];
        $data = DocumentInstance::model()->findAll('document_set_id=?', array($document_set_id));
        $array['data']['docinst'] = $this->getValues($data);

        $document_instance_ids = array();
        foreach ($array['data']['docinst'] as $idx => $d) {
            $document_instance_ids[] = $d['id'];
        }
        $document_instance_ids_csv = implode(',', $document_instance_ids);
        $data = DocumentInstanceData::model()->findAllByAttributes(array('document_instance_id' => $document_instance_ids));
        $array['data']['docinstversion'] = $this->getValues($data);

        //  GET THE DOCUMENT TARGETS
        $data = $this->getDocumentTargets($document_instance_ids);
        $array['data'] = array_merge($array['data'], $data);

        $data['document_set_id'] = $document_set_id;
        $array['data']['document_set_id'] = $document_set_id;

//      $contacts[0] = array('type'=>'PATIENT')
        if ($jsonOutput) {
            $json = json_encode($array);

            return $json;
        } else {
            return $data;
        }
    }


    public function getDocumentTargets($document_instance_ids)
    {
        $array = array();
        $data = DocumentTarget::model()->findAllByAttributes(array('document_instance_id' => $document_instance_ids));
        $array['doctargets'] = $this->getValues($data);

        //  GET THE DOCUMENT OUTPUTS
        $document_target_ids = array();
        foreach ($array['doctargets'] as $idx => $d) {
            $document_target_ids[] = $d['id'];
        }
        $data = DocumentOutput::model()->findAllByAttributes(array('document_target_id' => $document_target_ids));
        $array['docoutputs'] = $this->getValues($data);

        return $array;
    }

    public function getDocumentTargetsStructured($event_id)
    {
        $array = array();
        $return = array();

        $instance = DocumentInstance::model()->findByAttributes(array('event_id' => $event_id));
        $data = DocumentTarget::model()->findAllByAttributes(array('document_instance_id' => $instance->id));

        $array['doctargets'] = $this->getValues($data);

        foreach ($array['doctargets'] as $target) {
            $output_data = DocumentOutput::model()->findAllByAttributes(array('document_target_id' => $target->id));
            if ($output_data->ToCc == 'to') {
                $return["to"]["contact_type"] = $target->contact_name;
                $return["to"]["contact_id"] = $target->contact_id;
                $return["to"]["contact_name"] = $target->contact_name;
                $return["to"]["address"] = $target->address;
            } else {
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


    /**
     * Dispatches all the documents associated with this DocSet, that have not already been sent.
     */
    public function dispatchAll()
    {
        //  Loop through all documents in this set
        $filenames = array();
        $ids = DocumentInstance::getByDocsetId($this->docsetid);
        foreach ($ids as $id) {
            // If the status is not pending, skip
            $status = DocumentTarget::getStatusById($id);
            if ($status <> 'PENDING') {
                continue;
            }

            // Dispatch the object
            $docInstance = DocumentInstance::getById($id);
            $filename = $docInstance->dispatch();
            if ($filename) {
                $filenames[] = $filename;
            }
        }


        // If any objects were of type LOCALPRINT, then their filenames will be in the
        // filenames[] array. Use the pdf command line tool to join them in to one
        // PDF file, append the print() javascript function, and send to the display

        if ($filenames) {
            // TODO
        }
    }

    public function createNewDocSet($data)
    {
        $doc_set = null;
        if (isset($data['DocumentSet']['id'])) {
            $doc_set = DocumentSet::model()->findByPk($data['DocumentSet']['id']);
        }
        $doc_set = $doc_set ? $doc_set : new DocumentSet();

        $doc_set->event_id = $this->event_id;
        // TODO: check errors here!
        $result = $doc_set->save();
        if (!$result) {
            \OELog::log(print_r($doc_set->getErrors(), true));
        }

        $doc_instance = null;
        if (isset($data['DocumentInstance']['id'])) {
            $doc_instance = DocumentInstance::model()->findByPk($data['DocumentInstance']['id']);
        }
        $doc_instance = $doc_instance ? $doc_instance : new DocumentInstance();

        $doc_instance->document_set_id = $doc_set->id;
        $doc_instance->correspondence_event_id = $this->event_id;
        $result = $doc_instance->save();

        if (!$result) {
            \OELog::log(print_r($doc_instance->getErrors(), true));
        }

        $doc_instance_version = null;
        if (isset($data['DocumentInstanceData']['id'])) {
            $doc_instance_version = DocumentInstanceData::model()->findByPk($data['DocumentInstanceData']['id']);
        }
        $doc_instance_version = $doc_instance_version ? $doc_instance_version : new DocumentInstanceData();

        $doc_instance_version->document_instance_id = $doc_instance->id;
        $doc_instance_version->macro_id = $data['macro_id'];

        $result = $doc_instance_version->save();
        if (!$result) {
            \OELog::log(print_r($doc_instance_version->getErrors(), true));
        }

        if (isset($data['DocumentTarget'])) {
            // Before saving new Targets we check if there were any Recipients to remove
            if (Yii::app()->controller->action->id === 'update') {
                $this->removeTargetAndOutput($doc_set->id, $data['DocumentTarget']);
            }

            foreach ($data['DocumentTarget'] as $key => $post_document_target) {
                $data = array(
                    'to_cc' => $post_document_target['attributes']['ToCc'],
                    'contact_type' => $post_document_target['attributes']['contact_type'],
                    'contact_id' => $post_document_target['attributes']['contact_id'] ?? null,
                    'contact_name' => $post_document_target['attributes']['contact_name'] ?? null,
                    'address' => $post_document_target['attributes']['address'],
                    'email' => $post_document_target['attributes']['email'] ?? null,
                );

                if (isset($post_document_target['attributes']['id'])) {
                    $data['id'] = $post_document_target['attributes']['id'];
                }
                $doc_target = $this->createNewDocTarget($doc_instance, $data);

                if (isset($post_document_target['DocumentOutput'])) {
                    // If an output id is not posted back we remove it from the DB
                    if (isset($post_document_target['attributes']['id'])) {
                        $this->removeOutputs(
                            $post_document_target['attributes']['id'],
                            $post_document_target['DocumentOutput']
                        );
                    }

                    foreach ($post_document_target['DocumentOutput'] as $document_output) {
                        if (isset($document_output['output_type'])) {
                            $data = array(
                                'output_type' => $document_output['output_type'],
                            );

                            if (isset($document_output['id'])) {
                                $data['id'] = $document_output['id'];
                            }

                            if ($this->is_draft) {
                                $data['output_status'] = "DRAFT";
                            } else {
                                $data['output_status'] = "PENDING";
                            }

                            $this->createNewDocOutput($doc_target, $doc_instance_version, $data);
                        }
                    }
                }
            }
        }
    }

    public function createNewDocTarget($doc_instance, $data)
    {
        $doc_target = null;

        if (isset($data['id'])) {
            $doc_target = DocumentTarget::model()->findByPk($data['id']);
        }
        $doc_target = $doc_target ? $doc_target : new DocumentTarget();

        $doc_target->document_instance_id = $doc_instance->id;
        $doc_target->contact_type = $data['contact_type'];
        $doc_target->contact_id = $data['contact_id'];
        $doc_target->contact_name = $data['contact_name'];
        $doc_target->ToCc = $data['to_cc'];
        $doc_target->address = $data['address'];
        $doc_target->email = $data['email'];
        $doc_target->save();

        if (!$doc_target->save()) {
            \OELog::log(print_r($doc_target->getErrors(), true));
        }

        return $doc_target;
    }

    public function createNewDocOutput($doc_target, $doc_instance_version, $data)
    {
        $doc_output = null;

        if (isset($data['id'])) {
            $doc_output = DocumentOutput::model()->findByPk($data['id']);
        }
        $doc_output = $doc_output ? $doc_output : new DocumentOutput();

        $doc_output->document_target_id = $doc_target->id;
        $doc_output->document_instance_data_id = $doc_instance_version->id;
        $doc_output->output_type = $data['output_type'];
        $doc_output->requestor_id = 0;

        if (isset($data['output_status']) && $doc_output->output_type != "COMPLETE") {
            $doc_output->output_status = $data['output_status'];
        }

        if (!$doc_output->save()) {
            \OELog::log(print_r($doc_output->getErrors(), true));
        }
    }

    /**
     * When the user removes a saved DocumentTarget we remove the target and the output from the DB
     * @param type $document_id
     * @param type $new_document_targets
     */
    protected function removeTargetAndOutput($document_id, $new_document_targets)
    {
        $document_set = DocumentSet::model()->findByPk($document_id);

        if (isset($document_set->document_instance[0]->document_target)) {
            // get saved document_targets
            $document_targets = $document_set->document_instance[0]->document_target;
            foreach ($document_targets as $document_target) {
                //check if the document_target is in he posted ones
                $is_in = false;
                foreach ($new_document_targets as $new_document_target) {
                    if (isset($new_document_target['attributes']['id']) && $document_target->id == $new_document_target['attributes']['id']) {
                        $is_in = true;
                    }
                }

                // the target in the DB is not posted back so we can delete it as they removed it from the UI
                if (!$is_in) {
                    $deletable = true;
                    foreach ($document_target->document_output as $document_output) {
                        // we don't delete if it is already DocMan delivered
                        if ($document_output->output_status === 'COMPLETE' && $document_output->output_type === 'Docman') {
                            $deletable = false;
                        }
                    }

                    if ($deletable) {
                        foreach ($document_target->document_output as $document_output) {
                            $document_output->delete();
                        }
                        $document_target->delete();
                    }
                }
            }
        }
    }

    protected function removeOutputs($document_target_id, $new_document_outputs)
    {
        $document_output_ids = array();
        foreach ($new_document_outputs as $document_output) {
            if (isset($document_output['id']) && isset($document_output['output_type'])) {
                $document_output_ids[] = $document_output['id'];
            }
        }

        $criteria = new CDbCriteria();

        $criteria->addCondition('document_target_id = :document_target_id');
        $criteria->params[':document_target_id'] = $document_target_id;

        $criteria->addNotInCondition('id', $document_output_ids);

        DocumentOutput::model()->deleteAll($criteria);
    }
}
