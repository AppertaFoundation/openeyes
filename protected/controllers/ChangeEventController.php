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

    public function checkCreateAccess()
    {
        $args = $this->getCreateArgsForEventTypeOprn($this->event_type, array());
        Yii::log(print_r($args, true));
        if (!call_user_func_array(array($this, 'checkAccess'), $args)) {
            throw new CHttpException(403, 'Permission denied for creating change event type of ' . get_class($this->event_type));
        }
    }

    protected function resolvePatient($request)
    {
        $patient_id = $request->getParam('patient_id', null);
        if (!$this->patient = Patient::model()->findByPk($patient_id)) {
            throw new CHttpException(404, 'Patient not found');
        }
    }

    protected function findOrCreateEpisode()
    {
        //TODO: this needs significant change as we don't really want to be using support services for this.
        if (!$episode = Episode::model()->findByAttributes(array(
            'patient_id' => $this->patient->id,
            'support_services' => true,
            ))) {
            $episode = new Episode();
            $episode->support_services = true;
            $episode->patient_id = $this->patient->id;
            $episode->save();
        }
        return $episode;
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
            $episode = $this->findOrCreateEpisode();
            $event = new Event();
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
        $this->resolveElementAndEventType($request);
        $this->resolvePatient($request);
        $this->checkCreateAccess();


        $widget = $this->createWidget($this->element->widgetClass, array(
            'element' => $this->element,
            'data' => $request->getParam(CHtml::modelName($this->element)),
            'mode' => BaseEventElementWidget::$PATIENT_SUMMARY_MODE
        ));

        $this->validateAndCreateEvent();

        $this->redirect('patient/view', array('id' => $this->patient->id));
    }
}