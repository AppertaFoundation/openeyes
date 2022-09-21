<?php

/**
 * Created by Mike Smith <mike.smith@camc-ltd.co.uk>.
 */

use OEModule\OphCiExamination\models\OphCiExamination_Event_ElementSet_Assignment;
use OEModule\OphCiExamination\models\OphCiExamination_ElementSet;

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
        } catch (Exception $e) {
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
        $this->createWidget($this->element->getWidgetClass(), array(
            'element' => $this->element,
            'data' => $request->getParam(CHtml::modelName($this->element)),
            'mode' => BaseEventElementWidget::$PATIENT_SUMMARY_MODE
        ));

        $this->validateAndCreateEvent();

        $this->redirect('/patient/view/'.$this->patient->id);
    }

    public function actionFindWorkflowSteps()
    {
        $firm_id = Yii::app()->request->getQuery('firm_id');
        if (!$firm_id) {
            $this->renderJSON([]);
        }
        $event = Event::model()->find('id = :id', [':id' => Yii::app()->request->getQuery('event_id')]);
        $workflow = \OEModule\OphCiExamination\models\OphCiExamination_Workflow_Rule::model()->findWorkflowCascading(
            $firm_id, $event->episode->status->id
        );
        // Have to do this manually as $this->renderJSON does not support implicit Yii object serialisation.
        header('Content-type: application/json');
        echo CJSON::encode(
            $workflow->active_steps ?? []
        );
    }

    function actionUpdateEpisode(){
        $outcome = 'false';
        $event_id = \Yii::app()->request->getPost('eventId');
        $patient_id = \Yii::app()->request->getPost('patientId');

        if ($event_id && $patient_id) {
            $event = \Event::model()->findByPk($event_id);
            $episode = \Episode::model()->findByPk($event->episode_id);
            $properties = array('patient_id' => $patient_id, 'episode_id' => $episode->id, 'event_id' => $event->id, 'event_type_id' => $event->event_type_id);

            if ($episode->patient_id === $patient_id) {
                $action = 'update';
                $firm_id = \Yii::app()->request->getPost('selected_firm_id');

                $data = 'Context changed, firm remains the same';
                if ($firm_id) {
                    if ($episode->firm_id !== $firm_id) {
                        $current_firm = \Firm::model()->findByPk($episode->firm_id);
                        $new_firm = \Firm::model()->findByPk($firm_id);

                        //try to find an existing firm for the patient
                        $episode = \Episode::model()->find('patient_id = ? AND firm_id = ?', [$episode->patient_id, $firm_id]);

                        if (!$episode) {
                            $episode = new \Episode;
                            $episode->patient_id = $patient_id;
                            $episode->start_date = date('Y-m-d H:i:s');
                            $episode->episode_status_id = \EpisodeStatus::model()->find('name = ?', ['New'])->id;
                        }

                        //set the new firm id
                        $episode->firm_id = $new_firm->id;

                        $action = 'change-firm';
                        $data = 'Changed from '.$current_firm->name.' to '.\Firm::model()->findByPk($firm_id)->name;
                    }
                    $episode->last_modified_user_id = Yii::app()->user->id;
                    $episode->last_modified_date = date('Y-m-d H:i:s');
                }

                $service_id = \Yii::app()->request->getPost('change_service');
                if ($service_id && $service_id !== $episode->firm_id) {
                    $current_service = \Firm::model()->findByPk($episode->firm_id);
                    $new_service = \Firm::model()->findByPk($service_id);

                    $episode = \Episode::model()->find('patient_id = ? AND firm_id = ?', [$episode->patient_id, $firm_id]);

                    if ($episode) {
                        $episode->firm_id = $new_service->id;
                        $action = 'change-service';
                        $data = 'Changed from ' . $current_service->name . ' to ' . $new_service->name;
                    }
                }


                if ($episode->save()) {
                    Audit::add('episode', $action, $data, null, $properties);

                    $selected_workflow_step_id =  \Yii::app()->request->getPost('selectedWorkflowStepId');

                    if ($selected_workflow_step_id) {
                        $assignment = OphCiExamination_Event_ElementSet_Assignment::model()->find('event_id = ?', array($event->id));

                        if (!$assignment) {
                            // Create initial workflow assignment if event hasn't already got one
                            $assignment = new OphCiExamination_Event_ElementSet_Assignment();
                            $assignment->event_id = $event->id;
                        }

                        $assignment->step_id = $selected_workflow_step_id;
                        $assignment->step_completed = 0;

                        if ($assignment->save()) {
                            $data = 'Changed step to ' . OphCiExamination_ElementSet::model()->findByPk($selected_workflow_step_id)->name;
                            Audit::add('element set assignment', 'update', $data, null, $properties);
                        }
                    }

                    $event->episode_id = $episode->id;
                    $event->last_modified_user_id = Yii::app()->user->id;
                    $event->last_modified_date = date('Y-m-d H:i:s');
                    $event->firm_id = \Yii::app()->request->getPost('selectedContextId');

                    if ($event->save()) {
                        $element = ElementLetter::model()->findByAttributes(["event_id" => $event->id]);
                        if ($element) {
                            $element->updateFooter($element, $episode);
                        }

                        Audit::add('event', 'update', $data = null, $log_message = null, $properties);
                        $outcome = 'true';
                    }
                }
            }
        }

        echo $outcome;
    }
}
