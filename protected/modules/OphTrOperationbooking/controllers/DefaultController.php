<?php
/**
 * OpenEyes.
 *
 * (C) OpenEyes Foundation, 2019
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2019, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
class DefaultController extends OphTrOperationbookingEventController
{
    protected static $action_types = array(
        'checkProcedureEUR' => self::ACTION_TYPE_FORM,
        'cancel' => self::ACTION_TYPE_EDIT,
        'admissionLetter' => self::ACTION_TYPE_PRINT,
        'admissionForm' => self::ACTION_TYPE_PRINT,
        'verifyProcedures' => self::ACTION_TYPE_CREATE,
        'putOnHold' => self::ACTION_TYPE_EDIT,
        'putOffHold' => self::ACTION_TYPE_EDIT,
        'getHighFlowCriteriaPopupContent' => self::ACTION_TYPE_FORM,
    );

    public $eventIssueCreate = 'Operation requires scheduling';
    protected $operation_required = false;
    /** @var Element_OphTrOperation_Operation $operation */
    protected $operation = null;
    protected $contact_details = null;

    protected $eur_res;
    protected $eur_answer_res;
    protected $operations;
    protected $procedure_readonly = false;

    protected $show_element_sidebar = false;

    /**
     * setup the various js scripts for this controller.
     *
     * @param CAction $action
     *
     * @return bool
     */
    protected function beforeAction($action)
    {
        if (!$this->isPrintAction($action->id)) {
            Yii::app()->clientScript->registerScriptFile($this->assetPath.'/js/booking.js');
            Yii::app()->assetManager->registerScriptFile('js/jquery.validate.min.js');
            Yii::app()->assetManager->registerScriptFile('js/additional-validators.js');

            //adding Anaestethic JS
            $url = Yii::app()->getAssetManager()->publish(Yii::getPathOfAlias('application.modules.OphTrOperationnote.assets.js'), true);
            Yii::app()->clientScript->registerScriptFile($url . '/OpenEyes.UI.OphTrOperationnote.Anaesthetic.js');
            Yii::app()->clientScript->registerScript(
                'AnaestheticController',
                'new OpenEyes.OphTrOperationnote.AnaestheticController({ typeSelector: \'#Element_OphTrOperationbooking_Operation_AnaestheticType\'});',
                CClientScript::POS_END
            );

            $this->jsVars['nhs_date_format'] = Helper::NHS_DATE_FORMAT_JS;
            $this->jsVars['op_booking_inc_time_high_complexity'] = SettingMetadata::model()->getSetting('op_booking_inc_time_high_complexity');
            $this->jsVars['op_booking_decrease_time_low_complexity'] = SettingMetadata::model()->getSetting('op_booking_decrease_time_low_complexity');
            $this->jsVars['procedure_readonly'] = $this->procedure_readonly;
        }

        $return = parent::beforeAction($action);

        if (!$this->isPrintAction($action->id)) {
            $this->jsVars['priority_canschedule'] = array();

            foreach (OphTrOperationbooking_Operation_Priority::model()->findAll() as $priority) {
                $this->jsVars['priority_canschedule'][$priority->id] = $this->checkScheduleAccess($priority);
            }
        }
        return $return;
    }

    public function afterUpdateElements($event)
    {
        parent::afterUpdateElements($event);

        if (\Yii::app()->request->getPost('schedule_now')) {
            $api = $this->getApp()->moduleAPI->get('OphTrOperationbooking');
            $api->setOperationStatus($event->id, "Scheduled");

            $this->successUri = '/OphTrOperationbooking/waitingList/index/';
        }
    }

    /**
     * @param Element_OphTrOperationbooking_Diagnosis $element
     * @param $action
     */
    protected function setElementDefaultOptions_Element_OphTrOperationbooking_Diagnosis($element, $action)
    {
        if ($action == 'create') {
            if ($this->episode && $this->episode->diagnosis) {
                // set default eye and disorder
                $element->eye_id = $this->episode->eye_id;
                $element->disorder_id = $this->episode->disorder_id;
            }
        }
    }

    /**
     * @param Element_OphTrOperationbooking_Operation $element
     * @param $action
     */
    protected function setElementDefaultOptions_Element_OphTrOperationbooking_Operation($element, $action)
    {
        if ($action == 'create') {
            if ($this->operations) {
                // set the default selected operations if there is any
                $element->procedures = $this->operations['selected_proc'];
                $element->total_duration = $this->operations['Element_OphTrOperationbooking_Operation']['total_duration_procs'];
            }
            // set the default eye
            if ($this->episode && $this->episode->diagnosis) {
                $element->eye_id = $this->episode->eye_id;
            }

            // set default anaesthetic based on whether patient is a child or not.
            $key = $this->patient->isChild() ? 'ophtroperationbooking_default_anaesthetic_child' : 'ophtroperationbooking_default_anaesthetic';

            if (isset(Yii::app()->params[$key])) {
                if ($at = AnaestheticType::model()->find('code=?', array(Yii::app()->params[$key]))) {
                    $element->anaesthetic_type = array($at);
                }
            }

            if ($default_referral = $this->calculateDefaultReferral()) {
                $element->referral_id = $default_referral->id;
            }

            $element->site_id = Yii::app()->session['selected_site_id'];
        }
    }

    /**
     * Sets up operation based on the event.
     *
     * @param $id
     *
     * @throws CHttpException
     *                        (non-phpdoc)
     *
     * @see BaseEventTypeController::initWithEventId($id)
     */
    protected function initWithEventId($id)
    {
        parent::initWithEventId($id);

        $this->operation = Element_OphTrOperationbooking_Operation::model()->find('event_id=?', array($this->event->id));

        $this->eur_res = EUREventResults::model()->find('event_id=?', array($this->event->id));
        $this->eur_answer_res = $this->eur_res ? $this->eur_res->eurAnswerResults : null;

        $this->contact_details = Element_OphTrOperationbooking_ContactDetails::model()->find('event_id=?', array($this->event->id));
        if ($this->operation_required && !$this->operation) {
            if (!$this->eur_res->id) {
                throw new CHttpException(500, 'Operation not found');
            }
        }
    }

    /**
     * Sets up some JS vars for the procedure confirmation checking.
     */
    protected function initActionEdit()
    {
        $this->jsVars['OE_confirmProcedures'] = Yii::app()->params['OphTrOperationbooking_duplicate_proc_warn'];
        $this->jsVars['OE_patientId'] = $this->patient->id;
    }

    /**
     * get number of existing examination events
     *
     */
    public function getExaminationEventCount()
    {
        $event_type = EventType::model()->find('name = "Examination"');

        $criteria = new CDbCriteria();
        $criteria->join = "JOIN episode ON t.episode_id = episode.id";
        $criteria->compare('event_type_id', $event_type->id);
        $criteria->compare('episode.patient_id', $this->patient->id);

        return Event::model()->count($criteria);
    }

     /**
     * add the number of existing examination events to JS
     *
     */
    public function actionCreate()
    {
        $cancel_url = \Yii::app()->createURL("/patient/summary/", array("id" => $this->patient->id));
        $create_examination_url = Yii::app()->createAbsoluteUrl('site/index').'OphCiExamination/Default/create?patient_id=' . $this->patient->id;

        $this->jsVars['examination_events_count'] = $this->getExaminationEventCount();
        $this->jsVars['cancel_url'] = $cancel_url;
        $this->jsVars['create_examination_url'] = $create_examination_url;

        $require_exam_before_booking = SettingMetadata::model()->findByAttributes(array('key' => 'require_exam_before_booking'))->getSettingName();
        $this->jsVars['require_exam_before_booking'] = strtolower($require_exam_before_booking) == 'on';
        // find if there is any procedure needs EUR form
        $proc_need_eur = ProcedureSubspecialtyAssignment::model()->count('need_eur = 1') > 0;
        // get the System setting for EUR
        $enable_eur = SettingMetadata::model()->getSetting('cataract_eur_switch');
        // the EUR form will be displayed only if the operation booking is under cataract, at least one procedure needs EUR and the EUR System setting is turned on
        if (strtolower($this->firm->getSubspecialtyText()) === 'cataract' && $proc_need_eur && $enable_eur === 'on') {
            $this->procedure_readonly = true;
            $this->jsVars['procedure_readonly'] = $this->procedure_readonly;
            $this->getEURForm();
        } else {
            parent::actionCreate();
        }
    }

    /**
     * check if the procedure needs EUR form or not
     */
    public function actionCheckProcedureEUR()
    {
        $subspecialty_id = $this->firm->getSubspecialtyID();
        $selected_proc = $_GET['procedure_id'];
        $procedure_list = ProcedureSubspecialtyAssignment::model()->getEURProcedureListFromSubspecialty($subspecialty_id);
        if ($procedure_list[$selected_proc] == 1) {
            $this->renderJSON(true);
        }
        $this->renderJSON(false);
    }

    /**
     * render eur form
     * after submitting the form, if the patient is suitable for cataract surgery, proceed to booking form with eur form result
     * save the form if the patient is NOT suitable for cataract surgery
     */
    protected function getEURForm()
    {
        // new op-booking event
        if (empty($_POST)) {
            $this->render('form_eur');
            return;
        }
        if (isset($_POST['eur_result'])) {
            $this->eur_res = new EUREventResults();
            $this->eur_res->eye_num = $_POST['eye'];
            $this->eur_res->result = $_POST['eur_result'];
            $answerResults = array();
            foreach ($_POST['EUREventResult']['eurAnswerResults'] as $p) {
                if (isset($p['answer_id'])) {
                    $answerResult = new EURAnswerResults();
                    $answerResult->question_id = $p['question_id'];
                    $answerResult->answer_id = $p['answer_id'];
                    $answerResult->eye_num = $_POST['eye'];
                    $answerResults[] = $answerResult;
                }
            }
            $this->eur_answer_res = $answerResults;
            $this->eur_res->eurAnswerResults = $this->eur_answer_res;
        }
        // 'Next' button is clicked in form_eur
        if (isset($_POST['next'])) {
            foreach ($_POST['Procedures_procs'] as $proc) {
                $this->operations['selected_proc'][] = Procedure::model()->findByPK($proc);
            }
            $this->operations['Element_OphTrOperationbooking_Operation'] = $_POST['Element_OphTrOperationbooking_Operation'];
            // from EUR form
            if (isset($_POST['eur_result'])) {
                // has phaco procedure
                if ($_POST['eur_result'] == 1) {
                    // pass eur, redirect to booking form
                    $_POST = array();
                    parent::actionCreate();
                } else {
                    // failed eur, save eur
                    $transaction = Yii::app()->db->beginTransaction();
                    try {
                        $success = $this->saveEURForm();
                        if ($success) {
                            $this->event->addIssue('EUR Failed');
                            $transaction->commit();
                            $this->redirect(array($this->successUri . $this->event->id));
                        } else {
                            throw new Exception('could not save event');
                        }
                    } catch (Exception $e) {
                        $transaction->rollback();
                        throw $e;
                    }
                }
            } else {
                // no phaco procedure
                $_POST = array();
                parent::actionCreate();
            }
        } elseif (isset($_POST['#']) || (isset($_POST['schedule_now']) && $_POST['schedule_now'])) {
            // from create.php
            parent::actionCreate();
        }
    }

    /**
     * save the EUR form
     */
    protected function saveEURForm()
    {
        if ($this->event->isNewRecord) {
            if (!$this->event->save()) {
                OELog::log("Failed to create new event for episode_id={$this->episode->id}, event_type_id=" . $this->event_type->id);
                throw new Exception('Unable to save event.');
            }
            $this->logActivity('created event.');
            $this->event->audit('event', 'create', 'Created Event from EUR form');
        }

        $this->eur_res = new EUREventResults();
        $this->eur_res->event_id = $this->event->id;
        $this->eur_res->eye_num = $_POST['eye'];
        $this->eur_res->eye_side = @$_POST['Element_OphTrOperationbooking_Operation']['eye_id'];
        $this->eur_res->result = $_POST['eur_result'];
        $eur_result = $this->eur_res->result == 1 ? 'Passed' : 'Failed';
        if (!$this->eur_res->save()) {
            OELog::log("Failed to create new EUR Event result for episode_id={$this->episode->id}, event_type_id=" . $this->event_type->id);
            throw new Exception('Unable to save EUR Event result.');
        }
        foreach ($_POST['EUREventResult']['eurAnswerResults'] as $p) {
            if (isset($p['answer_id'])) {
                $answerResult = new EURAnswerResults();
                $answerResult->question_id = $p['question_id'];
                $answerResult->answer_id = $p['answer_id'];
                $answerResult->eye_num = $_POST['eye'];
                $answerResult->eye_side = $this->eur_res->eye_side;
                $answerResult->res_id = $this->eur_res->id;
                if (!$answerResult->save()) {
                    OELog::log("Failed to create new EUR Answer result for episode_id={$this->episode->id}, event_type_id=" . $this->event_type->id);
                    throw new Exception('Unable to save EUR Answer result.');
                }
            }
        }
        $this->logActivity("created eur $eur_result form.");
        $this->event->audit("eur $eur_result form", "create", "Saved $eur_result EUR Form");
        return true;
    }
    /**
     * Checks whether schedule now has been requested.
     *
     * (non-phpdoc)
     *
     * @see BaseEventTypeController::initActionCreate()
     */
    protected function initActionCreate()
    {
        parent::initActionCreate();
        $this->initActionEdit();

        if (isset($_POST['schedule_now']) && $_POST['schedule_now']) {
            $this->successUri = 'booking/schedule/';
        }
    }

    /**
     * Call to edit init.
     *
     * (non-phpdoc)
     *
     * @see BaseEventTypeController::initActionUpdate()
     */
    protected function initActionUpdate()
    {
        parent::initActionUpdate();
        $this->initActionEdit();
        $current_subspecialty = $this->event->firm ? $this->event->firm->serviceSubspecialtyAssignment->subspecialty->name : '';
        if (strtolower($current_subspecialty) === 'cataract') {
            $this->procedure_readonly = true;
        }
        $this->jsVars['procedure_readonly'] = $this->procedure_readonly;
    }
    /**
     * Make the operation element directly available for templates.
     *
     * @see BaseEventTypeController::initActionView()
     */
    public function initActionView()
    {
        $this->operation_required = true;
        parent::initActionView();
        if (!$this->operation) {
            $this->editable = false;
        }
        $this->extraViewProperties = array(
            'operation' => $this->operation,
            'eur' => $this->eur_res,
        );
    }

    /**
     * Make sure the EUR is displayed properly in lightning viewer
     * @param $id: event id
     */
    public function actionRenderEventImage($id)
    {
        $eur = EUREventResults::model()->findByAttributes(array('event_id' => $id));
        if ($eur) {
            $this->extraViewProperties = array('eur' => $eur);
        }
        parent::actionRenderEventImage($id);
    }
    /**
     * Handle procedures.
     *
     * @see BaseEventTypeController::setElementComplexAttributesFromData($element, $data, $index)
     */
    protected function setComplexAttributes_Element_OphTrOperationbooking_Operation($element, $data, $index = null)
    {
        // Using the ProcedureSelection widget, so the field doesn't map directly to the element attribute
        if (isset($data['Element_OphTrOperationbooking_Operation']['total_duration_procs'])) {
            $element->total_duration = $data['Element_OphTrOperationbooking_Operation']['total_duration_procs'];
        }
        $procs = array();
        if (isset($data['Procedures_procs'])) {
            foreach ($data['Procedures_procs'] as $proc_id) {
                $procs[] = Procedure::model()->findByPk($proc_id);
            }
        }
        $element->procedures = $procs;

        //AnaestheticType
        $type_assessments = array();
        if (isset($data['AnaestheticType']) && is_array($data['AnaestheticType'])) {
            $type_assessments_by_id = array();
            foreach ($element->anaesthetic_type_assignments as $type_assignments) {
                $type_assessments_by_id[$type_assignments->anaesthetic_type_id] = $type_assignments;
            }

            foreach ($data['AnaestheticType'] as $anaesthetic_type_id) {
                if ( !array_key_exists($anaesthetic_type_id, $type_assessments_by_id) ) {
                    $anaesthetic_type_assesment = new \OphTrOperationbooking_AnaestheticAnaestheticType();
                } else {
                    $anaesthetic_type_assesment = $type_assessments_by_id[$anaesthetic_type_id];
                }

                $anaesthetic_type_assesment->et_ophtroperationbooking_operation_id = $element->id;
                $anaesthetic_type_assesment->anaesthetic_type_id = $anaesthetic_type_id;

                $type_assessments[] = $anaesthetic_type_assesment;
            }
        }

        $element->anaesthetic_type_assignments = $type_assessments;
    }

    /**
     * Handle the patient unavailables.
     *
     * @see BaseEventTypeController::setElementComplexAttributesFromData($element, $data, $index)
     */
    protected function setComplexAttributes_Element_OphTrOperationbooking_ScheduleOperation($element, $data, $index)
    {
        if (isset($data['Element_OphTrOperationbooking_ScheduleOperation']['patient_unavailables'])) {
            $puns = array();
            foreach ($data['Element_OphTrOperationbooking_ScheduleOperation']['patient_unavailables'] as $i => $attributes) {
                if ($id = @$attributes['id']) {
                    $pun = OphTrOperationbooking_ScheduleOperation_PatientUnavailable::model()->findByPk($id);
                } else {
                    $pun = new OphTrOperationbooking_ScheduleOperation_PatientUnavailable();
                }
                $pun->attributes = Helper::convertNHS2MySQL($attributes);
                $puns[] = $pun;
            }
            $element->patient_unavailables = $puns;
        }
    }

    /**
     * Set procedures for Element_OphTrOperationbooking_Operation.
     *
     * @param $element
     * @param $data
     * @param $index
     */
    protected function saveComplexAttributes_Element_OphTrOperationbooking_Operation($element, $data, $index)
    {
        // using the ProcedureSelection widget, so not a direct field on the operation element
        $element->updateProcedures(isset($data['Procedures_procs']) ? $data['Procedures_procs'] : array());
        $element->updateAnaestheticType(isset($data['AnaestheticType']) ? $data['AnaestheticType'] : array());
    }

    /**
     * Set the patient unavailable periods for Element_OphTrOperationbooking_ScheduleOperation.
     *
     * @param $element
     * @param $data
     * @param $index
     */
    protected function saveComplexAttributes_Element_OphTrOperationbooking_ScheduleOperation($element, $data, $index)
    {
        // using the ProcedureSelection widget, so not a direct field on the operation element
        $element->updatePatientUnavailables(isset($data['Element_OphTrOperationbooking_ScheduleOperation']['patient_unavailables']) ?
                Helper::convertNHS2MySQL($data['Element_OphTrOperationbooking_ScheduleOperation']['patient_unavailables']) : array());
    }

    /**
     * Extend standard behaviour to perform validation of elements across the event.
     *
     * @param array $data
     *
     * @return array
     */
    protected function setAndValidateElementsFromData($data)
    {
        $errors = parent::setAndValidateElementsFromData($data);
        // need to do some validation at the event level

        $event_errors = OphTrOperationbooking_BookingHelper::validateElementsForEvent($this->open_elements);
        if ($event_errors) {
            if (@$errors['Event']) {
                $errors['Event'] = array_merge($errors['Event'], $event_errors);
            } else {
                $errors['Event'] = $event_errors;
            }
        }

        $operation_element = null;
        foreach ($this->open_elements as $element) {
            if (get_class($element) == 'Element_OphTrOperationbooking_Operation') {
                $operation_element = $element;
            }
        }

        if ($operation_element && $operation_element->booking) {
            $anaesthetic_type_ids = isset($data['AnaestheticType']) ? $data['AnaestheticType'] : [];
            foreach ($anaesthetic_type_ids as $anaesthetic_type_id) {
                $anaesthetic = AnaestheticType::model()->findByPk($anaesthetic_type_id);
                if ($anaesthetic) {
                    if (in_array($anaesthetic->id, $operation_element->anaesthetist_required_ids) && !$operation_element->booking->session->anaesthetist) {
                        $errors['Operation']['Anaesthetist'] = 'The booked session does not have an anaesthetist present, you must change the session or cancel the booking before making this change';
                    }
                    if ($anaesthetic->code == 'GA' && !$operation_element->booking->session->general_anaesthetic) {
                        $errors['Operation']['GeneralAnaesthetist'] = 'General anaesthetic is not available for the booked session, you must change the session or cancel the booking before making this change';
                    }
                }
            }
        }
        return $errors;
    }

    /**
     * Calculate the default referral for the event.
     *
     * @return null|Referral
     */
    public function calculateDefaultReferral()
    {
        $referrals = $this->getReferralChoices();
        $match = null;
        foreach ($referrals as $referral) {
            if ($referral->firm_id == $this->firm->id) {
                return $referral;
            } else {
                if (!$match && $referral->service_subspecialty_assignment_id == $this->firm->service_subspecialty_assignment_id) {
                    $match = $referral;
                }
            }
        }
        if (!$match && !empty($referrals)) {
            $match = $referrals[0];
        }

        return $match;
    }

    /**
     * Setup event properties.
     */
    protected function initActionCancel()
    {
        $this->operation_required = true;
        $this->initWithEventId(@$_GET['id']);
    }

    /**
     * AJAX method to check for any duplicate procedure bookings.
     */
    public function actionVerifyProcedures()
    {
        $this->setPatient($_REQUEST['patient_id']);

        $resp = array(
                'previousProcedures' => false,
        );

        $procs = array();
        $procs_by_id = array();

        if (isset($_POST['Procedures_procs'])) {
            foreach ($_POST['Procedures_procs'] as $proc_id) {
                if ($p = Procedure::model()->findByPk((int) $proc_id)) {
                    $procs[] = $p;
                    $procs_by_id[$p->id] = $p;
                }
            }
        }

        $eye = Eye::model()->findByPk((int) @$_POST['Element_OphTrOperationbooking_Operation']['eye_id']);

        if ($eye && count($procs)) {
            $matched_procedures = array();
            // get all the operation elements for this patient from booking events that have not been cancelled
            if (Yii::app()->params['OphTrOperationbooking_duplicate_proc_warn_all_eps']) {
                $episodes = $this->patient->episodes;
            } else {
                $episodes = array($this->getEpisode());
            }
            foreach ($episodes as $ep) {
                $events = $ep->getAllEventsByType($this->event_type->id);
                foreach ($events as $ev) {
                    if ($ev->id == @$_POST['event_id']) {
                        // if we're editing, then don't want to check against that event
                        continue;
                    }
                    $eur = EUREventResults::model()->findByAttributes(array('event_id' => $ev->id));
                    if (isset($eur) && $eur->result == 0) {
                        continue;
                    }
                    $op = Element_OphTrOperationbooking_Operation::model()->findByAttributes(array('event_id' => $ev->id));

                    // check operation still valid, and that it is for a matching eye.
                    if (!$op->operation_cancellation_date &&
                            ($op->eye_id == Eye::BOTH || $eye->id == Eye::BOTH || $op->eye_id == $eye->id)) {
                        foreach ($op->procedures as $existing_proc) {
                            if (in_array($existing_proc->id, array_keys($procs_by_id))) {
                                if (!isset($matched_procedures[$existing_proc->id])) {
                                    $matched_procedures[$existing_proc->id] = array();
                                }
                                $matched_procedures[$existing_proc->id][] = $op;
                            }
                        }
                    }
                }
            }

            // if procedure matches
            if (count($matched_procedures)) {
                $resp['previousProcedures'] = true;
                $resp['message'] = $this->renderPartial('previous_procedures', array(
                    'matched_procedures' => $matched_procedures,
                    'eye' => $eye,
                    'procs_by_id' => $procs_by_id,
                ), true);
            }
        }

        echo \CJSON::encode($resp);
    }

    /**
     * Cancel operation action.
     *
     * @param $id
     *
     * @throws CHttpException
     * @throws Exception
     */
    public function actionCancel($id)
    {
        $operation = $this->operation;

        if ($operation->status->name == 'Cancelled') {
            return $this->redirect(array('default/view/'.$this->event->id));
        }

        $errors = array();

        if (isset($_POST['cancellation_reason']) && isset($_POST['operation_id'])) {
            $comment = (isset($_POST['cancellation_comment'])) ? strip_tags(@$_POST['cancellation_comment']) : '';
            $result = $operation->cancel(@$_POST['cancellation_reason'], $comment);

            if ($result['result']) {
                $operation->event->deleteIssues();

                $operation->event->audit('event', 'cancel');

                die(json_encode(array()));
            }

            foreach ($result['errors'] as $form_errors) {
                foreach ($form_errors as $error) {
                    $errors[] = $error;
                }
            }

            die(json_encode($errors));
        }

        if (!$operation = Element_OphTrOperationbooking_Operation::model()->find('event_id=?', array($id))) {
            throw new CHttpException(500, 'Operation not found');
        }

        $this->patient = $operation->event->episode->patient;
        $this->title = 'Cancel operation';

        $this->processJsVars();

        $this->render('cancel', array(
            'operation' => $operation,
            'patient' => $operation->event->episode->patient,
            'date' => $operation->minDate,
            'errors' => $errors,
        ));
    }

    /**
     * Setup event properties.
     */
    protected function initActionAdmissionLetter()
    {
        $this->operation_required = true;
        $this->initWithEventId(@$_GET['id']);
    }

    /**
     * Generate admission letter for operation booking.
     *
     * @return bool
     *
     * @throws Exception
     */
    public function actionAdmissionLetter()
    {
        $this->layout = '//layouts/print';

        if ($this->patient->isDeceased()) {
            // no admission for dead patients
            return false;
        }

        $site = $this->operation->booking->session->theatre->site;
        if (!$firm = $this->operation->booking->session->firm) {
            $firm = $this->operation->event->episode->firm;
            $emergency_list = true;
        }
        $emergency_list = false;

        $this->event->audit('admission letter', 'print', false);

        $this->logActivity('printed admission letter');

        $this->pdf_print_suffix = 'admission_letter';
        $this->pdf_print_html = $this->render('../letters/admission_letter', array(
            'site' => $site,
            'patient' => $this->event->episode->patient,
            'firm' => $firm,
            'emergencyList' => $emergency_list,
            'operation' => $this->operation,
            'to_address' => $this->event->episode->patient->getLetterAddress(array(
                'include_name' => true,
                'delimiter' => "\n",
            )),
            'from_address' => $site->getLetterAddress(array(
                'include_name' => true,
                'include_telephone' => true,
                'include_fax' => true,
                'delimiter' => "\n",
            )),
        ), true);

        return $this->actionPDFPrint($this->operation->event->id);
    }

    protected function initActionAdmissionForm()
    {
        $this->operation_required = true;
        $this->initWithEventId(@$_GET['id']);
    }

    public function actionAdmissionForm()
    {
        $this->layout = '//layouts/print';

        $this->pdf_print_suffix = 'admission_form';
        $this->pdf_print_html = $this->render(
            '../letters/admission_form',
            array(
                'operation' => $this->operation,
                'site' => $this->operation->site,
                'patient' => $this->patient,
                'firm' => $this->episode->firm,
                'emergencyList' => false,
                'contact_details' => $this->contact_details
            ),
            true
        );

        return $this->actionPDFPrint($this->operation->event->id);
    }

    public function actionDelete($id)
    {
        if ($this->operation) {
            $operation = $this->operation;
            if ($operation->status->name === 'Cancelled') {
                parent::actionDelete($id);
            } else {
                Yii::app()->user->setFlash('error.error', "Please cancel this operation before deleting it.");
                return $this->redirect(array('default/view/'.$this->event->id));
            }
        } elseif ($this->eur_res) {
            parent::actionDelete($id);
        }
    }

    /**
     * initialise the controller with the event id.
     */
    protected function initActionPutOnHold()
    {
        $event_id = isset($_GET['id']) ? $_GET['id'] : null;
        $this->initWithEventId($event_id);
    }

    public function actionPutOnHold()
    {
        if (isset($_POST['et_cancel_put_on_hold'])) {
            return $this->redirect(array('/' . $this->event_type->class_name . '/default/view/' . $this->event->id));
        }

        $on_hold_reason = isset($_POST['on_hold_reason']) ? $_POST['on_hold_reason'] : null;
        $on_hold_comments = isset($_POST['on_hold_comments']) && trim($_POST['on_hold_comments']) ? $_POST['on_hold_comments'] : null;
        $on_hold_other_reason = isset($_POST['other_reason']) && trim($_POST['other_reason']) ? $_POST['other_reason'] : null;

        if ($on_hold_reason !== null || $on_hold_reason === 'Other' && $on_hold_other_reason !== null) {
            if ($on_hold_reason === 'Other') {
                $this->operation->on_hold_reason = $on_hold_other_reason;
            } else {
                $this->operation->on_hold_reason = $on_hold_reason;
            }
            if (trim($on_hold_comments) === "") {
                $this->operation->on_hold_comment = null;
            } else {
                $this->operation->on_hold_comment = $on_hold_comments;
            }
            $this->operation->status_id = OphTrOperationbooking_Operation_Status::model()->find('name = "On-Hold"')->id;
            $this->operation->event->deleteIssue('Operation requires scheduling');
            $this->operation->save();
        }
        return $this->redirect(array('default/view/' . $this->event->id));
    }

    protected function initActionPutOffHold()
    {
        $event_id = isset($_GET['id']) ? $_GET['id'] : null;
        $this->initWithEventId($event_id);
    }

    public function actionPutOffHold()
    {
        $this->operation->status_id = OphTrOperationbooking_Operation_Status::model()->find('name = "Requires rescheduling"')->id;
        $this->operation->on_hold_reason = null;
        $this->operation->on_hold_comment = null;
        $this->operation->save();
        return $this->redirect(array('default/view/' . $this->event->id));
    }


    protected function getEventElements()
    {
        if ($this->action->id == 'view') {
            return parent::getEventElements();
        }

        $elements = parent::getEventElements();
        foreach ($elements as $key => $element) {
            if (get_class($element) == 'Element_OphTrOperationbooking_PreAssessment') {
                if (!$element->hasTypes()) {
                    unset($elements[$key]);
                }
            }
        }
        return $elements;
    }

    /**
     * Get all the available element types for the event
     *
     * @return array
     */
    public function getAllElementTypes()
    {
        $preassessment_element = new Element_OphTrOperationbooking_PreAssessment();
        $remove = !$preassessment_element->hasTypes() ? ['Element_OphTrOperationbooking_PreAssessment'] : [];
        return array_filter(
            parent::getAllElementTypes(),
            function ($et) use ($remove) {
                return !in_array($et->class_name, $remove);
            }
        );
    }

    public function actionGetHighFlowCriteriaPopupContent()
    {

        $procedure_ids = array_map('intval', explode(',', Yii::app()->request->getParam('id')));
        $criteria = new \CDbCriteria();
        $criteria->addCondition('low_complexity_criteria IS NOT NULL');
        $criteria->addInCondition("id", $procedure_ids);

        $procedures = Procedure::model()->findAll($criteria);
        if (count($procedures)===0) {
            $response = null;
        } else {
            $response = [
                'html' => $this->renderPartial(
                    '_meh_high_flow',
                    array(
                        'procedures' => $procedures,
                    ),
                    true
                ),
            ];
        }

        $this->renderJSON($response);
    }
}
