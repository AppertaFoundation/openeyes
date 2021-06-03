<?php

namespace OEModule\PASAPI\components;

use OEModule\PASAPI\resources\BaseResource;
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

class PasApiObserver
{
    use \ExtraLog;
    /**
     * If PAS available in general - can be switch off from params to disable PASAPI
     * @var bool
     */
    private bool $available = false;

    /**
     * @var int
     */
    private int $save_patient_from_pas_type_id;

    public function __construct()
    {
        $this->isAvailable();
    }

    /**
     * Performs PAS searches and saves or returns Patient object
     *
     * @param $data
     * @return bool
     */
    public function search($data)
    {
        $this->extraLog($data);

        if (!$this->isAvailable()) {
            $this->extraLog("PAS is not available");
            return false;
        }

        $return_results = &$data['results'];

        // Non existing patient will be saved from this PAS if there are multiple patient.
        // Happens when user click on a patient on patient result screen -> means, selects a user and a type
        // he/she wants to work with
        $this->save_patient_from_pas_type_id = (int)$data['params']['save_from_pas_by_type_id'] ?? 0;

        $pas_results = [];
        foreach ($data['params']['terms_with_types'] ?? [] as $terms_with_type) {

            $type = \PatientIdentifierType::model()->findByPk($terms_with_type['patient_identifier_type']['id']);
            $pas = $this->initPas($type);

            if (!$pas) {
                $this->extraLog("No PAS for patient identifier type id: {$type->id}");
                continue;
            }
            $this->extraLog("PAS configured for patient identifier type id: {$type->id} | {$type->long_title}");

            $request_data = [
                // this can be hos_num, nhs_num, or any patient number (value)
                // based on $type (in DefaultPas.php) we will determinate what we need to send (hos_num or nhs_num)
                'patient_identifier_value' => $terms_with_type['term'],
                'patient_identifier_type_id' => $type->id,

                // if name is set we will search by that - btw, in theory patient_identifier_value and name can't be set
                // at the same time
                'last_name' => $data['lastname'] ?? '',
                'first_name' => $data['first_name'] ?? '',
                'dob' => $data['dob'] ?? '',

                // we return error message via patient model
                'patient' => $data['patient']
            ];

            if (!$pas->isAvailable() || !$pas->isPASqueryRequired($request_data)) {
                continue;
            }

            $this->extraLog("PAS is available and isPASqueryRequired is true");

            $results = $pas->request($request_data);

            if ($results) {
                // results by type
                $pas_results[$type->id] = $results;
            }
        }

        // $pas_results is an array of protected/modules/PASAPI/resources/Patient.php by type
        if ($pas_results) {

            $this->extraLog($pas_results);

            $count = count($pas_results, COUNT_RECURSIVE) - count($pas_results);
            if(isset($data['local_results_count'])) {
                $count += $data['local_results_count'];
            }

            foreach ($pas_results as $type_id => $pas_results_by_type) {

                if ($this->save_patient_from_pas_type_id && $this->save_patient_from_pas_type_id != $type_id) {
                    continue;
                }
                foreach ($pas_results_by_type as $pas_result) {

                    $assignment = $pas_result->getAssignment();
                    $model = $assignment->getInternal();
                    // we do not care about Patients already in OE DB
                    if (!$model->isNewRecord) {

                        // check if the patient belongs to the "correct" type
                        // as different types can have the same number
                        $criteria = new \CDbCriteria();
                        $criteria->addCondition('patient_id = :patient_id');
                        $criteria->addCondition('patient_identifier_type_id = :type_id');
                        $criteria->addCondition('value = :value');
                        $criteria->params[':patient_id'] = $model->id;
                        $criteria->params[':type_id'] = $type_id;
                        $criteria->params[':value'] = $data['params']['term'];

                        $patient_count = \PatientIdentifier::model()->count($criteria);

                        if ($patient_count) {
                            continue;
                        } else {
                            // The XML's patient is not saved, the but our patient $model points to a different
                            // patient as in the pasapi_assignment we do not check patient_identifier_type
                            // so we just reset the assignment to an empty one and save the new patient
                            $new_assignment = $assignment->getNewAssignment('Patient', $pas_result->id, 'Patient');
                            $pas_result->setAssignment($new_assignment); // which is empty
                        }
                    }

                    $type = \PatientIdentifierType::model()->findByPk($type_id);
                    if (($this->save_patient_from_pas_type_id == $type->id && $pas_result->id == $data['params']['term']) || $count === 1) {
                        // patient selected to save this Patient from this Type, we do not care about any other result
                        // this does Patient saving or adding Patient Identifier
                        $this->processPASResults($pas_result, $type, $data , $return_results);

                        // do not return any data, OE Patient::search will find
                        return;
                    } else {
                        $return_results[] = $this->buildPatientObject($pas_result, $type);
                    }
                }
            }

        } else {
            $this->extraLog("No PAS results.");
        }
        // returns data to Patient::search with referenced $data['results']
    }

