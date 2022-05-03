<?php

/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2021
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2021, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

namespace OEModule\PASAPI\resources;

use OEModule\PASAPI\models\PasApiAssignment;

class PatientMerge extends BaseResource
{
    /** @var int Add to the list, do not merge (will need manual confirmation and merge) */
    public const AUTO_MERGE_NEVER = 0;
    /** @var int Merge if all details (dob, gender) match, otherwise just add to the list */
    public const AUTO_MERGE_ON_MATCH = 1;
    /** @var int Merge at all times */
    public const AUTO_MERGE_ALWAYS = 2;

    private \Patient $primary_patient;
    private \Patient $secondary_patient;
    private \PatientIdentifierType $patient_identifier_type;

    protected static $resource_type = 'PatientMerge';
    protected static $model_class = \PatientMergeRequest::class;

    /** @var bool Indicates whether the resource was newly created */
    public bool $isNewResource = false;

    private function findPatient(string $identifier): \Patient
    {
        $assignment = PasApiAssignment::model()->findByResource("Patient", $identifier, \Patient::class);
        $patient = $assignment->getInternal();
        if ($patient->isNewRecord) {
            throw new \Exception("Patient does not exist locally");
        }
        /** @var \Patient $patient */
        return $patient;
    }

    /**
     * Set patient identifier type
     *
     * @param \PatientIdentifierType $patient_identifier_type
     * @return PatientMerge
     */
    public function setPatientIdentifierType(\PatientIdentifierType $patient_identifier_type): PatientMerge
    {
        $this->patient_identifier_type = $patient_identifier_type;
        return $this;
    }

    /**
     * Make sure patients exist
     *
     * @return bool True if both patients can be found
     */
    public function setAndValidatePatients(): bool
    {
        foreach (["primary", "secondary"] as $key) {
            $property_key = ucfirst($key) . "PatientNumber";
            if ($patient_number = $this->getAssignedProperty($property_key)) {
                try {
                    $this->{$key . "_patient"} = $this->findPatient($patient_number);
                } catch (\Exception $e) {
                    $this->addError("$key patient not found");
                }
            } else {
                $this->addError("$key patient number not provided");
            }
        }

        return empty($this->errors);
    }

    /**
     * Save the merge request and do the merge if possible
     *
     * @return false|int The merge request id or false on failure
     */
    public function save()
    {
        // Check if merge request exists in opposite direction
        if ($this->checkExistingOpposite()) {
            return false;
        }

        // Check if same merge request already exists
        if ($model = $this->getExisting()) {
            if ((int)$model->status === \PatientMergeRequest::STATUS_MERGED) {
                return $model->id;
            }
        } else {
            $model_class = static::$model_class;
            $model = new $model_class();
            /** @var \PatientMergeRequest $model */
            $model->primary_id = $this->primary_patient->id;
            $model->secondary_id = $this->secondary_patient->id;
            $model->primary_dob = $this->primary_patient->getDOB();
            $model->primary_gender = $this->primary_patient->gender;
            $model->secondary_dob = $this->secondary_patient->getDOB();
            $model->secondary_gender = $this->secondary_patient->gender;
            $primary_patient_identifier = \PatientIdentifierHelper::getPatientIdentifierByType($this->primary_patient->id, $this->patient_identifier_type);
            $model->primary_local_identifier_value = is_null($primary_patient_identifier) ? "" : $primary_patient_identifier->value;
            $secondary_patient_identifier = \PatientIdentifierHelper::getPatientIdentifierByType($this->secondary_patient->id, $this->patient_identifier_type);
            $model->secondary_local_identifier_value = is_null($secondary_patient_identifier) ? "" : $secondary_patient_identifier->value;
            $model->status = \PatientMergeRequest::STATUS_NOT_PROCESSED;
            if (!$model->save()) {
                foreach ($model->getErrors() as $key => $err) {
                    $this->addError("$key: $err");
                }
                return false;
            }
            $this->isNewResource = true;
        }

        /** @var \PatientMergeRequest $model */
        if ($this->getAutoMergeSetting() > self::AUTO_MERGE_NEVER) {
            $merge_handler = new \PatientMerge();
            $details_cmp = $merge_handler->comparePatientDetails($this->primary_patient, $this->secondary_patient);
            $conflicts = [];
            if ($details_cmp["is_conflict"]) {
                $conflicts = array_map(
                    function ($conflict) {
                        return $conflict["column"] . " mismatch";
                    },
                    $details_cmp["details"]
                );
            }

            if (empty($conflicts) || $this->getAutoMergeSetting() == self::AUTO_MERGE_ALWAYS) {
                $merge_handler->load($model, $this->patient_identifier_type);
                try {
                    $is_merged = $merge_handler->merge();
                    if (!$is_merged) {
                        $this->addWarning("Merge request was created but patients cannot be automatically merged.");
                        foreach ($merge_handler->getLog() as $log) {
                            $this->addWarning($log);
                        }
                    }
                } catch (\Exception $e) {
                    $this->addWarning("Merge request was created but patients cannot be automatically merged.");
                    $this->addWarning($e->getMessage());
                    foreach ($merge_handler->isMergable($this->primary_patient, $this->secondary_patient) as $conflict) {
                        $this->addWarning($conflict["message"]);
                    }
                }
                // Save merge status
                if ($is_merged) {
                    $merge_handler->addLog("Patients successfully merged");
                    $model->status = \PatientMergeRequest::STATUS_MERGED;
                } else {
                    $merge_handler->addLog("Merge failed");
                    $model->status = \PatientMergeRequest::STATUS_CONFLICT;
                }
                $model->merge_json = json_encode(array('log' => $merge_handler->getLog()));
                $model->save();
            }

            if ($this->getAutoMergeSetting() < self::AUTO_MERGE_ALWAYS) {
                // Displaying conflict messages would be misleading if
                // auto merge strategy was set to Always
                foreach ($conflicts as $conflict) {
                    $this->addWarning("Merge conflict: " . $conflict);
                }
            }
        }

        return $model->id;
    }

    private function getExisting(): ?\PatientMergeRequest
    {
        $model_class = static::$model_class;
        return $model_class::model()->findByAttributes([
            "primary_id" => $this->primary_patient->id,
            "secondary_id" => $this->secondary_patient->id,
        ]);
    }

    private function checkExistingOpposite(): bool
    {
        $model_class = static::$model_class;
        if (
            $existing = $model_class::model()->findByAttributes([
            "primary_id" =>  $this->secondary_patient->id,
            "secondary_id" => $this->primary_patient->id,
            ])
        ) {
            $this->addError("Merge request already exists in opposite direction (status: " . $existing->getStatusText() . ")");
            return true;
        }

        return false;
    }

    private function getAutoMergeSetting(): int
    {
        return (int)\SettingMetadata::model()->getSetting('pasapi_automerge');
    }
}
