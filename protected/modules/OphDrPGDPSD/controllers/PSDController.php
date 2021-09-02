<?php

class PSDController extends DefaultController
{
    protected static $action_types = array(
        'getSetMedications' => self::ACTION_TYPE_FORM,
        'getMedication' => self::ACTION_TYPE_FORM,
        'getPathStep' => self::ACTION_TYPE_FORM,
        'createPSD' => self::ACTION_TYPE_FORM,
        'RemovePSD' => self::ACTION_TYPE_FORM,
        'unlockPathStep' => self::ACTION_TYPE_FORM,
        'confirmAdministration' => self::ACTION_TYPE_FORM,
        'checkPincode' => self::ACTION_TYPE_FORM,
    );

    protected $api;

    protected function beforeAction($action)
    {
        $this->api = \Yii::app()->moduleAPI->get('OphDrPGDPSD');
        return parent::beforeAction($action);
    }

    public function actionCreatePSD()
    {
        $prefix = 'PresetAssignment';
        $response_data = array(
            'msg' => null,
            'success' => 0
        );
        $data = Yii::app()->request->getParam($prefix, array());
        $patients = array_key_exists('patients', $data) ? $data['patients'] : array();
        $presets = array_key_exists('presets', $data) ? $data['presets'] : array();
        $errors = array();
        $both_eye_id = MedicationLaterality::BOTH;
        $left_eye_id = MedicationLaterality::LEFT;
        $right_eye_id = MedicationLaterality::RIGHT;
        $transaction = Yii::app()->db->beginTransaction();
        foreach ($patients as $patient) {
            foreach ($presets as $preset) {
                $preset_entries = $preset['entries'];
                unset($preset['entries']);
                foreach ($preset_entries as $key => &$entry) {
                    if (isset($entry['laterality']) && $entry['laterality'] && intval($entry['laterality']) === $both_eye_id) {
                        $entry['pair_key'] = $key + 1;
                        $dup_entry = $entry;
                        $entry['laterality'] = $right_eye_id;
                        $dup_entry['laterality'] = $left_eye_id;
                        $preset_entries[] = $dup_entry;
                    }
                }
                $assignment = new OphDrPGDPSD_Assignment();
                $assignment->confirmed = 1;
                $assignment->attributes = array_merge($patient, $preset);
                $assignment->cacheMeds($preset_entries);
                if (!$assignment->save()) {
                    $errors = $assignment->getErrors();
                }
            }
        }
        if ($errors) {
            $transaction->rollback();
            $response_data['msg'] = $errors;
            $response_data['success'] = 0;
        } else {
            $transaction->commit();
            $response_data['msg'] = 'Assigned Successfully';
            $response_data['success'] = 1;
            $audit_assigned_psd = $assignment->pgdpsd_id ? ", Assigned PSD: {$assignment->pgdpsd_id}" : "";
            Audit::add('PSD Assignment', 'created assignment', "Assignment id: {$assignment->id}{$audit_assigned_psd}");
        }
        $this->renderJSON($response_data);
    }

    public function actionRemovePSD()
    {
        $ret = array(
            'success' => 0
        );
        $assignment_id = \Yii::app()->request->getParam('assignment_id', null);
        $assignment = OphDrPGDPSD_Assignment::model()->findByPk($assignment_id);
        if ($assignment && intval($assignment->status) === $assignment::STATUS_TODO) {
            $transaction = \Yii::app()->db->beginTransaction();
            $assignment->delete();
        }
        if ($assignment->getErrors()) {
            $transaction->rollback();
        } else {
            $transaction->commit();
            $ret['success'] = 1;
            Audit::add('PSD Assignment', 'removed assignment', "Assignment id: {$assignment_id}");
        }
        $this->renderJSON($ret);
    }

    public function actionGetPathStep($partial, $pathstep_id, $patient_id, $for_administer = null, $interactive = 1)
    {
        $assignment = OphDrPGDPSD_Assignment::model()->findByPk($pathstep_id);
        $can_remove_psd = \Yii::app()->user->checkAccess('Prescribe') && intval($assignment->status) === $assignment::STATUS_TODO && !$assignment->elements ? '' : 'disabled';
        if ($assignment) {
            if (intval($interactive)) {
                $interactive = $assignment->getAppointmentDetails()['date'] === 'Today' ? 1 : 0;
            }
            $dom = $this->renderPartial(
                '/pathstep/pathstep_view',
                array(
                    'assignment' => $assignment,
                    'partial' => intval($partial),
                    'patient_id' => $patient_id,
                    'for_administer' => $for_administer,
                    'is_prescriber' => \Yii::app()->user->checkAccess('Prescribe'),
                    'can_remove_psd' => $can_remove_psd,
                    'interactive' => intval($interactive),
                ),
                true
            );
            $this->renderJSON($dom);
        }
    }

    public function actionUnlockPathStep()
    {
        $data = Yii::app()->request->getParam('Assignment', array());
        $patient_id = array_key_exists('patient_id', $data) ? $data['patient_id'] : null;
        $assignment_id = array_key_exists('assignment_id', $data) ? $data['assignment_id'] : null;
        $this->actionGetPathStep(0, $assignment_id, $patient_id, 1);
    }

