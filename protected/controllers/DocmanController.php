<?php

class DocmanController extends BaseController
{
	
	public function accessRules()
    {
        return array(
            array('allow', 'roles' => array('admin', 'User')),
        );
    }
	
	
	public function behaviors()
	{
		return array(
            'ContactBehavior' => array(
                'class' => 'application.behaviors.ContactBehavior',
            ),
        );
	}
	
	public function onBeforeSave()
	{
	}

	public function onAfterDelete()
	{
	}
	
    public function actionIndex()
	{
		// for independent front-end testing!
		$this->render('/docman/index', array('module'=>null, 'data'=>null));
        //$this->renderPartial('/docman/index');
    }

	public function addTableToEvent( $module, $data )
	{
		$this->renderPartial('/docman/index', array('module'=>$module, 'data'=>$data));
	}


	public function getDocTable( $event_id )
	{
		$data = $this->getDocSetData(0, $event_id);
		$data["correspondence_mode"] = 1;
		echo $this->renderPartial('/docman/document_table', array('data'=>$data));
	}

	public function actionAjaxGetDocTable()
	{
		if (!Yii::app()->request->isAjaxRequest) { return; }
		if(Yii::app()->request->getQuery('id'))
		{
			$data = $this->getDocSetData(0);
		}else
		{
			$data = array();
		}
		if(Yii::app()->request->getQuery('in_correspondence'))
		{
			// correspondence_mode: if we are using the docman inside a correspondence event
			// we shouldn't allow to add
			$data["correspondence_mode"] = 1;
		}
		echo $this->renderPartial('/docman/document_table', array('data'=>$data));
	}

	private function getDocSetData($json, $event_id = null)
	{
		if(!$event_id)
		{
			$event_id = Yii::app()->request->getQuery('id');
		}
		$docSet = DocumentSet::model()->findByAttributes(array("event_id"=>$event_id));
		$doc = new Document($docSet->id);

		return $doc->ajaxGetDocSet($event_id, $json);
	}

    public function actionAjaxGetDocSet()
	{
		header("Content-Type: application/json");
		if (!Yii::app()->request->isAjaxRequest) { return; }
		print $this->getDocSetData(1);
	}

	public function actionAjaxGetDocTableEditRow()
	{
		if (!Yii::app()->request->isAjaxRequest) { return; }
		//$patient_id = $this->patient_id;

		$patient_id = Yii::app()->request->getQuery('patient_id');
		$macro_data = null;
		$macro_id= Yii::app()->request->getQuery('macro_id');
		if($macro_id > 0)
		{
			if ($api = Yii::app()->moduleAPI->get('OphCoCorrespondence')) {
				$macro_data = $api->getMacroTargets($patient_id, $macro_id);
			}
		}
		echo $this->renderPartial('/docman/document_row_edit', array('data'=>$macro_data));
	}

	public function actionAjaxGetDocTableRecipientRow()
	{
		if (!Yii::app()->request->isAjaxRequest) { return; }
		$patient_id = Yii::app()->request->getQuery('patient_id');
        $last_row_index = Yii::app()->request->getQuery('last_row_index');
		echo $this->renderPartial('/docman/document_row_recipient', array('row_index'=>$last_row_index+1));
	}

	public function actionAjaxGetContactData()
	{
		if (!Yii::app()->request->isAjaxRequest) { return; }
		$contact_id = Yii::app()->request->getQuery('contact_id');
		if($contact_id)
		{
			$contact = Contact::model()->findByPk($contact_id);
            $address = isset($contact->correspondAddress) ? $contact->correspondAddress : $contact->address;
            $data["contact_type"] = $contact->getType();
            // if the contact type is GP it's possible that it has no address, so we have to look for practice
            if(!$address)
            {
                if($data["contact_type"] == 'Gp')
                {
                    $patient_id = Yii::app()->request->getQuery('patient_id');
                    $patient = Patient::model()->findByPk($patient_id);
                    $address = isset($patient->practice->contact->correspondAddress) ? $patient->practice->contact->correspondAddress : $patient->practice->contact->address;
                }
            }

            if(!$address)
            {
                $data["address"] = "N/A";
            }else
            {
                $data["address"] = implode("\n", $address->getLetterArray());
            }

			echo json_encode($data);
		}
	}

    public function actionAjaxGetMacros()
	{
		header("Content-Type: application/json");
		if (!Yii::app()->request->isAjaxRequest) { return; }
		$doc = new Document(null);
		print $doc->ajaxGetMacros();
	}

	protected function getMacros()
	{
		$doc = new Document(null);
		return $doc->getMacros();
	}

	public function actionAjaxUpdateTargetAddress()
	{
		if (!Yii::app()->request->isAjaxRequest) { return; }
		$doc_target_id = Yii::app()->request->getQuery('doc_target_id');
		if($doc_target_id) {
			$doc_data = DocumentTarget::model()->findByPk($doc_target_id);
			if ($new_address = Yii::app()->request->getQuery('new_address'))
			{
				$doc_data->address = $new_address;
				$doc_data->contact_modified = 1;
				$doc_data->save();
				echo $new_address;
			}
		}
		return;
	}

	public function actionAjaxGetMacroTargets()
	{
		$macro_data = null;
		if($macro_id= Yii::app()->request->getQuery('macro_id'))
		{
			if ($api = Yii::app()->moduleAPI->get('OphCoCorrespondence')) {
				$patient_id = Yii::app()->request->getQuery('patient_id');
				$macro_data = $api->getMacroTargets($patient_id, $macro_id);
			}
		}
		echo $this->renderPartial('/docman/document_row_edit',array('data'=>$macro_data));
	}

	public function actionCreateNewCorrespondence($macroId)
	{
		if ($api = Yii::app()->moduleAPI->get('OphCoCorrespondence')) {
			$api->createCorrespondenceContent($api->createNewCorrespondenceEvent($this->episode->id),
				$macroId);
		}
	}

}