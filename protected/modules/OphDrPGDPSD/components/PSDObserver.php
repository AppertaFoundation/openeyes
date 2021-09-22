<?php

class PSDObserver
{
    /**
     * @throws Exception
     */
    public function createPSD($data)
    {
        if ($data['step']->getState('action_type') === 'manage_psd') {
            $preset_id = $data['step']->getState('preset_id');
            $laterality = $data['step']->getState('laterality');
            $pathway_id = $data['step']->pathway_id;

            $pathway = Pathway::model()->findByPk($pathway_id);
            $preset = OphDrPGDPSD_PGDPSD::model()->findByPk($preset_id);

            if (!$pathway || !$preset) {
                throw new Exception("Unable to retrieve pathway or Drug Administration preset order.");
            }

            $errors = array();
            $transaction = Yii::app()->db->beginTransaction();
            $assignment = new OphDrPGDPSD_Assignment();
            $assignment->confirmed = 1;
            $assignment->patient_id = $pathway->worklist_patient->patient_id;
            $assignment->visit_id = $pathway->worklist_patient_id;
            $assignment->pgdpsd_id = $preset_id;
            $assignment->institution_id = Yii::app()->session['selected_institution_id'];
            $assignment->cacheMeds($preset->serialiseMedicationAssignments($laterality));
            if (!$assignment->save()) {
                $errors = $assignment->getErrors();
            }
            if ($errors) {
                $transaction->rollback();
            } else {
                $assignment->refresh();

                // Update the state data.
                $data['step']->setState('assignment_id', $assignment->id);
                $data['step']->save();

                $transaction->commit();
                $audit_assigned_psd = $assignment->pgdpsd_id ? ", Assigned PSD: $assignment->pgdpsd_id" : "";
                Audit::add('PSD Assignment', 'created assignment', "Assignment id: $assignment->id$audit_assigned_psd");
            }
        }
    }

    /**
     * @param $data
     * @param $invoker
     * @throws CDbException
     * @throws Exception
     */
    public function removePSD($data)
    {
        if ($data['step']->getState('action_type') === 'manage_psd') {
            $assignment = OphDrPGDPSD_Assignment::model()->findByPk($data['step']->getState('assignment_id'));
            if ($assignment && (int)$assignment->status === $assignment::STATUS_TODO) {
                $transaction = \Yii::app()->db->beginTransaction();
                $assignment->delete();

                if ($assignment->getErrors()) {
                    $transaction->rollback();
                    throw new Exception('Unable to remove PSD.');
                } else {
                    $transaction->commit();
                    Audit::add('PSD Assignment', 'removed assignment', "Assignment id: {$assignment->id}");
                }
            }
        }
    }

    /**
     * @param $params
     * @param $invoker
     * @throws Exception
     */
    public function unlockPSD($params): void
    {
        if ($params['step']->getState('action_type') === 'manage_psd') {
            $assignment_id = $params['step']->getState('assignment_id');
            $assignment = OphDrPGDPSD_Assignment::model()->findByPk($assignment_id);
            if (!$assignment) {
                throw new Exception("Unable to retrieve step or PSD.");
            }
            $params['step']->pincode = $params['step']->getState('pincode');
            $params['step']->save();
            $assignment->updateStatus();
        }
    }

    /**
     * @param $params
     * @throws Exception
     */
    public function confirmAdministration($params): void
    {
        if ($params['step']->getState('action_type') === 'manage_psd') {
            $step = $params['step'];
            $pgdpsd_api = Yii::app()->moduleAPI->get('OphDrPGDPSD');
            $assignment_id = $params['assignment_id'];
            $patient_id = $params['patient_id'];
            $assignment = OphDrPGDPSD_Assignment::model()->findByPk($assignment_id);

            if (!$assignment) {
                throw new Exception('Unable to retrieve step.');
            }
            $entries = $params['assignment']['entries'];
            $worklist_patient_id = $assignment->visit_id;
            $firm = Firm::model()->findByPk(Yii::app()->session['selected_firm_id']);

            if (!$firm) {
                throw new Exception("Unable to retrieve context.");
            }
            $assignment_entries = array();
            $audit_data = array();
            foreach ($assignment->assigned_meds as $med) {
                $assignment_entries[$med->id] = $med;
            }
            $errors = array();
            $latest_element = null;
            if (!$assignment->elements) {
                $latest_element = $pgdpsd_api->getPatientLatestDAelement(
                    $assignment->patient,
                    $worklist_patient_id,
                    EventType::model()->find('class_name = \'OphDrPGDPSD\'')
                );
            } else {
                foreach ($assignment->elements as $element) {
                    $element_firm_id = $element->event->episode->firm->getSubspecialtyID();
                    $event_date = date('Y-m-d', strtotime($element->event->event_date));
                    if ((int)$element_firm_id === (int)$firm->getSubspecialtyID() && $event_date === date('Y-m-d')) {
                        $latest_element = $element;
                        break;
                    }
                }
            }
            $transaction = Yii::app()->db->beginTransaction();
            $has_administered = false;
            foreach ($entries as $key => &$entry) {
                $entry['administered'] = isset($entry['administered']) && $entry['administered']
                    ? (int)$entry['administered']
                    : 0;
                $entry['administered_time'] = isset($entry['administered_time']) && $entry['administered_time']
                    ? date('Y-m-d H:i:s', $entry['administered_time'] / 1000)
                    : null;
                $entry['administered_by'] = isset($entry['administered_by']) && $entry['administered_by']
                    ? $entry['administered_by']
                    : null;
                $entry['laterality'] = isset($entry['laterality']) && $entry['laterality'] ? $entry['laterality'] : null;
                $assignment_entries[(int)$key]->attributes = $entry;
                $assignment_entries[(int)$key]->save();
                // if a medication is administered, a record in event_medication_use will be needed
                if ($assignment_entries[$key]->administered) {
                    $has_administered = true;
                    $audit_data[] = $assignment_entries[$key]->medication_id
                        . ' was administered by '
                        . $assignment_entries[$key]->administered_by;
                }
            }
            unset($entry);
            $errors = array_merge($errors, $assignment->getErrors());
            $failed_saving = false;
            if ($has_administered) {
                if (!$latest_element) {
                    // create an automated one if no corresponding event
                    $patient = Patient::model()->findByPk($patient_id);
                    if ($patient) {
                        $episode = $patient->getOrCreateEpisodeForFirm($firm);
                        $da_creator = new DrugAdministrationCreator($episode);
                        $da_creator->setEntriesAndWorklistPatient($assignment, $worklist_patient_id, $firm->id);
                        if (!$da_creator->save()) {
                            $failed_saving = true;
                            $errors = array_merge($errors, $da_creator->getErrors());
                        } else {
                            $latest_element = $da_creator->elements['Element_DrugAdministration'];
                            Audit::add('PSD Assignment', 'created automated event', "Event: $latest_element->event_id");
                        }
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
                    Audit::add(
                        'PSD Assignment',
                        'administration',
                        "Assignment id: $assignment_id, Details: $audit_data, Event: $latest_element->event_id"
                    );
                }
                $assignment->updateStatus();
                $transaction->commit();
            }
        }
    }
}
