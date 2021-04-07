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


class AnyTypePatientSearchHelper implements PatientSearchHelperInterface
{

    /**
     * @var PatientIdentifierType[]
     */
    private $types = [];

    /**
     * Returns all the PatientIdentifierType attributes in stdClass
     * to be compatible with DefaultTypePatientSearchHelper
     *
     * @return stdClass[]
     */
    public function getTypes(): array
    {
        if (!$this->types) {
            $this->types = array_map(function($type) {

                $search_type_object = new \stdClass();
                foreach ($type->getAttributes() as $attribute => $value) {
                    $search_type_object->{$attribute} = $value;
                }
                return $search_type_object;
            }, PatientIdentifierType::model()->findAll());
        }

        return $this->types;
    }

    /**
     * Returns valid types for the search
     *
     * @param array $search_terms
     * @return array
     */
    public function getSearchTermsWithTypes(array $search_terms) : array
    {
        $valid_types = [];

        foreach ($this->getTypes() as $type) {
            $matches = [];
            $padded_term = sprintf($type->pad ?: '%s', $search_terms['term']);
            preg_match($type->validate_regex, $padded_term, $matches);

            $match = $matches[0] ?? null;

            if ($match) {
                $valid_types[] = [
                    'term' => $match,
                    'patient_identifier_type' => get_object_vars($type),
                ];
            }
        }

        return $valid_types;
    }
}
