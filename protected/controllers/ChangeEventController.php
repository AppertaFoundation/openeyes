<?php

/**
 * Created by Mike Smith <mike.smith@camc-ltd.co.uk>.
 */
class ChangeEventController extends BaseController
{
    /**
     * @var Patient
     */
    public $patient;
    /**
     * @var EventType
     */
    protected $event_type;
    /**
     * @var ElementType
     */
    protected $element_type;
    /**
     * @var BaseEventTypeElement
     */
    protected $element;

    /**
     * We are going to rely on this being null initially to prevent permission checking by firm/episode
     * @var null
     */
    public $firm;

    /**
     * @var Episode
     */
    protected $currentEpisode;

    public function behaviors()
    {
        return array(
            'CreateEventBehavior' => array(
                'class' => 'application.behaviors.CreateEventControllerBehavior',
            ),
        );
    }

    public function accessRules()
    {
        // Allow logged in users - the main authorisation check happens from the action methods
        return array(array('allow', 'users' => array('@')));
    }

    protected function resolveElementAndEventType($request)
    {
        $this->element_type = ElementType::model()
            ->with('event_type')
            ->findByPk($request->getParam('element_type_id', null));

        if (!$this->element_type) {
            throw new CHttpException(404, 'Unrecognised element');
        }

        $this->event_type = $this->element_type->event_type;
        $this->element = $this->element_type->getInstance();
    }

    /**
     * @throws CHttpException
     */
    public function checkCreateAccess()
    {
        // Change events are only necessary for editing from the patient summary screen, and at initial implementation
        // this has been disabled. The code has been kept in place though to enable it to be easily enabled
        // at a later stage if necessary.
        if (!$this->getApp()->params['allow_patient_summary_clinic_changes']) {
            return false;
        }

        $args = $this->getCreateArgsForEventTypeOprn($this->event_type);
        if (!call_user_func_array(array($this, 'checkAccess'), $args)) {
            throw new CHttpException(403, 'Permission denied for creating change event type of ' . get_class($this->event_type));
        }
    }

    /**
     * @param $request
     * @throws CHttpException
     */
    protected function resolvePatient($request)
    {
        $patient_id = $request->getParam('patient_id', null);
        if (!$this->patient = Patient::model()->findByPk($patient_id)) {
            throw new CHttpException(404, 'Patient not found');
        }
    }

    /**
     * @return CActiveRecord|Episode
     */
    protected function getCurrent_episode()
    {
        if (!$this->currentEpisode) {
            $this->currentEpisode = Episode::getChangeEpisode($this->patient);
        }

        return $this->currentEpisode;
    }

    /**
     * Sets the firm property on the controller from the session.
     * @TODO: consolidate with duplication in BaseModuleController
     * @throws HttpException
     */
    protected function setFirmFromSession()
    {
        if (!$firm_id = $this->app->session->get('selected_firm_id')) {
            throw new HttpException('Firm not selected');
        }
        if (!$this->firm || $this->firm->id != $firm_id) {
            $this->firm = Firm::model()->findByPk($firm_id);
        }
    }

    /**
     * Validates the element and then creates the change event for it.
     *
     * @throws CHttpException
     * @throws Exception
     */
    protected function validateAndCreateEvent()
    {
        if (!$this->element->validate()) {
            Yii::log(print_r($this->element->getErrors(), true));
            throw new CHttpException(404, 'Invalid request');
        }
        $transaction = $this->app->db->beginTransaction();
        try {
            $episode = $this->current_episode;
            if ($episode->isNewRecord) {
                // The first change event for this patient
                $episode->save();
            }
            $event = new Event();
            // standard events only store the date not the time, so we override the current default
            // to just use the date for the event, and not the time.
            $event->event_date = substr($event->event_date, 0, 10) . ' 00:00:00';
            $event->event_type_id = $this->event_type->id;
            $event->episode_id = $episode->id;
            $event->save();

            $this->element->event_id = $event->id;
            $this->element->save();

            $this->logActivity('created event.');

            $event->audit('event', 'create');

            $this->app->user->setFlash('success', "{$this->element_type->name} updated.");

            $transaction->commit();
        }
        catch (Exception $e) {
            $transaction->rollback();
            throw $e;
        }
    }

    public function actionSave()
    {
        $request = $this->getApp()->request;

        $this->setFirmFromSession();
        $this->resolveElementAndEventType($request);
        $this->resolvePatient($request);
        $this->checkCreateAccess();

        // the widget will initialise the values correctly on the element.
        $this->createWidget($this->element->widgetClass, array(
            'element' => $this->element,
            'data' => $request->getParam(CHtml::modelName($this->element)),
            'mode' => BaseEventElementWidget::$PATIENT_SUMMARY_MODE
        ));

        $this->validateAndCreateEvent();

        $this->redirect('/patient/view/'.$this->patient->id);
    }

    function actionUpdateEpisode(){
        $outcome = 'false';
        if($_POST['eventId'] && $_POST['patientId']){
            $event = \Event::model()->findByPk($_POST['eventId']);
            $episode = \Episode::model()->findByPk($event->episode_id);
            $properties = array('patient_id' => $_POST['patientId'], 'episode_id' => $episode->id, 'event_id' => $event->id, 'event_type_id' => $event->event_type_id);

            if($episode->patient_id == $_POST['patientId']){
                $action = 'update';
                if(isset($_POST['selectedSubspecialtyId']) && $_POST['selectedSubspecialtyId']){
                    if($episode->firm_id != $_POST['selectedSubspecialtyId']){
                        $episode = new \Episode;
                        $episode->patient_id = $_POST['patientId'];
                        $episode->start_date = date('Y-m-d H:i:s');
                        $action = 'change-firm';
                    }
                    $episode->firm_id = $_POST['selectedSubspecialtyId'];
                    $episode->last_modified_user_id = Yii::app()->user->id;
                    $episode->last_modified_date = date('Y-m-d H:i:s');                    
                }

                if($episode->save()) {
                    Audit::add('episode', $action, $data = null, $log_message = null, $properties);

                    if(isset($_POST['selectedWorkflowStepId']) && $_POST['selectedWorkflowStepId']){
                        $step = \OEModule\OphCiExamination\models\OphCiExamination_Event_ElementSet_Assignment::model()->find('event_id = ?', array($event->id));
                        $step->step_id = $_POST['selectedWorkflowStepId'];

                        if($step->save()) {
                            Audit::add('element set assignment', 'update', $data = null, $log_message = null, $properties);
                        }                        
                    }

                    $event->episode_id = $episode->id;
                    $event->last_modified_user_id = Yii::app()->user->id;
                    $event->last_modified_date = date('Y-m-d H:i:s');
                    $event->firm_id = $_POST['selectedContextId'];

                    if($event->save()) {
                        Audit::add('event', 'update', $data = null, $log_message = null, $properties);
                        $outcome = 'true';
                    }
                }
            }            
        }

        echo $outcome;
    }
}