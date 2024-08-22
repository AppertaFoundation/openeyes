<?php

class EventStepObserver
{
    /**
     * @param array $params
     * @throws JsonException
     * @throws Exception
     */
    public function createEvent(array $params)
    {
        /**
         * @var $step PathwayStep
         */
        $step = $params['step'];

        // We only want to capture steps that have the new_event action and aren't examination steps (these are handled separately).
        if ($step->getState('action_type') === 'new_event' && $step->getState('event_type') !== 'OphCiExamination') {
            $event_type_id = EventType::model()->find(
                'class_name = :class_name',
                [':class_name' => $step->getState('event_type')]
            )->id;
            $firm = Firm::model()->findByPk($step->getState('firm_id'));
            $service = $firm->getDefaultServiceFirm();
            $service_id = $service->id;
            $params = [
                'patient_id' => $step->pathway->worklist_patient->patient_id,
                'context_id' => $step->getState('firm_id'),
                'service_id' => $service_id,
                'event_type_id' => $event_type_id,
                'worklist_patient_id' => $step->pathway->worklist_patient_id,
                'step_id' => $step->id,
            ];

            if (!$step->getState('event_create_url')) {
                $step->setState('event_create_url', '/patientEvent/create?' . http_build_query($params));
                $step->save();
            }
        }
    }
}
