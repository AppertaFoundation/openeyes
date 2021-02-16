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
        $this->render('/docman/index', array('module' => null, 'data' => null));
        //$this->renderPartial('/docman/index');
    }

    public function actionGetCreateTable($element_id = null, $macro_id = 7)
    {
        $document_set = new DocumentSet();
        $document_instance = new DocumentInstance();
        $document_target = new DocumentTarget();
        $document_output = new DocumentOutput();
        $macro_data = array();
        $letter_targets = array();

        if (($api = Yii::app()->moduleAPI->get('OphCoCorrespondence')) && $macro_id) {
            $patient_id = Yii::app()->request->getQuery('patient_id');
            $macro_data = $api->getMacroTargets($patient_id, $macro_id);
            $letter_targets = $api->getMacroTargetsByElementLetterId($element_id);
        }

        $this->renderPartial('/docman/_create', array(
            'row_index' => (isset($row_index) ? $row_index : 0),
            'document_set' => $document_set,
            'document_target' => $document_target,
            'document_output' => $document_output,
            'macro_data' => $macro_data,
            'macro_id' => $macro_id,
            'letter_targets' => $letter_targets,
        ));
    }

    public function addTableToEvent($module, $data)
    {
        $this->renderPartial('/docman/index', array('module' => $module, 'data' => $data));
    }


    public function getDocTable($event_id)
    {
        $data = $this->getDocSetData(0, $event_id);
        $data["correspondence_mode"] = 1;
        echo $this->renderPartial('/docman/document_table', array('data' => $data));
    }

    public function actionAjaxGetDocTable()
    {
        if (!Yii::app()->request->isAjaxRequest) {
            return;
        }
        if ($event_id = Yii::app()->request->getQuery('id')) {
            $data = $this->getDocSetData(0);
        } else {
            $data = array();
        }
        if (Yii::app()->request->getQuery('in_correspondence')) {
            // correspondence_mode: if we are using the docman inside a correspondence event
            // we shouldn't allow to add
            $data["correspondence_mode"] = 1;
        }
        echo $this->renderPartial('/docman/document_table', array('data' => $data));
    }

    private function getDocSetData($json, $event_id = null)
    {
        if (!$event_id) {
            $event_id = Yii::app()->request->getQuery('id');
        }
        $docSet = DocumentSet::model()->findByAttributes(array("event_id" => $event_id));
        $doc = new Document($docSet->id);

        return $doc->ajaxGetDocSet($event_id, $json);
    }

    public function actionAjaxGetDocSet()
    {
        header("Content-Type: application/json");
        if (!Yii::app()->request->isAjaxRequest) {
            return;
        }
        print $this->getDocSetData(1);
    }

    public function actionAjaxGetDocTableEditRow()
    {
        if (!Yii::app()->request->isAjaxRequest) {
            return;
        }
        //$patient_id = $this->patient_id;

        $patient_id = Yii::app()->request->getQuery('patient_id');
        $macro_data = null;
        $macro_id = Yii::app()->request->getQuery('macro_id');
        if ($macro_id > 0) {
            if ($api = Yii::app()->moduleAPI->get('OphCoCorrespondence')) {
                $macro_data = $api->getMacroTargets($patient_id, $macro_id);
            }
        }
        echo $this->renderPartial('/docman/document_row_edit', array('data' => $macro_data));
    }

    public function actionAjaxGetDocTableRecipientRow()
    {
        if (!Yii::app()->request->isAjaxRequest) {
            return;
        }
        $patient_id = Yii::app()->request->getQuery('patient_id');
        $patient = Patient::model()->findByPk($patient_id);
        $last_row_index = Yii::app()->request->getQuery('last_row_index');
        $selected_contact_type = Yii::app()->request->getQuery('selected_contact_type');
        $is_mandatory = Yii::app()->request->getQuery('is_mandatory');

        $contact_id = null;
        $address = null;
        if ($selected_contact_type == 'PATIENT') {
            $contact_id = $patient->contact_id;
        } else {
            if ($selected_contact_type == \SettingMetadata::model()->getSetting('gp_label')) {
                if (isset($patient->practice)) {
                    $contact_id = $patient->practice->contact->id;
                } else {
                    if (isset($patient->gp->contact)) {
                        $contact_id = $patient->gp->contact->id;
                    }
                }
            }
        }

        $contact_name = null;
        $contact_nickname = null;
        $email = null;
        if ($contact_id) {
            $contact = Contact::model()->findByPk($contact_id);
            $address = isset($contact->correspondAddress) ? $contact->correspondAddress : $contact->address;
            if (!$address) {
                if ($selected_contact_type == \SettingMetadata::model()->getSetting('gp_label')) {
                    $address = isset($patient->practice->contact->correspondAddress) ? $patient->practice->contact->correspondAddress : $patient->practice->contact->address;
                }
            }
            $contact_name = $contact->getFullName();
            $contact_nickname = $contact->nick_name;
            $email = isset($contact) ? $contact->email : null;
        }

        if ($address) {
            $address = implode("\n", $address->getLetterArray());
        }


        $this->renderPartial(
            '/docman/document_row_recipient',
            array(
                'contact_id' => $contact_id,
                'address' => $address,
                'row_index' => $last_row_index + 1,
                'selected_contact_type' => $selected_contact_type,
                'contact_name' => $contact_name,
                'contact_nickname' => $contact_nickname,
                'can_send_electronically' => isset($patient->gp) || isset($patient->practice),
                'is_internal_referral' => true,
                'is_mandatory' => $is_mandatory == 'true' ? true : false,
                'email' => $email,
                'patient_id' => $patient_id,
            )
        );
        $this->getApp()->end();
    }


    public function actionAjaxGetMacros()
    {
        header("Content-Type: application/json");
        if (!Yii::app()->request->isAjaxRequest) {
            return;
        }
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
        if (!Yii::app()->request->isAjaxRequest) {
            return;
        }
        $doc_target_id = Yii::app()->request->getQuery('doc_target_id');
        if ($doc_target_id) {
            $doc_data = DocumentTarget::model()->findByPk($doc_target_id);
            if ($new_address = Yii::app()->request->getQuery('new_address')) {
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
        $patient_id = null;
        $macro_data = null;
        if ($macro_id = Yii::app()->request->getQuery('macro_id')) {
            if ($api = Yii::app()->moduleAPI->get('OphCoCorrespondence')) {
                $patient_id = Yii::app()->request->getQuery('patient_id');
                $macro_data = $api->getMacroTargets($patient_id, $macro_id);
            }
        }

        $element = new ElementLetter();
        echo $this->renderPartial('/docman/_create', array(
                'row_index' => (isset($row_index) ? $row_index : 0),
                'macro_data' => $macro_data,
                'patient_id' => $patient_id,
                'macro_id' => $macro_id,
                'element' => $element,
                'can_send_electronically' => true,
            ));
    }

    public function actionCreateNewCorrespondence($macroId)
    {
        if ($api = Yii::app()->moduleAPI->get('OphCoCorrespondence')) {
            $api->createCorrespondenceContent(
                $api->createNewCorrespondenceEvent($this->episode->id),
                $macroId
            );
        }
    }
}
