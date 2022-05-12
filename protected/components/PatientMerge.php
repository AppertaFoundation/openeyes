<?php

/**
 * OpenEyes.
 *
 *
 * Copyright OpenEyes Foundation, 2017
 *
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright 2017, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
class PatientMerge
{
    /**
     * @var Patient AR
     */
    private $primary_patient;

    /**
     * @var Patient AR
     */
    private $secondary_patient;

    /**
     * @var array
     */
    private $log = array();

    /**
     * @var string
     */
    private $merge_id;

    /**
     * @var PatientIdentifier
     */
    public $primary_patient_identifier;
    /**
     * @var PatientIdentifier
     */
    public $secondary_patient_identifier;

    /**
     * @var PatientIdentifierType|null
     * If set, this explicit identifier type will be used
     * to look up patient numbers
     */
    private ?PatientIdentifierType $patient_identifier_type;

    /**
     * Set primary patient by id.
     *
     * @param int $id
     */
    public function setPrimaryPatientById($id)
    {
        $this->primary_patient = Patient::model()->findByPk($id);
    }

    /**
     * Returns the Primary patient.
     *
     * @return Patient AR record
     */
    public function getPrimaryPatient()
    {
        return $this->primary_patient;
    }

    /**
     * Set secondaty patient by id.
     *
     * @param int $id
     */
    public function setSecondaryPatientById($id)
    {
        $this->secondary_patient = Patient::model()->findByPk($id);
    }

    /**
     * Returns the secondary patient.
     *
     * @return Patient AR record
     */
    public function getSecondaryPatient()
    {
        return $this->secondary_patient;
    }

    /**
     * Adding message to the log array.
     *
     * @param string $msg
     */
    public function addLog($msg)
    {
        $this->log[] = $msg;
    }

    /**
     * Returns the log messages.
     *
     * @return type
     */
    public function getLog()
    {
        return $this->log;
    }

    /**
     * Load data from PatientMergeRequest AR record.
     *
     * @param PatientMergeRequest $request
     * @param PatientIdentifierType $patient_identifier_type If set, this identifier type will be used to look up
     *                                       patients instead of the one based on the logged in user's
     *                                       institution_id
     */
    public function load(PatientMergeRequest $request, PatientIdentifierType $patient_identifier_type = null)
    {
        $this->setPrimaryPatientById($request->primary_id);
        $this->setSecondaryPatientById($request->secondary_id);
        $this->patient_identifier_type = $patient_identifier_type;
        $this->merge_id = $request->id;
        $this->primary_patient_identifier = $this->getPatientIdentifier($request->primary_id);
        $this->secondary_patient_identifier = $this->getPatientIdentifier($request->secondary_id);
    }

    private function getPatientIdentifier($patient_id)
    {
        if(is_null($this->patient_identifier_type)) {
            return PatientIdentifierHelper::getIdentifierForPatient(SettingMetadata::model()->getSetting('display_primary_number_usage_code'), $patient_id, \Institution::model()->getCurrent()->id, Yii::app()->session['selected_site_id']);
        } else {
            return PatientIdentifierHelper::getPatientIdentifierByType($patient_id, $this->patient_identifier_type);
        }
    }

    /**
     * Compare data in the patient table.
     *
     * @param patient AR record $primary
     * @param patient AR record $secondary
     *
     * @return array
     */
    public function comparePatientDetails(Patient $primary, Patient $secondary)
    {
        //columns to be compared in patient table
        $columns = array(
            'dob', 'gender', /*'hos_num', 'nhs_num', 'date_of_death', 'ethnic_group_id', 'contact_id', */
        );

        $conflict = array();

        foreach ($columns as $column) {
            if ($primary->$column !== $secondary->$column) {
                $conflict[] = array(
                    'column' => $column,
                    'primary' => $primary->$column,
                    'secondary' => $secondary->$column,
                );
            }
        }

        return array(
            'is_conflict' => !empty($conflict),
            'details' => $conflict,
        );
    }

    /**
     * Check if there is anything prevents the merging
     * e.g.: PAS patient cannot be merged into local patient
     *
     * note: personal details are not checked here
     *
     * @param Patient $secondary
     * @param Patient $primary
     */
    public function isMergable(Patient $primary, Patient $secondary)
    {

        $conflict = array();

        if ($secondary->is_local == 0 && $primary->is_local == 1) {
            $conflict[] = array(
                'column' => 'is_local',
                'primary' => $primary->id,
                'secondary' => $secondary->id,
                'message' => 'Non local patient(' . PatientIdentifierHelper::getIdentifierPrompt($this->secondary_patient_identifier) . ':' . PatientIdentifierHelper::getIdentifierValue($this->secondary_patient_identifier) . ') cannot be merged into local patient(' . PatientIdentifierHelper::getIdentifierPrompt($this->primary_patient_identifier) . ':' . PatientIdentifierHelper::getIdentifierValue($this->primary_patient_identifier) . ').',
                'attribute' => 'secondary_id',
            );
        }

        if ($secondary->is_deceased !== $primary->is_deceased) {
            $conflict[] = array(
                'column' => 'is_deceased',
                'primary' => $primary->id,
                'secondary' => $secondary->id,
                'message' => "Patients' is_deceased flag must be the same.",
                'attribute' => 'is_deceased',
            );
        }

        if (Yii::app()->hasModule('Genetics')) {

            $primary_genetics_patient = GeneticsPatient::model()->findByAttributes(['patient_id' => $primary->id]);
            $secondary_genetics_patient = GeneticsPatient::model()->findByAttributes(['patient_id' => $secondary->id]);

            if ($primary_genetics_patient && $secondary_genetics_patient) {
                //Abort if karyotypic sex or deceased flag are not the same
                if ($primary_genetics_patient->gender_id !== $secondary_genetics_patient->gender_id) {
                    $conflict[] = array(
                        'column' => 'gender_id',
                        'primary' => $primary->id,
                        'secondary' => $secondary->id,
                        'message' => "Genetics Patients' karyotypic sex must be the same.",
                        'attribute' => 'secondary_id',
                    );
                }
            }
        }

        return $conflict;
    }

    /**
     * Do the actual merging by calling separated functions to move episodes, events etc...
     *
     * @return bool $is_merged success or fail
     */
    public function merge()
    {
        $is_merged = false;

        $is_mergable = $this->isMergable($this->primary_patient, $this->secondary_patient);

        if (!empty($is_mergable)) {
            $msg = isset($is_mergable[0]['message']) ? $is_mergable[0]['message'] : ('Patients cannot be merged ' . print_r($is_mergable[0], true));
            throw new Exception($msg);
        }

        $transaction = Yii::app()->db->beginTransaction();

        try {
            // Update Episode
            $is_merged = $this->updateEpisodes($this->primary_patient, $this->secondary_patient);

            // Update legacy episodes
            $is_merged = $is_merged && $this->updateLegacyEpisodes($this->primary_patient, $this->secondary_patient);

            // Update change tracker episodes
            $is_merged = $is_merged && $this->updateChangeTrackerEpisodes($this->primary_patient, $this->secondary_patient);

            //Update Other ophthalmic diagnoses
            $is_merged = $is_merged && $this->updateOphthalmicDiagnoses($this->primary_patient, $this->secondary_patient->ophthalmicDiagnoses);

            // Update Systemic Diagnoses
            $is_merged = $is_merged && $this->updateSystemicDiagnoses($this->primary_patient, $this->secondary_patient->systemicDiagnoses);

            // Update Genetics
            if (Yii::app()->hasModule('Genetics')) {
                $is_merged = $is_merged && $this->updateGenetics($this->primary_patient, $this->secondary_patient);
            }

            // Update Hot-list
            $is_merged = $is_merged && $this->updateHotList($this->primary_patient, $this->secondary_patient);

            // Update Work-lists
            $is_merged = $is_merged && $this->updateWorkLists($this->primary_patient, $this->secondary_patient);

            if ($is_merged) {
                $secondary_patient_identifiers = PatientIdentifier::model()->disableDefaultScope()->findAllByAttributes(['patient_id' => $this->secondary_patient->id]);
                foreach ($secondary_patient_identifiers as $row_id => $secondary_patient_identifier_row) {

                    $criteria = new CDbCriteria();
                    $criteria->addCondition('patient_id = :patient_id');
                    $criteria->addCondition('value = :value');
                    $criteria->addCondition('patient_identifier_type_id = :patient_identifier_type_id');
                    $criteria->params[':patient_id'] = $this->primary_patient->id;
                    $criteria->params[':value'] = $secondary_patient_identifier_row->value;
                    $criteria->params[':patient_identifier_type_id'] = $secondary_patient_identifier_row->patient_identifier_type_id;

                    $duplicate_patient_identifier = PatientIdentifier::model()->disableDefaultScope()->find($criteria);

                    $duplicate_patient_identifier_found = !is_null($duplicate_patient_identifier);

                    if (!$duplicate_patient_identifier_found) {
                        $new_patient_identifier_row = new PatientIdentifier();
                        $new_patient_identifier_row->patient_id = $this->primary_patient->id;
                        $new_patient_identifier_row->patient_identifier_type_id = $secondary_patient_identifier_row->patient_identifier_type_id;
                        $new_patient_identifier_row->value = $secondary_patient_identifier_row->value;
                        $new_patient_identifier_row->deleted = $secondary_patient_identifier_row->deleted;
                        $new_patient_identifier_row->source_info = ($secondary_patient_identifier_row->source_info !== 'ACTIVE' ? $secondary_patient_identifier_row->source_info . ', ' : '');
                        if (in_array(
                                $secondary_patient_identifier_row->patient_identifier_type_id,
                                array_column($this->primary_patient->identifiers, 'patient_identifier_type_id')
                            ) || $secondary_patient_identifier_row->deleted == "1") {
                            $new_patient_identifier_row->source_info .= "DEL ID=(" . $this->merge_id . ")";
                            $new_patient_identifier_row->deleted = '1';
                        } else {

                            $new_patient_identifier_row->source_info .= "MERG ID =(" . $this->merge_id . ")";
                        }
                        $new_patient_identifier_row->last_modified_user_id = Yii::app()->user->id;
                        $new_patient_identifier_row->last_modified_date = date("Y-m-d H:i:s");
                        $new_patient_identifier_row->created_user_id = Yii::app()->user->id;
                        $new_patient_identifier_row->created_date = date("Y-m-d H:i:s");
                    }

                    $secondary_patient_identifier_row->deleted = '1';
                    $secondary_patient_identifier_row->source_info = "DEL ID=(" . $this->merge_id . ")" . "[" . time() . "]";
                    if (!$secondary_patient_identifier_row->update(['deleted','source_info'])) {
                        $errors[] = $secondary_patient_identifier_row->getErrors();
                        throw new Exception('Failed to update : ' . print_r($errors, true));
                    }

                    if (!$duplicate_patient_identifier_found) {
                        if (!$new_patient_identifier_row->save()) {
                            $errors[] = $new_patient_identifier_row->getErrors();
                            throw new Exception('Failed to insert : ' . print_r($errors, true));
                        }
                    }

                    if ($duplicate_patient_identifier_found && $duplicate_patient_identifier->deleted == 1) {
                        $duplicate_patient_identifier->source_info = \PatientIdentifierHelper::PATIENT_IDENTIFIER_ACTIVE_SOURCE_INFO;
                        $duplicate_patient_identifier->deleted = 0;
                        $duplicate_patient_identifier->update(['source_info', 'deleted']);
                    }
                }

                $secondary_patient = $this->secondary_patient;

                $secondary_patient->deleted = 1;

                if ($secondary_patient->save()) {
                    $msg = 'Patient ' . PatientIdentifierHelper::getIdentifierPrompt($this->secondary_patient_identifier) . ': ' . PatientIdentifierHelper::getIdentifierValue($this->secondary_patient_identifier) . ' flagged as deleted.';
                    $this->addLog($msg);
                    Audit::add('Patient Merge', 'Patient flagged as deleted', $msg);
                    $is_merged = $is_merged && true;

                    $transaction->commit();

                } else {
                    $transaction->rollback();
                    \OELog::log('Patient merge - secondary patient[id:' . $secondary_patient->id . '] could not be saved');
                    return false;
                }
            }
        } catch (Exception $e) {
            \OELog::logException($e);
            $transaction->rollback();
            return false;
        }

        return $is_merged;
    }

    /**
     * Updating an episode
     *  - if primary has no episodes than we just assign the secondary patient's episodes to the primary
     *  - if secondary patient has no episodes we have nothing to do here
     *  - if both patiens have episode we have to check if there is any conflicting(same subspeicaly like cataract or glaucoma) episodes
     *      - we move the non conflictong episodes from secondary to primary
     *      - when two episodes are conflicting we have to keep the episode with the highest status (when compared using the standard order of status from New to Discharged).
     *      - start date should be the earliest start date of the two episodes
     *      - end date should be the latest end date of the two episodes (null is classed as later than any date).
     *
     * @param Patient $primary_patient
     * @param Patient $secondary_patient
     *
     * @return bool
     *
     * @throws Exception
     */
    public function updateEpisodes(Patient $primary_patient, Patient $secondary_patient)
    {
        $primary_has_episodes = $primary_patient->episodes;
        $secondary_has_episodes = $secondary_patient->episodes;

        // if primary has no episodes than we just assign the secondary patient's episodes to the primary
        if (!$primary_has_episodes && $secondary_has_episodes) {
            // this case is fine, we can assign the episodes from secondary to primary
            $this->updateEpisodesPatientId($primary_patient->id, $secondary_patient->episodes);
        } elseif ($primary_has_episodes && !$secondary_has_episodes) {
            // primary has episodes but secondary has not, nothing to do here
        } else {
            // Both have episodes, we have to compare the subspecialties

            foreach ($secondary_patient->episodes as $secondary_episode) {
                $secondary_subspecialty = $secondary_episode->getSubspecialtyID();

                $is_same_subspecialty = false;
                foreach ($primary_has_episodes as $primary_episode) {
                    $primary_subspecialty = $primary_episode->getSubspecialtyID();

                    if ($secondary_subspecialty == $primary_subspecialty) {

                        /* We have to keep the episode with the highest status */

                        if ($primary_episode->status->order > $secondary_episode->status->order) {
                            // the primary episode has greater status than the secondary so we move the events from the Secondary into the Primary
                            $this->updateEventsEpisodeId($primary_episode->id, $secondary_episode->events);

                            // keeping the oldest episode's firm_id
                            if ($primary_episode->start_date > $secondary_episode->start_date) {
                                $primary_episode->firm_id = $secondary_episode->firm_id;
                            }

                            //set earliest start date and latest end date of the two episodes
                            list($primary_episode->start_date, $primary_episode->end_date) = $this->getTwoEpisodesStartEndDate($primary_episode, $secondary_episode);

                            $primary_episode->save();

                            // after all events are moved we flag the secondary episode as deleted
                            $secondary_episode->deleted = 1;
                            if ($secondary_episode->save()) {
                                $msg = 'Episode ' . $secondary_episode->id . " marked as deleted, events moved under the primary patient's same firm episode.";
                                $this->addLog($msg);
                                Audit::add('Patient Merge', 'Episode marked as deleted', $msg);
                            } else {
                                throw new Exception('Failed to update Episode: ' . $secondary_episode->id . ' ' . print_r($secondary_episode->errors, true));
                            }
                        } else {

                            // the secondary episode has greater status than the primary so we move the events from the Primary into the Secondary
                            $this->updateEventsEpisodeId($secondary_episode->id, $primary_episode->events);

                            // keeping the oldest episode's firm_id
                            if ($primary_episode->start_date < $secondary_episode->start_date) {
                                $secondary_episode->firm_id = $primary_episode->firm_id;
                            }

                            list($secondary_episode->start_date, $secondary_episode->end_date) = $this->getTwoEpisodesStartEndDate($primary_episode, $secondary_episode);

                            /* BUT do not forget we have to delete the primary episode - at this point we already moved the secondary episode to the primary patient **/
                            $primary_episode->deleted = 1;

                            if ($primary_episode->save()) {
                                $msg = 'Episode ' . $primary_episode->id . " marked as deleted, events moved under the secondary patient's same firm episode.";
                                $this->addLog($msg);
                                Audit::add('Patient Merge', 'Episode marked as deleted', $msg);
                            } else {
                                throw new Exception('Failed to update Episode: ' . $primary_episode->id . ' ' . print_r($primary_episode->errors, true));
                            }

                            //then we move the episode to the pri1mary
                            $this->updateEpisodesPatientId($primary_patient->id, array($secondary_episode));
                        }

                        $is_same_subspecialty = true;
                    }
                }

                // if there is no conflict we still need to move the secondary episode to the primary patient
                if (!$is_same_subspecialty) {
                    $this->updateEpisodesPatientId($primary_patient->id, array($secondary_episode));
                } else {
                    // there was a conflict and the episode was already moved in the foreach above
                }
            }
        }

        // if the save() functions not throwing errors than we can just return true
        return true;
    }

    /**
     * Moving Legacy episode from secondary patient to primary.
     *
     * @param type $primary_patient
     * @param type $secondary_patient
     *
     * @return bool
     *
     * @throws Exception
     */
    public function updateLegacyEpisodes($primary_patient, $secondary_patient)
    {
        // if the secondary patient has legacy episodes
        if ($secondary_patient->legacyepisodes) {

            // if primary patient doesn't have legacy episode we can just update the episode's patient_id to assign it to the primary patient
            if (!$primary_patient->legacyepisodes) {

                // Patient can have only one legacy episode
                $legacy_episode = $secondary_patient->legacyepisodes[0];

                $primary_identifier = $this->getPatientIdentifier($primary_patient->id);
                $secondary_identifier = $this->getPatientIdentifier($secondary_patient->id);

                $legacy_episode->patient_id = $primary_patient->id;
                if ($legacy_episode->save()) {
                    $msg = 'Legacy Episode ' . $legacy_episode->id . ' moved from patient ' . PatientIdentifierHelper::getIdentifierValue($secondary_identifier) . ' to ' . PatientIdentifierHelper::getIdentifierValue($primary_identifier);
                    $this->addLog($msg);
                    Audit::add('Patient Merge', 'Legacy Episode moved', $msg);
                } else {
                    throw new Exception('Failed to update (legacy) Episode: ' . $legacy_episode->id . ' ' . print_r($legacy_episode->errors, true));
                }
            } else {
                $primary_legacy_episode = $primary_patient->legacyepisodes[0];
                $secondary_legacy_episode = $secondary_patient->legacyepisodes[0];

                if ($primary_legacy_episode->created_date < $secondary_legacy_episode->created_date) {
                    // we move the events from the secondaty patient's legacy episod to the primary patient's legacy epiode
                    $this->updateEventsEpisodeId($primary_legacy_episode->id, $secondary_legacy_episode->events);

                    // Flag secondary patient's legacy episode deleted as it will be empty

                    $secondary_legacy_episode->deleted = 1;
                    if ($secondary_legacy_episode->save()) {
                        $msg = 'Legacy Episode ' . $secondary_legacy_episode->id . "marked as deleted, events moved under the primary patient's same firm episode.";
                        $this->addLog($msg);
                        Audit::add('Patient Merge', 'Legacy Episode marked as deleted', $msg);
                    } else {
                        throw new Exception('Failed to update (legacy) Episode: ' . $secondary_legacy_episode->id . ' ' . print_r($secondary_legacy_episode->errors, true));
                    }
                } else {
                    // in this case the secondary legacy episode is older than the primary
                    // so move the primary legacy episode's events to the secondary legacy episode
                    // then move the secondary legacy episode to the Primary patient
                    // then flag the primary's legacy episode as deleted // as only 1 legacy episode can be assigned to the patient

                    $this->updateEventsEpisodeId($secondary_legacy_episode->id, $primary_legacy_episode->events);

                    $primary_legacy_episode->deleted = 1;

                    if ($primary_legacy_episode->save()) {
                        $msg = 'Legacy Episode ' . $primary_legacy_episode->id . "marked as deleted, events moved under the secondary patient's same firm episode.";
                        $this->addLog($msg);
                        Audit::add('Patient Merge', 'Legacy Episode marked as deleted', $msg);
                    } else {
                        throw new Exception('Failed to update (legacy) Episode: ' . $primary_legacy_episode->id . ' ' . print_r($primary_legacy_episode->errors, true));
                    }

                    //then we move the episode to the pri1mary
                    $this->updateEpisodesPatientId($primary_patient->id, array($secondary_legacy_episode));
                }
            }
        }

        // if the save() functions not throwing errors than we can just return true
        return true;
    }

    /**
     * @param $primary_patient
     * @param $secondary_patient
     * @return bool
     * @throws Exception
     */
    public function updateChangeTrackerEpisodes($primary_patient, $secondary_patient)
    {
        $primary_ep = Episode::getChangeEpisode($primary_patient, false);
        $secondary_ep = Episode::getChangeEpisode($secondary_patient, false);
        $primary_identifier = $this->getPatientIdentifier($primary_patient->id);
        $secondary_identifier = $this->getPatientIdentifier($secondary_patient->id);
        if ($secondary_ep) {
            if ($primary_ep) {
                // move events
                $this->updateEventsEpisodeId($primary_ep->id, $secondary_ep->events);
                // mark as deleted
                $secondary_ep->deleted = 1;
                if ($secondary_ep->save()) {
                    $msg = 'Change Episode ' . $secondary_ep->id . "marked as deleted, events moved under the primary patient's change episode.";
                    $this->addLog($msg);
                    Audit::add('Patient Merge', 'Change Episode marked as deleted', $msg);
                } else {
                    throw new Exception('Failed to update (change) Episode: ' . $secondary_ep->id . ' ' . print_r($secondary_ep->errors, true));
                }
            } else {
                // move episode
                $secondary_ep->patient_id = $primary_patient->id;
                if ($secondary_ep->save()) {
                    $msg = 'Change Episode ' . $secondary_ep->id . ' moved from patient ' . PatientIdentifierHelper::getIdentifierValue($secondary_identifier) . ' to ' . PatientIdentifierHelper::getIdentifierValue($primary_identifier);
                    $this->addLog($msg);
                    Audit::add('Patient Merge', 'Change Episode moved', $msg);
                } else {
                    throw new Exception('Failed to update (change) Episode: ' . $secondary_ep->id . ' ' . print_r($secondary_ep->errors, true));
                }
            }
        }
        return true;
    }

    /**
     * Updates the Ophthalmic Diagnoses' patient_id
     *
     * @param Patient $new_patient
     * @param type $ophthalmic_diagnoses
     * @return boolean
     * @throws Exception
     */
    public function updateOphthalmicDiagnoses($new_patient, $ophthalmic_diagnoses)
    {
        $primary_identifier = $this->getPatientIdentifier($new_patient->id);
        foreach ($ophthalmic_diagnoses as $ophthalmic_diagnosis) {

            $secondary_identifier = $this->getPatientIdentifier($ophthalmic_diagnosis->patient->id);
            $msg = 'Ophthalmic Diagnosis(SecondaryDiagnosis) ' . $ophthalmic_diagnosis->id . ' moved from Patient ' . PatientIdentifierHelper::getIdentifierValue($secondary_identifier) . ' to ' . PatientIdentifierHelper::getIdentifierValue($primary_identifier);
            $ophthalmic_diagnosis->patient_id = $new_patient->id;
            if ($ophthalmic_diagnosis->save()) {
                $this->addLog($msg);
                Audit::add('Patient Merge', 'Ophthalmic Diagnosis(SecondaryDiagnosis) moved patient', $msg);
            } else {
                throw new Exception('Failed to update Ophthalmic Diagnosis(SecondaryDiagnosis): ' . $ophthalmic_diagnosis->id . ' ' . print_r($ophthalmic_diagnosis->errors, true));
            }
        }

        return true;
    }

    /**
     * Update Systemati Diagnoses' patient id
     *
     * @param Patient $new_patient
     * @param type $systemic_diagnoses
     * @return boolean
     * @return boolean
     * @throws Exception
     */
    public function updateSystemicDiagnoses($new_patient, $systemic_diagnoses)
    {
        $primary_identifier = $this->getPatientIdentifier($new_patient->id);
        foreach ($systemic_diagnoses as $systemic_diagnosis) {
            $secondary_identifier = $this->getPatientIdentifier($systemic_diagnosis->patient->id);
            $msg = 'Systemic Diagnoses ' . $systemic_diagnosis->id . ' moved from Patient ' . PatientIdentifierHelper::getIdentifierValue($secondary_identifier) . ' to ' . PatientIdentifierHelper::getIdentifierValue($primary_identifier);
            $systemic_diagnosis->patient_id = $new_patient->id;
            if ($systemic_diagnosis->save()) {
                $this->addLog($msg);
                Audit::add('Patient Merge', 'Systemic Diagnoses moved patient', $msg);
            } else {
                throw new Exception('Failed to update Systemic Diagnoses: ' . $systemic_diagnosis->id . ' ' . print_r($systemic_diagnosis->errors, true));
            }
        }

        return true;
    }

    /**
     * Merging Genetics patients
     *
     * @param Patient $primary_patient
     * @param Patient $secondary_patient
     * @return bool
     */
    public function updateGenetics(Patient $primary_patient, Patient $secondary_patient)
    {
        $primary_genetics_patient = GeneticsPatient::model()->findByAttributes(['patient_id' => $primary_patient->id]);
        $secondary_genetics_patient = GeneticsPatient::model()->findByAttributes(['patient_id' => $secondary_patient->id]);

        $primary_identifier = $this->getPatientIdentifier($primary_patient->id);
        $secondary_identifier = $this->getPatientIdentifier($secondary_patient->id);

        //if primary is genetics patient but secondary is not
        if ($primary_genetics_patient && !$secondary_genetics_patient) {
            //nothing to do here, as we would need to move genetics data from secondary to primary but the secondary is not a genetics patient

        } elseif (!$primary_genetics_patient && $secondary_genetics_patient) {
            //else if secondary is genetics patient but primary is not

            //now here we have to move all the genetics data from secondary to primary
            //as the primary is not a genetics user we can just update the Geneticspatient->patient_id attribute

            $secondary_genetics_patient->patient_id = $primary_patient->id;

            if ($secondary_genetics_patient->save()) {
                $this->addLog("Secondary Genetics Patient's (subject id:{$secondary_genetics_patient->id}) data moved to Primary Patient({PatientIdentifierHelper::getIdentifierPrompt($secondary_identifier)}:{PatientIdentifierHelper::getIdentifierValue($secondary_identifier)})");
            }

        } else if ($primary_genetics_patient && $secondary_genetics_patient) {
            //else both are genetics patients

            //here we cannot just re-wire the GeneticsPatient table's patient_id, actually we are not even update the GeneticsPatient table but
            //all the other tables that are referencing to the GeneticsPatient table.
            //We change the patient_id (which will be the genetics_patient.id in the related tables ) e.g.: genetics_patient_diagnosis.patient_id <- again, this is the genetics_patient.id

            $primary_diagnoses = $primary_genetics_patient->genetics_diagnosis;
            $primary_diagnoses_ids = array();
            foreach ($primary_diagnoses as $primary_diagnosis) {
                $primary_diagnoses_ids[$primary_diagnosis->disorder_id] = $primary_diagnosis;
            }

            if ($secondary_genetics_patient->diagnoses) {

                $primary_diagnoses = array();
                foreach ($secondary_genetics_patient->diagnoses as $genetics_patient_diagnosis) {

                    if (!array_key_exists($genetics_patient_diagnosis->id, $primary_diagnoses_ids)) {
                        $primary_diagnoses[] = $genetics_patient_diagnosis;

                        $this->addLog("Genetics Patient Diagnosis {$genetics_patient_diagnosis->id} moved from Subject {$secondary_genetics_patient->id} to {$primary_genetics_patient->id}");
                    }
                }

                $primary_genetics_patient->diagnoses = array_merge($primary_genetics_patient->diagnoses, $primary_diagnoses);
            }

            $primary_pedigrees = $primary_genetics_patient->pedigrees;
            $primary_pedigrees_ids = array();
            foreach ($primary_pedigrees as $primary_pedigrees) {
                $primary_pedigrees_ids[$primary_pedigrees->id] = $primary_pedigrees;
            }

            if ($secondary_genetics_patient->pedigrees) {

                $primary_pedigrees = array();
                foreach ($secondary_genetics_patient->pedigrees as $genetics_patient_pedigree) {
                    if (!array_key_exists($genetics_patient_pedigree->id, $primary_pedigrees_ids)) {
                        $primary_pedigrees[] = $genetics_patient_pedigree;

                        $this->addLog("Genetics Patient Pedigree {$genetics_patient_pedigree->id} moved from Subject {$secondary_genetics_patient->id} to {$primary_genetics_patient->id}");
                    }
                }

                $primary_genetics_patient->pedigrees = array_merge($primary_genetics_patient->pedigrees, $primary_pedigrees);
            }

            $primary_studies = $primary_genetics_patient->studies;
            $primary_study_ids = array();
            foreach ($primary_studies as $primary_study) {
                $primary_study_ids[$primary_study->id] = $primary_study;
            }
            if ($secondary_genetics_patient->studies) {

                $primary_study = array();
                foreach ($secondary_genetics_patient->studies as $genetics_patient_study) {
                    if (!array_key_exists($genetics_patient_study->id, $primary_study_ids)) {
                        $primary_study[] = $genetics_patient_study;

                        $this->addLog("Genetics Patient Study {$genetics_patient_study->id} moved from Subject {$secondary_genetics_patient->id} to {$primary_genetics_patient->id}");
                    }
                }

                $primary_genetics_patient->studies = array_merge($primary_genetics_patient->studies, $primary_study);
            }

            $primary_relationships = $primary_genetics_patient->relationships;
            $primary_relationship_ids = array();
            foreach ($primary_relationships as $primary_relationship) {
                $primary_relationship_ids[$primary_relationship->patient_id] = $primary_relationship;
            }

            if ($secondary_genetics_patient->relationships) {

                $primary_relationships = array();
                foreach ($secondary_genetics_patient->relationships as $genetics_patient_relationship) {
                    if (!array_key_exists($genetics_patient_relationship->patient_id, $primary_relationship_ids)) {
                        $primary_relationships[] = $genetics_patient_relationship;

                        $this->addLog("Genetics Patient Relationship {$genetics_patient_relationship->id} moved from Subject {$secondary_genetics_patient->id} to {$primary_genetics_patient->id}");
                    }
                }

                $primary_genetics_patient->relationships = array_merge($primary_genetics_patient->relationships, $primary_relationships);

            }

            $primary_genetics_patient->comments .= (!empty($primary_genetics_patient->comments) ? ", " : '') . $secondary_genetics_patient->comments;

            if ($primary_genetics_patient->save()) {
                $this->addLog("Genetics Patient comment saved");
            }

            $secondary_genetics_patient->studies = [];
            $secondary_genetics_patient->pedigrees = [];
            $secondary_genetics_patient->diagnoses = [];
            $secondary_genetics_patient->relationships = [];
            $secondary_genetics_patient->deleted = 1;
            if ($secondary_genetics_patient->save()) {
                Audit::add('Patient Merge', 'delete', "Genetics Patient id" . $secondary_genetics_patient->id . " " . PatientIdentifierHelper::getIdentifierPrompt($secondary_identifier) . ":" . PatientIdentifierHelper::getIdentifierValue($secondary_identifier));
                $this->addLog("Genetics Patient(subject) flagged as deleted (id): " . $secondary_genetics_patient->id);
            } else {
                OELog::log(print_r($secondary_genetics_patient->getErrors(), true));
            }

            return true;
        }

        //none of them genetics patients
        return true;
    }


    /**
     * Assign episodes to a new paient id.
     *
     * @param int $patientId the primary Patient Id
     * @param array of AR $episodes
     *
     * @return bool true if no error thrown
     */
    public function updateEpisodesPatientId($new_patient_id, $episodes)
    {
        foreach ($episodes as $episode) {
            $msg = 'Episode ' . $episode->id . ' moved from patient ' . $episode->patient_id . ' to ' . $new_patient_id;
            $episode->patient_id = $new_patient_id;

            if ($episode->save()) {
                $this->addLog($msg);
                Audit::add('Patient Merge', 'Episode moved patient', $msg);
            } else {
                throw new Exception('Failed to save Episode: ' . print_r($episode->errors, true));
            }
        }

        return true;
    }

    /**
     * Moving event from one episode to another.
     *
     * @param int $new_episode_id
     * @param array $events
     *
     * @return bool
     *
     * @throws Exception
     */
    public function updateEventsEpisodeId($new_episode_id, $events)
    {
        foreach ($events as $event) {
            $msg = 'Event ' . $event->id . ' moved from Episode ' . $event->episode_id . ' to ' . $new_episode_id;

            $event->episode_id = $new_episode_id;

            if ($event->save()) {
                $this->addLog($msg);
                Audit::add('Patient Merge', 'Event moved episode', $msg);
            } else {
                throw new Exception('Failed to save Event: ' . print_r($event->errors, true));
            }
        }

        return true;
    }

    /**
     * Returns the  earliest start date and the latest end date of the two episodes
     *
     * @param Episode $primary_episode
     * @param Episode $secondary_episode
     * @return array start date, end date
     */
    public function getTwoEpisodesStartEndDate(Episode $primary_episode, Episode $secondary_episode)
    {
        $start_date = ($primary_episode->start_date > $secondary_episode->start_date) ? $secondary_episode->start_date : $primary_episode->start_date;

        if (!$primary_episode->end_date || !$secondary_episode->end_date) {
            $end_date = null;
        } else {
            $end_date = ($primary_episode->end_date < $secondary_episode->end_date) ? $secondary_episode->end_date : $primary_episode->end_date;
        }

        return array($start_date, $end_date);
    }
    
    /**
    * Updating Lists including Hot list and worklist
    * @param Patient $primary_patient
    * @param Patient $secondary_patient
    *
    * @return bool
    *
    * @throws Exception
    */
    public function updateHotList(Patient $primary_patient, Patient $secondary_patient)
    {
        // Get complete hot list (including for other users)
        $hotlist_items = \UserHotlistItem::model()->findAllByAttributes(['patient_id'=>$secondary_patient->id]);

        // for each row where $secondary_patient is included
        foreach($hotlist_items as $h_item){
            // TODO: Potentiality add check for duplicates here
            // set $secondary_patient to $primary_patient
            $h_item->patient_id = $primary_patient->id;
            $h_item->user_comment = $h_item->user_comment . " Hotlist item Merged from ".$secondary_patient->id." to ".$primary_patient->id.".";
            // try to save row
            if($h_item->save()){
                $msg = 'Hotlist item for user '.$h_item->createdUser->first_name." " .$h_item->createdUser->last_name." updated. Changed patient id from ".$secondary_patient->id." to ".$primary_patient->id.".";
                $this->addLog($msg);
                Audit::add('Patient Merge', 'Hotlist item Patient updated', $msg);
            }
            else{
                // throw exception if fail
                throw new Exception('Failed to update hotlist item: '.$h_item->id.' '.print_r($h_item->errors, true));
            }            
        }
        return true;
    }

    /**
    * Updating Lists including Hot list and worklist
    * @param Patient $primary_patient
    * @param Patient $secondary_patient
    *
    * @return bool
    *
    * @throws Exception
    */
    public function updateWorkLists(Patient $primary_patient, Patient $secondary_patient)
    {       
        // Get Worklist items        
        $worklist_items = \WorklistPatient::model()->findAllByAttributes(['patient_id'=>$secondary_patient->id]);
        // for each row where $secondary_patient is included
        foreach($worklist_items as $w_item){
            // TODO: Potentiality add check for duplicates here
            // set $secondary_patient to $primary_patient                
            $w_item->patient_id = $primary_patient->id;
            // try to save row
            if($w_item->save()){
                $msg = 'Worklist item for worklist '.$w_item->worklist->name." at time ".$w_item->getScheduledTime() ." updated. Changed patient id from ".$secondary_patient->id." to ".$primary_patient->id.".";
                $this->addLog($msg);
                Audit::add('Patient Merge', 'Worklist item Patient updated', $msg);
            }
            else{
                // throw exception if fail
                throw new Exception('Failed to update worklist item: '.$w_item->id.' '.print_r($w_item->errors, true));
            }
        }
        return true;
    }
}