    /**
     * "Merging" patient resources based on some criteria
     *
     * @param \Patient[]
     * @return \Patient[]
     */
    public function mergeResults(array $patients) : array
    {
        $patient_result = [];

        $if_patient_already_added = function($identifier_value, $dob, $gender) use ($patient_result) : bool {
            if (isset($patient_result["{$identifier_value}{$dob}{$gender}"])) {
                return true;
            }
            return false;
        };

        foreach($patients as $patient) {

            if ($patient->globalIdentifier) {

                // Patient resource returns Y-m-d H:i:s
                $dob = date("Ymd", strtotime($patient->dob));
                $key = "{$patient->globalIdentifier->value}{$dob}{$patient->gender}";
                if ($if_patient_already_added($patient->globalIdentifier->value, $patient->dob, $patient->gender)) {
                    // add local numbers to existing(in array) patient
                    if ($patient->localIdentifiers) {
                        $patient_result[$key]->localIdentifiers = array_merge($patient_result[$key]->localIdentifiers, $patient->localIdentifiers);
                    }
                } else {
                    $patient_result[$key] = $patient;
                }

            } else {
                // this should not happen actually, but just in case
                $patient_result[] = $patient;
            }
        }

        return $patient_result;
    }

    public function getExtraPatientIdentifierIds(Patient $resource) : array
    {
        // After we saved a new Patient we do query PASes with GLOBAL number as well
        // to save additional local numbers

        $global_number = $resource->getAssignedProperty('NHSNumber');
        $pas_results = [];

        // fetch the newly created Patient from resource
        $assignment = $resource->getAssignment();
        $patient = $assignment->getInternal();

        foreach (\PatientIdentifierType::model()->findAll() as $type) {

            $pas = $this->initPas($type);

            if (!$pas) {
                continue;
            }
        }

        $extra_identifiers_by_type = [];
        if ($pas_results) {
            foreach ($pas_results as $type_id => $pas_results_by_type) {
                foreach ($pas_results_by_type as $_resource) {
                    $_patient = $this->buildPatientObject($_resource, \PatientIdentifierType::model()->findByPk($type_id));

                    // cross check patient
                    // DOB and genders should match, we searched by GLOBAL number so that should match as well
                    if ($patient->dob === date("Y-m-d", strtotime($_patient->dob)) && $patient->gender === $_patient->gender) {
                        $extra_identifiers_by_type[$type_id] = $_resource->id;
                    }
                }
            }
        }

        return $extra_identifiers_by_type;
    }

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

