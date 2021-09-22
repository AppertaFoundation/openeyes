<?php

namespace OEModule\OphCiExamination\components;

use EventType;
use Exception;
use Firm;
use JsonException;
use PathwayStep;
use Yii;

class PathstepObserver
{

    /**
     * @throws JsonException
     * @throws Exception
     */
    public function createOrUpdateEvent($params)
    {
        /**
         * @var $step PathwayStep
         */
        $step = $params['step'];

        // We only want to capture steps that have the new_event action and are examination steps
        // (other event types are handled separately)
        if ($step->getState('action_type') === 'new_event' && $step->getState('event_type') === 'OphCiExamination') {
            $event_type_id = EventType::model()->find(
                'class_name = :class_name',
                [':class_name' => 'OphCiExamination']
            )->id;
            $firm = Firm::model()->findByPk($step->getState('firm_id'));
            if (!$firm) {
                $firm = Firm::model()->findByPk(Yii::app()->session['selected_firm_id']);
            }
            $service = Firm::model()->find(
                'service_subspecialty_assignment_id = :id AND can_own_an_episode = 1',
                [':id' => $firm->service_subspecialty_assignment_id]
            );
            $service_id = $service->id;

            // Determine if an examination event already exists for the patient for the episode connected to the service
            // firm. If it doesn't exist, set the URL to the event creation URL so the episode is also created.
            // Otherwise, set the URL to the step create URL for the existing event.
            $latest_exam_event_for_episode = Yii::app()->db->createCommand()
                ->select('e.id')
                ->from('event e')
                ->join('event_type et', 'et.id = e.event_type_id')
                ->join('episode ep', 'ep.id = e.episode_id')
                ->where('ep.patient_id = :patient_id AND ep.firm_id = :service_id AND et.class_name = \'OphCiExamination\'')
                ->limit(1)
                ->order('e.event_date DESC')
                ->bindValues(
                    [':patient_id' => $step->pathway->worklist_patient->patient_id, ':service_id' => $service_id]
                )
                ->queryScalar();

            if ($latest_exam_event_for_episode) {
                $params = [
                    'patient_id' => $step->pathway->worklist_patient->patient_id,
                    'step_id' => $step->getState('workflow_step_id'),
                    'worklist_patient_id' => $step->pathway->worklist_patient_id,
                    'worklist_step_id' => $step->id,
                ];
                $step->setState(
                    'event_create_url',
                    '/OphCiExamination/default/step/' . $latest_exam_event_for_episode. '?' . http_build_query($params)
                );
            } else {
                $params = [
                    'patient_id' => $step->pathway->worklist_patient->patient_id,
                    'context_id' => $step->getState('firm_id'),
                    'service_id' => $service_id,
                    'event_type_id' => $event_type_id,
                    'worklist_patient_id' => $step->pathway->worklist_patient_id,
                    'step_id' => $step->id,
                ];
                $step->setState('event_create_url', '/patientEvent/create?' . http_build_query($params));
            }

            $step->save();
        }
    }
}
