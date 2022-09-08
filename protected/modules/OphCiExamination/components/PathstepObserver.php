<?php

namespace OEModule\OphCiExamination\components;

use EventType;
use Exception;
use Firm;
use JsonException;
use OEModule\OphCiExamination\models\Element_OphCiExamination_ClinicOutcome;
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

            $service_id = $step->getState('service_id');

            // Either use the firm for an existing episode for the selected subspecialty or default to the first one found if it's not set in the step state
            if ($service_id === null) {
                $episode = $step->pathway->worklist_patient->patient->getOpenEpisodeOfSubspecialty($firm->serviceSubspecialtyAssignment->subspecialty_id);

                if ($episode) {
                    $service_id = $episode->firm_id;
                } else {
                    $service = Firm::model()->find(
                        'service_subspecialty_assignment_id = :id AND can_own_an_episode = 1',
                        [':id' => $firm->service_subspecialty_assignment_id]
                    );

                    $service_id = $service->id;
                }
            }

            $workflow_step_id = $step->getState('workflow_step_id');

            // This is to avoid the scenario where undoing a pathway step would proceed ahead examination workflow to next step
            if ($workflow_step_id === "" || $workflow_step_id === "undefined") {
                // The pathway step is not part of a workflow step
                // So either take user to update the associated event, or to event creation page
                $workflow_step_condition[] = "e.step_id = :step_id";
                $workflow_step_condition[] = [':step_id' => $step->id];
                $event_status = 'update';       // Take the user to update associated event
            } else {
                $workflow_step_condition = '1=1';
                $event_status = 'step';         // It is part of a workflow step, so proceed to next step of latest available event
            }

            // Determine if an examination event already exists for the patient visit.
            // If it doesn't exist, set the URL to the event creation URL so the episode is also created.
            // Otherwise, set the URL to the step create URL for the existing event.
            $latest_exam_event_for_visit = Yii::app()->db->createCommand()
                ->select('e.id')
                ->from('event e')
                ->join('event_type et', 'et.id = e.event_type_id')
                ->join('episode ep', 'ep.id = e.episode_id')
                ->where('e.worklist_patient_id = :worklist_patient_id AND ep.firm_id = :service_id AND et.class_name = \'OphCiExamination\'')
                ->andWhere($workflow_step_condition)
                ->andWhere('e.deleted = 0')
                ->limit(1)
                ->order('e.event_date DESC')
                ->bindValues(
                    [':worklist_patient_id' => $step->pathway->worklist_patient_id, ':service_id' => $service_id]
                )
                ->queryScalar();

            if ($latest_exam_event_for_visit) {
                $params = [
                    'patient_id' => $step->pathway->worklist_patient->patient_id,
                    'step_id' => $workflow_step_id,
                    'worklist_patient_id' => $step->pathway->worklist_patient_id,
                    'worklist_step_id' => $step->id,
                ];
                $step->setState(
                    'event_create_url',
                    '/OphCiExamination/default/' . $event_status . '/' . $latest_exam_event_for_visit . '?' . http_build_query($params)
                );
            } else {
                $params = [
                    'patient_id' => $step->pathway->worklist_patient->patient_id,
                    'context_id' => $firm->id,
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

    /**
     * @throws JsonException
     * @throws Exception
     */
    public function createFollowUpStep($params)
    {
        $event = $params['event'];

        $outcome_element = Element_OphCiExamination_ClinicOutcome::model()->find('event_id = :id', [':id' => $event->id]);

        if ($outcome_element && $outcome_element->hasFollowUpStatus()) {
            $step_type = \PathwayStepType::model()->find('short_name = \'Book Apt.\'');
            foreach ($outcome_element->entries as $entry) {
                if ($entry->isFollowUp()) {
                    // First, attempt to find an existing follow-up step with a matching entry ID. If one is found, update it. Otherwise, create a new one.
                    $existing_step = PathwayStep::model()->find(
                        'pathway_id = :id AND step_type_id = :step_type AND JSON_CONTAINS(state_data, \''
                        . $entry->id
                        . '\', \'$.followup_entry_id\')',
                        [':id' => $event->worklist_patient->pathway->id, ':step_type' => $step_type->id]
                    );
                    if ($existing_step) {
                        $initial_state_data = json_decode($existing_step->state_data);

                        $initial_state_data['site_id'] = $entry->site_id;
                        $initial_state_data['service_id'] = $entry->service_id;
                        $initial_state_data['firm_id'] = $entry->context_id;
                        $initial_state_data['duration_value'] = $entry->followup_quantity;
                        $initial_state_data['duration_period'] = $entry->followupPeriod->name;
                        $existing_step->state_data = json_encode($initial_state_data);
                        $existing_step->save();
                    } else {
                        $initial_state_data = json_decode($step_type->state_data_template);

                        $initial_state_data['site_id'] = $entry->site_id;
                        $initial_state_data['service_id'] = $entry->service_id;
                        $initial_state_data['firm_id'] = $entry->context_id;
                        $initial_state_data['duration_value'] = $entry->followup_quantity;
                        $initial_state_data['duration_period'] = $entry->followupPeriod->name;
                        $initial_state_data['followup_entry_id'] = $entry->id;

                        $new_step = $step_type->createNewStepForPathway($event->worklist_patient->pathway->id, $initial_state_data);

                        if ($new_step) {
                            // Re-activate pathway if it has already been marked as completed.
                            if ((int)$event->worklist_patient->pathway->status === \Pathway::STATUS_DONE) {
                                $pathway = $event->worklist_patient->pathway;
                                $pathway->status = \Pathway::STATUS_WAITING;
                                if (!$pathway->save()) {
                                    throw new \CHttpException(500, 'Unable to re-activate pathway.');
                                }
                                $event->worklist_patient->pathway->refresh();
                            }
                        }
                    }
                }
            }
        }
    }
}