    public function actionConfirmAdministration()
    {
        $pgdpsd_api = \Yii::app()->moduleAPI->get('OphDrPGDPSD');
        $data = \Yii::app()->request->getParam('Assignment', array());
        $assignment_id = array_key_exists('assignment_id', $data) ? $data['assignment_id'] : null;
        $patient_id = array_key_exists('patient_id', $data) ? $data['patient_id'] : null;
        $assignment = \OphDrPGDPSD_Assignment::model()->findByPk($assignment_id);
        $entries = array_key_exists('entries', $data) ? $data['entries'] : array();
        $worklist_patient_id = array_key_exists('worklist_patient_id', $data) ? intval($data['worklist_patient_id']) : null;
        $firm = \Firm::model()->findByPk(\Yii::app()->session['selected_firm_id']);
        $assignment_entries = array();
        $audit_data = array();
        foreach ($assignment->assigned_meds as $med) {
            $assignment_entries[$med->id] = $med;
        }
        $errors = array();
        $latest_element = null;
        if (!$assignment->elements) {
            $latest_element = $pgdpsd_api->getPatientLatestDAelement($assignment->patient, $worklist_patient_id, $this->event_type);
        } else {
            foreach ($assignment->elements as $element) {
                $element_event = $element->event;
                if(!$element_event || ($element_event && $element_event->deleted)){
                    continue;
                }
                $element_firm_id = $element->event->episode->firm->getSubspecialtyID();
                $event_date = date('Y-m-d', strtotime($element->event->event_date));
                if (intval($element_firm_id) === intval($firm->getSubspecialtyID()) && $event_date === date('Y-m-d')) {
                    $latest_element = $element;
                    break;
                }
            }
        }
        $transaction = \Yii::app()->db->beginTransaction();
        $has_administered = false;
        foreach ($entries as $key => &$entry) {
            $entry['administered'] = isset($entry['administered']) && $entry['administered'] ? intval($entry['administered']) : 0;
            $entry['administered_time'] = isset($entry['administered_time']) && $entry['administered_time'] ? date('Y-m-d H:i:s', $entry['administered_time'] / 1000) : null;
            $entry['administered_by'] = isset($entry['administered_by']) && $entry['administered_by'] ? $entry['administered_by'] : null;
            $entry['laterality'] = isset($entry['laterality']) && $entry['laterality'] ? $entry['laterality'] : null;
            $assignment_entries[$key]->attributes = $entry;
            $assignment_entries[$key]->save();
            // if a medication is administered, a record in event_medication_use will be needed
            if ($assignment_entries[$key]->administered) {
                $has_administered = true;
                $audit_data[] = $assignment_entries[$key]->medication_id . ' was administered by ' . $assignment_entries[$key]->administered_by;
            }
        }
        $errors = array_merge($errors, $assignment->getErrors());
        $failed_saving = false;
        if ($has_administered) {
            if (!$latest_element) {
                // create an automated one if no corresponding event
                $patient = \Patient::model()->findByPk($patient_id);
                $episode = $patient->getOrCreateEpisodeForFirm($firm);
                $da_creator = new \DrugAdministrationCreator($episode);
                $da_creator->setEntriesAndWorklistPatient($assignment, $worklist_patient_id, $firm->id);
                if (!$da_creator->save()) {
                    $failed_saving = true;
                    $errors = array_merge($errors, $da_creator->getErrors());
                } else {
                    $latest_element = $da_creator->elements['Element_DrugAdministration'];
                    Audit::add('PSD Assignment', 'created automated event', "Event: {$latest_element->event_id}");
                }
            } else {
                $latest_element->updateAssignmentList($assignment);
                if (!$latest_element->save()) {
                    $failed_saving = true;
                    $errors = array_merge($errors, $latest_element->getErrors());
                }
            }
        }
        if ($errors || $failed_saving) {
            $transaction->rollback();
        } else {
            if ($has_administered) {
                $audit_data = implode(', ', $audit_data);
                Audit::add('PSD Assignment', 'administration', "Assignment id: {$assignment_id}, Details: {$audit_data}, Event: {$latest_element->event_id}");
            }
            $transaction->commit();
        }
        $this->actionGetPathStep(0, $assignment_id, $patient_id, 0);
    }

    public function actionCheckPincode()
    {
        $pincode = \Yii::app()->request->getParam('pincode', null);
        $assignment_id = Yii::app()->request->getParam('assignment_id', null);
        $assignment = \OphDrPGDPSD_Assignment::model()->findByPk($assignment_id);
        $ret = array(
            'success' => 0,
            'payload' => null,
        );
        if (!$this->api) {
            $this->api = \Yii::app()->moduleAPI->get('OphDrPGDPSD');
        }
        $user_auth_objs = $this->api->getInstitutionUserAuth($pincode);
        if (!$user_auth_objs) {
            return $ret;
        }
        $users = array();
        foreach ($user_auth_objs as $user_auth) {
            $user_id = $user_auth->user_id;
            $users[$user_id] = $user_auth->user;
        }
        $users = array_values($users);

        if (count($users) !== 1) {
            return $ret;
        }
        $user = $users[0];
        if ($assignment && $user) {
            $user_roles = Yii::app()->user->getRole($user->id);
            $is_prescriber = in_array('Prescribe', array_values($user_roles));
            $is_med_admin = in_array('Med Administer', array_values($user_roles));
            if ($assignment->checkAuth($user) || $is_prescriber || $is_med_admin) {
                $ret['success'] = 1;
                $ret['payload'] = array(
                    'id' => $user->id,
                    'name' => $user->getFullName(),
                );
            }
        }
        $user = $ret['payload'] ? $ret['payload']['id'] : 'Not Found or Authorized';
        Audit::add('PSD Assignment', 'check pin', "Assignment id: {$assignment_id}, Accessed User: {$user}");
        $this->renderJSON($ret);
    }
}
