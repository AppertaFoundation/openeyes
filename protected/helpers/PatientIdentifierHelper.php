<?php

/**
 * OpenEyes.
 *
 * (C) OpenEyes Foundation, 2020
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2020, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

class PatientIdentifierHelper
{
    const PATIENT_IDENTIFIER_ACTIVE_SOURCE_INFO = "ACTIVE";
    const PATIENT_IDENTIFIER_DELETED_BY_STRING = "DEL BY PATIENT ID ";
    /**
     * Returns the display order rules for the specified institution and site.
     *
     * @param $usage_type
     * @param $institution_id
     * @param null $site_id
     * @return PatientIdentifierTypeDisplayOrder[]|null
     */
    public static function getPatientIdentifierTypeDisplayOrders($usage_type, $institution_id, $site_id = null)
    {
        $cache_key = implode(':', [self::class, $usage_type, $institution_id, $site_id ?? '0']);
        $display_orders = Yii::app()->cache->get($cache_key);

        if ($display_orders === false) {
            $condition = 'patientIdentifierType.usage_type=:usage_type AND t.institution_id=:institution_id';
            $site_condition = ' AND t.site_id IS NULL';

            $criteria = new CDbCriteria();
            $criteria->order = 'display_order';
            $criteria->with = 'patientIdentifierType';
            $criteria->params = [':usage_type' => $usage_type, ':institution_id' => $institution_id];

            if ($site_id) {
                $site_condition = ' AND t.site_id=:site_id';
                $criteria->params[':site_id'] = $site_id;
            }

            $criteria->condition = $condition . $site_condition;

            $display_orders = PatientIdentifierTypeDisplayOrder::model()->findAll($criteria);

            Yii::app()->cache->set($cache_key, $display_orders, 5);
        }

        return $display_orders;
    }

    /**
     * Returns the identifier type for the specified institution and site.
     *
     * @param $usage_type
     * @param $institution_id
     * @param null $site_id
     * @return PatientIdentifierType|null
     */
    public static function getPatientIdentifierType($usage_type, $institution_id, $site_id = null): ?PatientIdentifierType
    {
        $condition = 'usage_type=:usage_type AND institution_id=:institution_id';
        $site_condition = ' AND site_id IS NULL';

        $criteria = new CDbCriteria();
        $criteria->params = [':usage_type' => $usage_type, ':institution_id' => $institution_id];

        if ($site_id) {
            $site_condition = ' AND site_id=:site_id';
            $criteria->params[':site_id'] = $site_id;
        }

        $criteria->condition = $condition . $site_condition;

        return PatientIdentifierType::model()->find($criteria);
    }

    /**
     * Returns the global institution id defined in the system setting.
     *
     * @return int|null
     */
    public static function getGlobalInstitutionIdFromSetting(): ?int
    {
        $institutions = Institution::model()->findAll('remote_id=:remote_id', [':remote_id' => SettingMetadata::model()->getSetting('global_institution_remote_id')]);
        $count = count($institutions);
        if (!$count || $count > 1) {
            return null;
        }

        return $institutions[0]->id;
    }


    /**
     * Returns the patient identified by the given value and patient identifier type.
     *
     * @param $identifier_value
     * @param $identifier_type_key
     * @return Patient|null
     */
    public static function getPatientByPatientIdentifier($identifier_value, $identifier_type_key)
    {
        $patient_identifier_type = PatientIdentifierType::model()->findByAttributes([
            'unique_row_string' => $identifier_type_key
        ]);
        if (isset($patient_identifier_type)) {
            $patient_identifier = PatientIdentifier::model()->findByAttributes([
                'value' => $identifier_value,
                'patient_identifier_type_id' => $patient_identifier_type->id
            ]);
            return $patient_identifier ? $patient_identifier->patient : null;
        } else {
            throw new exception("Patient Identifier Type for '$identifier_type_key' not found.");
        }
    }


    /**
     * Returns the identifier of the specified patient.
     *
     * @param $usage_type
     * @param $patient_id
     * @param $institution_id
     * @param null $site_id
     * @return PatientIdentifier|null
     */
    public static function getIdentifierForPatient($usage_type, $patient_id, $institution_id, $site_id = null, $disable_default_scope = false): ?PatientIdentifier
    {
        $cases = $site_id ? ['site', 'institution'] : ['institution'];
        $current_site_id = $site_id;

        $identifiers_by_type_id = array_reduce(
            PatientIdentifier::model()->with('patientIdentifierType', 'patientIdentifierStatus')->cache(3)->findAll("patient_id=:patient_id and deleted = 0", [':patient_id' => $patient_id]),
            function ($by_id, $patient_identifier) {
                $by_id[$patient_identifier->patient_identifier_type_id] = $patient_identifier;
                return $by_id;
            },
            []
        );

        foreach ($cases as $case) {
            if ($case === 'institution') {
                $current_site_id = null;
            }
            $order_rules = self::getPatientIdentifierTypeDisplayOrders($usage_type, $institution_id, $current_site_id);
            foreach ($order_rules as $rule) {
                if (array_key_exists($rule->patient_identifier_type_id, $identifiers_by_type_id)) {
                    return $identifiers_by_type_id[$rule->patient_identifier_type_id];
                }
            }
        }

        if ($usage_type === 'GLOBAL') {
            //when there are no usage code rules for GLOBAL, query id of institution using parameter: “Global Institution Name”
            $institution_id = self::getGlobalInstitutionIdFromSetting();
        }

        // when there are no usage code rules for this institution – fall back to patient identifier type
        $current_site_id = $site_id;
        foreach ($cases as $case) {
            if ($case === 'institution') {
                $current_site_id = null;
            }
            $identifier_type = self::getPatientIdentifierType($usage_type, $institution_id, $current_site_id);
            if ($identifier_type) {
                if ($disable_default_scope) {
                    $criteria = new CDbCriteria();
                    $criteria->condition = "patient_id=:patient_id AND patient_identifier_type_id=:patient_identifier_type_id";
                    $criteria->params = [':patient_id' => $patient_id, ':patient_identifier_type_id' => $identifier_type->id];

                    $identifier = PatientIdentifier::model()->disableDefaultScope()->find($criteria);
                } else {
                    $identifier = $identifiers_by_type_id[$identifier_type->id] ?? null;
                }

                if ($identifier) {
                    return $identifier;
                }
            }
        }

        return null;
    }

    /**
     * Returns the identifier of the specified patient.
     *
     * @param $usage_type
     * @param $institution_id
     * @param null $site_id
     * @return PatientIdentifier|null
     */
    public static function getMaxIdentifier($type_id): ?PatientIdentifier
    {
        $criteria = new CDbCriteria();
        $criteria->select = "*, REGEXP_REPLACE(value, '[^0-9]+', '') as numeric_value";
        $criteria->condition = "patient_identifier_type_id=:patient_identifier_type_id";
        $criteria->params = [':patient_identifier_type_id' => $type_id];
        $criteria->order = 'ABS(numeric_value) desc';
        $criteria->limit = 1; // We only want the first result.
        $identifier = PatientIdentifier::model()->find($criteria);
        if ($identifier) {
            return $identifier;
        }

        return null;
    }

    /**
     * Returns all patient's identifiers
     *
     * @param $patient_id
     * @return string
     */
    public static function getAllPatientIdentifiersForReports($patient_id)
    {
        $identifiers = '';
        $patient = Patient::model()->findByPk($patient_id);

        if ($patient) {
            foreach ($patient->identifiers as $identifier) {
                $identifiers .= $identifier->patientIdentifierType->short_title . ' (' . $identifier->patientIdentifierType->institution->short_name . '): ' . $identifier->getDisplayValue() . ', ';
            }
        }

        return $identifiers;
    }

    /**
     * Returns the default identifier prompt for the specified usage type, institution and site.
     *
     * @param $usage_type
     * @param $patient_id
     * @param $institution_id
     * @param null $site_id
     * @return PatientIdentifier|null
     */
    public static function getIdentifierDefaultPromptForInstitution($usage_type, $institution_id, $site_id = null): string
    {
        $cases = $site_id ? ['site', 'institution'] : ['institution'];
        $current_site_id = $site_id;

        foreach ($cases as $case) {
            if ($case === 'institution') {
                $current_site_id = null;
            }
            $identifier_rules = self::getPatientIdentifierTypeDisplayOrders($usage_type, $institution_id, $current_site_id);
            if (!empty($identifier_rules)) {
                return $identifier_rules[0]->patientIdentifierType->short_title;
            }
        }

        if ($usage_type === 'GLOBAL') {
            //when there are no usage code rules for GLOBAL, query id of institution using parameter: “Global Institution Name”
            $institution_id = self::getGlobalInstitutionIdFromSetting();
        }

        //if no display_order is defined, search the PatientIdentifierType for the current institution and site
        $current_site_id = $site_id;
        foreach ($cases as $case) {
            if ($case === 'institution') {
                $current_site_id = null;
            }
            $identifier_type = self::getPatientIdentifierType($usage_type, $institution_id, $current_site_id);
            if ($identifier_type) {
                return $identifier_type->short_title;
            }
        }

        return 'N/A';
    }

    /**
     * Returns the prompt (short title) for the specified identifier.
     *
     * @param PatientIdentifier|null $patient_identifier
     * @return string
     */
    public static function getIdentifierPrompt($patient_identifier = null): string
    {
        if ($patient_identifier) {
            return $patient_identifier->patientIdentifierType->short_title;
        }

        return 'N/A';
    }

    /**
     * Returns the display value for the specified identifier.
     *
     * @param PatientIdentifier|null $patient_identifier
     * @return string
     */
    public static function getIdentifierValue(PatientIdentifier $patient_identifier = null): string
    {
        if ($patient_identifier) {
            return $patient_identifier->getDisplayValue();
        }

        return 'None';
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
    public static function addNumberToPatient(\Patient $patient, \PatientIdentifierType $type, string $identifier): bool
    {
        $duplicate_identifier = \PatientIdentifier::model()->findByAttributes([
            'patient_identifier_type_id' => $type->id,
            'value' => $identifier,
            'deleted' => 0,
            'source_info' => PatientIdentifierHelper::PATIENT_IDENTIFIER_ACTIVE_SOURCE_INFO
        ]);

        $is_term_valid = $type->validateTerm($identifier);

        if ($is_term_valid) {
            if ($duplicate_identifier) {
                if ($type->usage_type == PatientIdentifierType::GLOBAL_USAGE_TYPE) {
                    $duplicate_identifier->deleted = 1;
                    $duplicate_identifier->source_info =  \PatientIdentifierHelper::PATIENT_IDENTIFIER_DELETED_BY_STRING . $patient->id . '[' . time() . ']';
                    $duplicate_identifier->save();
                } else {
                    return false;
                }
            }

            $patient_identifier = new \PatientIdentifier();
            $patient_identifier->patient_id = $patient->id;
            $patient_identifier->patient_identifier_type_id = $type->id;
            $patient_identifier->value = $identifier;

            if (!$patient_identifier->save()) {
                \OELog::log("Patient id: '{$patient->id}' not added to type id {$type->id} " . print_r($patient_identifier->getErrors(), true));
                return false;
            }
        }

        if (!$is_term_valid) {
            \OELog::log("An attempt was made to add identifier: {$identifier} to PatientIdentifierType id: {$type->id} but validateTerm() failed.");
            return false;
        }

        return true;
    }

    /**
     * Returns the current GLOBAL type
     *
     * @return PatientIdentifierHelper|null
     */
    public static function getCurrentGlobalType(): ?PatientIdentifierType
    {
        $global_institution_id = \PatientIdentifierHelper::getGlobalInstitutionIdFromSetting();
        return \PatientIdentifierHelper::getPatientIdentifierType("GLOBAL", $global_institution_id);
    }

    /**
     * Retrieve an identifier for a Patient by identifier type
     *
     * @param int $patient_id
     * @param PatientIdentifierType $id_type
     * @return PatientIdentifier|null
     */
    public static function getPatientIdentifierByType(int $patient_id, PatientIdentifierType $patient_identifier_type): ?PatientIdentifier
    {
        return PatientIdentifier::model()->findByAttributes([
            "patient_id" => $patient_id,
            "patient_identifier_type_id" => $patient_identifier_type->id
        ]);
    }

    /**
     * Retrieve a padded search term that matches patient identifier regex
     *
     * @param string $term
     * @param string $validate_regex
     * @return string $pad
     */
    public static function getPaddedTermRegexResult(string $term, string $validate_regex, string $pad = null)
    {
        if($validate_regex) {
            preg_match($validate_regex, $term, $matches);

            if (isset($matches[0])) {
                return $matches[0];
            } else {
                $padded = sprintf($pad ?: '%s', $term);
                preg_match($validate_regex, $padded, $matches);

                return $matches[0] ?? null;
            }
        }

        return null;
    }
}