        if($patient) {
            \PatientIdentifierHelper::addNumberToPatient($patient, $type, $resource->id);
            $return_results[] = $patient;
        } elseif ($resource->save()) {

            $assignment = $resource->getAssignment();
            $patient = $assignment->getInternal();

            // id here is the LOCAL number, set in DefaultPas.php
            \PatientIdentifierHelper::addNumberToPatient($patient, $type, $resource->id);

            // add GLOBAL
            $global_type = \PatientIdentifierHelper::getCurrentGlobalType();
            \PatientIdentifierHelper::addNumberToPatient($patient, $global_type, $resource->getAssignedProperty('NHSNumber'));
            $resource->addGlobalNumberStatus($patient);

            // Fire PAS request with GLOBAL number to save more LOCAL ids
            $extra_identifier_ids = $this->getExtraPatientIdentifierIds($resource); // returns local nums by type

            foreach ($extra_identifier_ids as $type_id => $extra_identifier_id) {
                $_type = \PatientIdentifierType::model()->findByPk($type_id);
                \PatientIdentifierHelper::addNumberToPatient($patient, $_type, $extra_identifier_id);
            }

            $return_results[] = $patient;

        } elseif($data['patient'] instanceof \Patient) {
            $data['patient']->addPasError('Patient not updated/saved from PAS, some data may be out of date or incomplete');
            \OELog::log('PASAPI Patient resource model could not be saved. Hos num: ' . $resource->id . ' ' . print_r($resource->errors, true));
            return false;
        } else {
            return false;
        }

        return true;
    }

    /**
     * Returns the PAS class based on the $type's config
     *
     * @param \PatientIdentifierType $type
     * @return \BasePAS|null
     */
    public function initPas(\PatientIdentifierType $type)
    {
        if ($type->pas_api && $type->pas_api['enabled'] === true) {
            $class_name = $type->pas_api['class'] ?? 'DefaultPas';
            $pas_class_name = "\\OEModule\\PASAPI\components\\Pases\\$class_name";
            $pas_class = new $pas_class_name($type->pas_api);
            $pas_class->setType($type);
            $pas_class->init($type->pas_api);

            return $pas_class;
        }

        return null;
    }

    /**
     * Build Patient Object from XML without saving
     *
     * @param Patient $resource
     * @param \PatientIdentifierType $type
     * @return \Patient
     */
    private function buildPatientObject(\OEModule\PASAPI\resources\Patient $resource, \PatientIdentifierType $type) : \Patient
    {
        $patient = new \Patient();

        $contact = new \Contact();
        $patient->contact = $contact;

        $global_type = \PatientIdentifierHelper::getCurrentGlobalType();
        $global_number = $resource->getAssignedProperty('NHSNumber');

        $identifiers = [];
        foreach (['GLOBAL', 'LOCAL'] as $usage_type) {

            $patient_identifier = new \PatientIdentifier();

            if ($usage_type === 'GLOBAL') {
                $patient_identifier->patient_identifier_type_id = $global_type->id;
                $patient_identifier->patientIdentifierType = $global_type;
                $patient_identifier->value = $global_number;
                $patient->globalIdentifier = $patient_identifier;

                $identifiers[] = $patient_identifier;
            }

            if ($usage_type === 'LOCAL') {
                $patient_identifier->patient_identifier_type_id = $type->id;
                $patient_identifier->patientIdentifierType = $type;
                $patient_identifier->value = $resource->id;
                $patient->localIdentifiers = [$patient_identifier];

                $identifiers[] = $patient_identifier;
            }
        }

        $patient->identifiers = $identifiers;

        $resource->assignProperty($patient, 'dob', 'DateOfBirth');
        $resource->assignProperty($patient, 'date_of_death', 'DateOfDeath');
        $resource->assignProperty($patient, 'is_deceased', 'IsDeceased');
        $resource->assignProperty($patient, 'gender', 'Gender');

        $resource->assignProperty($contact, 'title', 'Title');
        $resource->assignProperty($contact, 'first_name', 'FirstName');
        $resource->assignProperty($contact, 'last_name', 'Surname');
        $resource->assignProperty($contact, 'primary_phone', 'TelephoneNumber');
        $resource->addGlobalNumberStatus($patient);

        return $patient;
    }

    /**
     * Is PAS enabled in general
     * Turning off means no PAS connection will be made doesn't matter how many PAS instances are configured
     *
     * @return bool
     */
    public function isAvailable() : bool
    {
        $enabled = (isset(\Yii::app()->params['pasapi']['enabled']) && \Yii::app()->params['pasapi']['enabled'] === true);
        return $this->available = $enabled;
    }
}
