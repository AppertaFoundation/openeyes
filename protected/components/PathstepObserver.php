<?php

class PathstepObserver
{
    /**
     * Observer method for bootstrapping a new event.
     * This is mainly used for creating a new step when a Drug Administration
     * element or event has been added to a patient.
     * @param $params
     * @throws Exception
     */
    public function createExternalStep($params)
    {
        /**
         * @var $step_type PathwayStepType
         */
        $step_type = $params['step_type'];
        $worklist_patient_id = $params['worklist_patient_id'];
        $raise_event = $params['raise_event'] ?? true;
        /**
         * @var WorklistPatient $worklist_patient
         */
        $worklist_patient = WorklistPatient::model()->findByPk($worklist_patient_id);

        $step_type->createNewStepForPathway($worklist_patient->pathway->id, $params['initial_state'], $raise_event);
    }

    /**
     * @param EventType $event_type
     * @param int $patient_id
     * @param bool $include_active_steps
     * @param WorklistPatient $event_wp
     * @return PathwayStep|null
     * @throws JsonException
     */
    protected function getUnassociatedStepForEvent(
        EventType $event_type,
        int $patient_id,
        bool $include_active_steps = false,
        WorklistPatient $worklist_patient = null
    ): ?PathwayStep {
        $criteria = new CDbCriteria();
        if (!$worklist_patient) {
            // Assuming the latest date maps to the active worklist/visit for the patient.
            $latest_date = Yii::app()->db->createCommand()
                ->select('MAX(`when`)')
                ->from('worklist_patient p')
                ->where('p.patient_id = :id', [':id' => $patient_id])
                ->queryScalar();

            $worklist_patient = WorklistPatient::model()->find(
                'patient_id = :id AND `when` = :when',
                [':id' => $patient_id, ':when' => $latest_date]
            );
        }

        $status_list = $include_active_steps ? '0, 1' : '0';

        /**
         * @var $worklist_patient WorklistPatient
         */
        if ($worklist_patient) {
            if (!$worklist_patient->pathway) {
                $worklist_patient->worklist->worklist_definition->pathway_type->instancePathway($worklist_patient);
                $worklist_patient->refresh();
            }
            $criteria->compare('pathway_id', $worklist_patient->pathway->id);
            $criteria->addCondition("status IS NULL OR status IN ($status_list)");
            $criteria->addCondition('JSON_CONTAINS(state_data, \'"'.$event_type->class_name.'"\', \'$.event_type\')');
            $criteria->order = 'status DESC, queue_order ASC, todo_order ASC';
            $criteria->limit = 1; // We only want the first result.

            return PathwayStep::model()->find($criteria);
        }
        return null;
    }

    /**
     * Observer method for bootstrapping step status.
     * This is mainly used for events saved with an in-progress state eg. Draft.
     * @param $params
     * @throws Exception
     */
    public function startStep($params): void
    {
        if (isset($params['event'])) {
            $step = PathwayStep::model()->findByPk($params['event']->step_id);

            if (!$step) {
                // Event is not linked to a step but should still update the first relevant step.
                $step = $this->getUnassociatedStepForEvent($params['event_type'], $params['patient_id'], false, $params['event_type']->worklist_patient);

                if ($step) {
                    $params['event']->worklist_patient_id = $step->pathway->worklist_patient_id;
                    $params['event']->step_id = $step->id;
                    $params['event']->save();
                }
            }

            if ($step && (int)$step->status === PathwayStep::STEP_REQUESTED) {
                $module = $params['event']->eventType->class_name;
                $step->nextStatus(['view_url' => "/$module/default/view/{$params['event']->id}"]);
            }
        } else {
            // Find the first non-completed step with an associated event type that matches the specified event type.
            $step = $this->getUnassociatedStepForEvent($params['event_type'], $params['patient_id'], false, $params['event_type']->worklist_patient);

            if ($step) {
                $event_type_id = EventType::model()->find(
                    'class_name = :class_name',
                    [':class_name' => $step->getState('event_type')]
                )->id;
                $firm = Firm::model()->findByPk(Yii::app()->session['selected_firm_id']);
                if (!$firm) {
                    throw new Exception('Unable to retrieve context.');
                }
                $service = $firm->getDefaultServiceFirm();

                if ($service) {
                    $url_params = [
                        'patient_id' => $step->pathway->worklist_patient->patient_id,
                        'context_id' => $firm->id,
                        'service_id' => $service ? $service->id : null,
                        'event_type_id' => $event_type_id,
                        'worklist_patient_id' => $step->pathway->worklist_patient_id,
                        'step_id' => $step->id,
                    ];
                    $create_url = '/patientEvent/create?' . http_build_query($url_params);

                    // Set the event creation URL.
                    $step->nextStatus(['event_create_url' => $create_url]);
                }
            }
            // If no step, then do nothing.
        }
    }

    /**
     * Observer method for bootstrapping step status.
     * This is mainly used for events saved with a completed status eg. Printed.
     * @param $params
     * @throws Exception
     */
    public function completeStep($params): void
    {
        if ($params['event']) {
            $step = PathwayStep::model()->findByPk($params['event']->step_id);

            // Only fire this event handler if the action is a create action, or if the action is an update action AND
            // the event is an Examination event.
            if (
                $params['action'] === 'create'
                || ($params['action'] === 'update' && in_array($params['event']->eventType->class_name, ['OphCiExamination', 'OphInBiometry'], true))
            ) {
                if ($step) {
                    if ((int)$step->status === PathwayStep::STEP_STARTED) {
                        $module = $params['event']->eventType->class_name;
                        $state_data = [
                            'event_id' => $params['event']->id,
                            'event_view_url' => "/$module/default/view/{$params['event']->id}",
                            'event_create_url' => null, // Clear out the event creation URL
                        ];
                        $step->markCompleted($state_data);
                    }
                } else {
                    // Event is not linked to a step but should still update the first relevant step.
                    // Find the first non-completed step with an associated event type that matches the specified event type.
                    $step = $this->getUnassociatedStepForEvent(
                        $params['event']->eventType,
                        $params['event']->episode->patient_id,
                        true,
                        $params['event']->worklist_patient
                    );

                    if ($step) {
                        $params['event']->worklist_patient_id = $step->pathway->worklist_patient_id;
                        $params['event']->step_id = $step->id;
                        $params['event']->save();
                        $module = $params['event']->eventType->class_name;

                        // Set the event ID and view URL. The view URL will be used for 'Go to Event' buttons in the pathway.
                        $state_data = [
                            'event_id' => $params['event']->id,
                            'event_view_url' => "/$module/default/view/{$params['event']->id}",
                            'event_create_url' => null, // Clear out the event creation URL
                        ];
                        $step->markCompleted($state_data);
                    }
                    // If no step, then do nothing.
                }
            }
        }
    }
}
