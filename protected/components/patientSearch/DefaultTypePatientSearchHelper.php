<?php
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

class DefaultTypePatientSearchHelper implements PatientSearchHelperInterface
{
    /**
     * @var PatientIdentifierType[]
     */
    private $types = [];

    /**
     * Returns all the PatientIdentifierType
     *
     * @param bool $refresh
     * @return PatientIdentifierType[]
     * @throws Exception
     */
    public function getTypes(bool $refresh = false): array
    {
        if (!$this->types || $refresh) {
            $this->types = [];
            foreach (['GLOBAL', 'LOCAL'] as $usage_type) {
                // types are stdClasses
                $types = $this->getTypesByUsageType($usage_type);
                $this->types = array_merge($this->types, $types);
            }
        }

        return $this->types;
    }

    /**
     * Creates standard class based on PatientIdentifierType and PatientIdentifierTypeDisplayOrder
     * the output is a standard class having attributes from both models so
     * it will be easier to handle information later
     *
     * @param PatientIdentifierType $type
     * @param PatientIdentifierTypeDisplayOrder $patient_identifier_display_order_entry
     * @return stdClass
     */
    private function createSearchTypeObject(\PatientIdentifierType $type, \PatientIdentifierTypeDisplayOrder $patient_identifier_display_order_entry = null): stdClass
    {
        $search_type_object = new \stdClass();
        foreach ($type->getAttributes() as $attribute => $value) {
            $search_type_object->{$attribute} = $value;
        }

        $search_type_object->search_protocol_prefix = $patient_identifier_display_order_entry ? $patient_identifier_display_order_entry->getSearchProtocols() : [];

        // if no patient_identifier_display_order_entry provided than the type is searchable
        $search_type_object->searchable = $patient_identifier_display_order_entry->searchable ?? true;

        return $search_type_object;
    }

    /**
     * Returns PatientIdentifierType based on usage type
     *
     * @param string $usage_type
     * @return PatientIdentifierType[]
     * @throws Exception
     */
    public function getTypesByUsageType(string $usage_type): array
    {
        $types = [];

        $institution_id = \Institution::model()->getCurrent()->id;
        $site_id = \Yii::app()->session['selected_site_id'];

        $orders = \PatientIdentifierHelper::getPatientIdentifierTypeDisplayOrders($usage_type, $institution_id, $site_id);

        if (!$orders) {
            $orders = \PatientIdentifierHelper::getPatientIdentifierTypeDisplayOrders($usage_type, $institution_id, null);
        }

        if ($orders) {
            $types = array_map(function ($order) {
                return $this->createSearchTypeObject($order->patientIdentifierType, $order);
            }, $orders);
        } else {

            if ($usage_type === 'GLOBAL') {
                // use global institution id from settings to query the type
                $institution_global_id = null;
                $institutions = Institution::model()->findAll('remote_id=:remote_id', [':remote_id' => SettingMetadata::model()->getSetting('global_institution_remote_id')]);
                $count = count($institutions);
                if ($count === 1) {
                    $institution_global_id = $institutions[0]->id;
                }

                if ($institution_global_id) {
                    $global_type = \PatientIdentifierHelper::getPatientIdentifierType($usage_type, $institution_global_id);

                    if ($global_type) {
                        $types[] = $this->createSearchTypeObject($global_type);
                    }
                }

            } elseif ($usage_type === 'LOCAL') {
                // use current institution/site
                $type = \PatientIdentifierHelper::getPatientIdentifierType($usage_type, $institution_id, $site_id);

                if (!$type) {
                    $type = \PatientIdentifierHelper::getPatientIdentifierType($usage_type, $institution_id, null);
                }

                if ($type) {
                    $types[] = $this->createSearchTypeObject($type);
                }
            }
        }

        // type is stdClass[]
        return $types;
    }

    /**
     * Returns valid types for the search
     *
     * @param array $search_terms
     * @return array
     * @throws Exception
     */
    public function getSearchTermsWithTypes(array $search_terms): array
    {
        $protocol = $search_terms['protocol'];

        // type here is an stdClass, created in function createSearchTypeObject()
        $types = $this->getTypes();

        $valid_types = $this->searchByTypes($types, $search_terms['term'], $protocol);

        return $valid_types;
    }

    /**
     * Loops over stdClass $types and calls a function to decide if the type is searchable
     *
     * @param $types
     * @param $name
     * @param $term
     * @param $protocol
     * @return array
     */
    private function searchByTypes($types, $term, $protocol): array
    {
        $valid_types = [];

        // $types are stdClasses created in createSearchTypeObject()
        foreach ($types as $type) {
            $is_protocol_searchable = in_array($protocol, $type->search_protocol_prefix) || !$protocol;
            $result = $this->_search($type, $term, $type->searchable, $is_protocol_searchable);

            if ($result) {
                $valid_types[] = $result;
            }
        }

        return $valid_types;
    }

    /**
     * Decides if one particular type is searchable based on several condition
     *
     * @param $type
     * @param $name
     * @param $term
     * @param $is_type_searchable
     * @param $is_protocol_searchable
     * @return array
     */
    private function _search($type, $term, $is_type_searchable, $is_protocol_searchable): array
    {
        $matches = [];
        $padded_term = sprintf($type->pad ?: '%s', $term);
        preg_match($type->validate_regex, $padded_term, $matches);

        $match = $matches[0] ?? null;

        if ($match) {
            if (($is_type_searchable) &&
                // $is_protocol_searchable: only in protocol space (restricts only if protocol provided by user)
                $is_protocol_searchable) {
                return [
                    'term' => $match,
                    // $type here is an stdClass, created in function createSearchTypeObject()
                    'patient_identifier_type' => \PatientIdentifierType::model()->findByPk($type->id),
                ];
            }
        } elseif ($type->usage_type === 'LOCAL') {
            // We need to check if it's a global number
            $institution_global_id = null;
            $institutions = Institution::model()->findAll('remote_id=:remote_id', [':remote_id' => SettingMetadata::model()->getSetting('global_institution_remote_id')]);

            $count = count($institutions);
            if ($count === 1) {
                $institution_global_id = $institutions[0]->id;
            }

            if ($institution_global_id) {
                $global_type = \PatientIdentifierHelper::getPatientIdentifierType('GLOBAL', $institution_global_id);
            }

            if (isset($global_type)) {
                $padded_term = sprintf($global_type->pad ?: '%s', $term);
                preg_match($global_type->validate_regex, $padded_term, $matches);
                $match = $matches[0] ?? null;

                if ($match) {
                    return [
                        'term' => $match,
                        // $type here is an stdClass, created in function createSearchTypeObject()
                        'patient_identifier_type' => \PatientIdentifierType::model()->findByPk($type->id),
                        'is_global_search' => true
                    ];
                }
            }
        }

        return [];
    }
}
