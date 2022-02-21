<?php

namespace OEModule\PASAPI\components;

use CDbCriteria;
use OEModule\PASAPI\resources\Patient;

/**
 * (C) OpenEyes Foundation, 2020
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2020, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

class PasSearchUpdateObserver extends PasApiObserver
{
    /**
     * Processing resource object, saves Patient or returns them to the referenced array - from Patient object
     *
     * @param array $resources
     * @param array $data
     * @return array
     */
    public function processPASResults($resource, $type, array $data, &$return_results) : bool
    {
        $_assignment = $resource->getAssignment();
        $_patient = $_assignment->getInternal();

        $resource->partial_record = !$_patient->isNewRecord;

        $global_number = $resource->getAssignedProperty('NHSNumber');
        $gender = strtoupper($resource->getAssignedProperty('Gender'));
        $dob = $resource->getAssignedProperty('DateOfBirth');

        $global_type = \PatientIdentifierHelper::getCurrentGlobalType();

        $criteria = new \CDbCriteria();
        $criteria->join = 'JOIN patient_identifier pi ON pi.patient_id = t.id';
        $criteria->addCondition('patient_identifier_type_id = :patient_identifier_type_id');
        $criteria->addCondition('pi.value = :value');
        $criteria->addCondition('t.dob = :dob');
        $criteria->addCondition('t.gender = :gender');
        $criteria->params[':patient_identifier_type_id'] = $global_type->id;
        $criteria->params[':value'] = $global_number;
        $criteria->params[':gender'] = $gender;
        $criteria->params[':dob'] = date('Y-m-d', strtotime($dob));

        $patient = \Patient::model()->find($criteria);
        $global_type = \PatientIdentifierHelper::getCurrentGlobalType();

        if ($patient) {
            $this->addIdentifierToPatient($patient, $type, $resource->id);

            $this->addIdentifierToPatient($patient, $global_type, $resource->getAssignedProperty('NHSNumber'));
            $return_results[] = $patient;
        } elseif ($resource->save()) {
            $assignment = $resource->getAssignment();
            $patient = $assignment->getInternal();

            // id here is the LOCAL number, set in DefaultPas.php
            $this->addIdentifierToPatient($patient, $type, $resource->id);

            // add GLOBAL

            $this->addIdentifierToPatient($patient, $global_type, $resource->getAssignedProperty('NHSNumber'));
            $resource->addGlobalNumberStatus($patient);

            if ($global_type->validateTerm($resource->getAssignedProperty('NHSNumber'))) {
                // Fire PAS request with GLOBAL number to save more LOCAL ids
                $extra_identifier_ids = $this->getExtraPatientIdentifierIds($resource); // returns local nums by type

                foreach ($extra_identifier_ids as $type_id => $extra_identifier_id) {
                    $_type = \PatientIdentifierType::model()->findByPk($type_id);
                    $this->addIdentifierToPatient($patient, $_type, $extra_identifier_id);
                }
            }

            $return_results[] = $patient;
        } elseif ($data['patient'] instanceof \Patient) {
            $data['patient']->addPasError('Patient not updated/saved from PAS, some data may be out of date or incomplete');
            \OELog::log('PASAPI Patient resource model could not be saved. Hos num: ' . $resource->id . ' ' . print_r($resource->errors, true));
            return false;
        } else {
            return false;
        }

        return false;
    }

    /**
     * Adds new GLOBAL or LOCAL identifier(number) to a patient
     *
     * @param Patient $patient
     * @param PatientIdentifierType $type
     * @param string $identifier
     * @return bool
     * @throws Exception
     */
    private function addIdentifierToPatient(\Patient $patient, \PatientIdentifierType $type, string $identifier) : bool
    {
        // validate the identifier
        $is_term_valid = $type->validateTerm($identifier);
        if (!$is_term_valid) {
            \OELog::log("An attempt was made to add identifier: {$identifier} to PatientIdentifierType id: {$type->id} but validateTerm() failed.");
            return false;
        }

        // criteria for querying active wrong identifiers
        $criteria = new CDbCriteria();
        $criteria->compare('patient_identifier_type_id', $type->id);
        $criteria->compare('patient_id', $patient->id);
        $criteria->compare('deleted', 0);
        $criteria->addCondition('value != :val');
        $criteria->params[':val'] = $identifier;
        $irrelevant_identifiers = \PatientIdentifier::model()->disableDefaultScope()->findAll($criteria);

        $success = true;

        $transaction = \Yii::app()->db->beginTransaction();
        // deactivate all wrong identifiers
        foreach ($irrelevant_identifiers as $id) {
            $id->deleted = 1;
            $id->source_info = \PatientIdentifierHelper::PATIENT_IDENTIFIER_DELETED_BY_STRING . $patient->id . '['.microtime().']';

            if (!$id->save(true, ['deleted', 'source_info'], true)) {
                \OELog::log("Patient id: '{$id->patient_id}' not added to type id {$id->patient_identifier_type_id} " . print_r($id->getErrors(), true));
                $success = false;
            }
        }

        // find matched identifier type and value
        $identifier_to_update = \PatientIdentifier::model()->disableDefaultScope()->findByAttributes([
            'patient_identifier_type_id' => $type->id,
            'value' => $identifier,
        ]);

        if ($identifier_to_update) {
            // if found match, repoint patient, activate it
            $identifier_to_update->patient_id = $patient->id;
            $identifier_to_update->deleted = 0;
            $identifier_to_update->source_info = \PatientIdentifierHelper::PATIENT_IDENTIFIER_ACTIVE_SOURCE_INFO;
        } else {
            $identifier_to_update = new \PatientIdentifier();
            $identifier_to_update->patient_id = $patient->id;
            $identifier_to_update->patient_identifier_type_id = $type->id;
            $identifier_to_update->value = $identifier;
        }
        // unsetting unique_row_str, because that column is a combination of other columns
        unset($identifier_to_update['unique_row_str']);

        if (!$identifier_to_update->save()) {
            \OELog::log("Patient id: '{$patient->id}' not added to type id {$type->id} ({$type->usage_type}) with value: {$identifier}. " . print_r($identifier_to_update->getErrors(), true));
            $success = false;
        }

        if ($success) {
            $transaction->commit();
            return true;
        } else {
            $transaction->rollback();
            return false;
        }
    }
}
