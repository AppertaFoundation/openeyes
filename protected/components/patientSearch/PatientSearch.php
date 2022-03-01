<?php

/**
 * OpenEyes.
 *
 * Copyright OpenEyes Foundation, 2020
 *
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright 2020, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
class PatientSearch
{

    use ExtraLog;
    /**
     * Patient name regex
     */
    const PATIENT_NAME_REGEX = '/^([a-zA-Z]{2,}\s?[a-zA-z]{1,}\'?-?,?\s?[a-zA-Z]{2,}\s?([a-zA-Z]{1,})?)/';

    /**
     * @var array relevant information of search term by type
     *
     */
    private $search_terms = [
        'patient_identifier_value' => [],
        'first_name' => null,
        'last_name' => null,

        // the protocol user provided front of a search e.g: any:0000020
        'protocol' => null,

        // term is without the protocol (protocol stripped down)
        'term' => null,

        // original term is what the user typed in the searchbox
        'original_term' => null,
    ];

    /**
     * If PAS should be used or not
     *
     * @var bool
     */
    public $use_pas = false;

    /**
     * Holding the search helper class, either DefaultTypePatientSearchHelper or AnyTypePatientSearchHelper
     */
    public $search_helper;

    /**
     * If set user will be saved from this pas, patient_identify_type.pas_api
     *
     * @var null|int
     */
    private $save_from_pas_by_type_id = null;

    /**
     * PatientSearch constructor.
     */
    public function __construct($use_pas = false)
    {
        $this->use_pas = $use_pas;
    }

    /**
     * Suppress PAS integration.
     *
     * @return Patient
     */
    public function noPas()
    {
        // Clone to avoid singleton problems with use_pas flag
        $model = clone $this;
        $model->use_pas = false;

        return $model;
    }

    /**
     * Allow PAS integration.
     *
     * @return Patient
     */
    public function usePas()
    {
        // Clone to avoid singleton problems with use_pas flag
        $model = clone $this;
        $model->use_pas = true;

        return $model;
    }

    public function saveFromPASbyTypeId(int $patient_identifier_type_id)
    {
        $this->save_from_pas_by_type_id = $patient_identifier_type_id;
    }

    /**
     * Checking the search term if it is a NHS number, Hospital number or Patient name.
     *
     * @param string $term
     * @return array
     */
    public function parseTerm($term): array
    {
        $term = trim($term);
        $this->search_terms['original_term'] = $term;
        // we need to strip down protocol from the beginning of the term
        $this->search_terms['term'] = trim(preg_replace('/.*[:]/', '$1', $term));

        $name = $this->getPatientName($this->search_terms['term']);
        $this->search_terms['protocol'] = strtolower($this->fetchProtocol($term));
        $this->search_terms['is_name_search'] = false;

        if ($name) {
            $this->search_terms['first_name'] = trim($name['first_name']);
            $this->search_terms['last_name'] = trim($name['last_name']);
            $this->search_terms['is_name_search'] = true;
        } else {

            // remove spaces and dashes, NHS number can come like 000-000-0000, 000 000 0000
            $this->search_terms['term'] = str_replace([' ', '-'], '', $this->search_terms['term']);
        }

        $this->search_terms = CHtml::encodeArray($this->search_terms);

        $this->extraLog($this->search_terms);

        return $this->search_terms;
    }

    /**
     * Fetching protocol from search term like any:0000020
     *
     * @param $term
     * @return string
     */
    public function fetchProtocol($term): string
    {
        $split = explode(':', $term);
        return count($split) === 2 ? $split[0] : '';
    }

    /**
     * Initialize the search criteria for the patient search
     *
     * @param $term
     * @return array
     * @throws Exception (Institution::model()->getCurrent() throws)
     */
    public function prepareSearch($term, $patient_identifier_type = null): array
    {
        $this->parseTerm($term);

        // Get the valuse from URL
        $current_page = Yii::app()->request->getParam('Patient_page');
        $page_size = Yii::app()->request->getParam('pageSize', 20);

        // if no GET param we try to fetch the value from the $criteria, default value 0 is none of them set
        $sort_dir = Yii::app()->request->getParam('sort_dir', 0);
        $sort_dir = ($sort_dir == 0 || $sort_dir == 'asc') ? 'asc' : 'desc';

        $sort_by = Yii::app()->request->getParam('sort_by');
        switch ($sort_by) {
            case 0:
                $sort_by = 'value*1';
                break;
            case 1:
                $sort_by = 'title';
                break;
            case 2:
                $sort_by = 'first_name';
                break;
            case 3:
                $sort_by = 'last_name';
                break;
            case 4:
                $sort_by = 'dob';
                break;
            case 5:
                $sort_by = 'gender';
                break;
        }

        $patient_criteria = array(
            'pageSize' => $page_size,
            'sortBy' => $sort_by,
            'sortDir' => $sort_dir,
            'currentPage' => $current_page,
            'first_name' => CHtml::decode($this->search_terms['first_name']),
            'last_name' => CHtml::decode($this->search_terms['last_name']),
            'patient_identifier_value' => $this->search_terms['patient_identifier_value'],
            'terms_with_types' => [],
            'original_term' => $term,
            'term' => $this->search_terms['term'],
            'is_name_search' => $this->search_terms['is_name_search'],
        );

        // we only care about types when it is a number search
        if (!$this->search_terms['is_name_search']) {
            if ($patient_identifier_type) {
                $patient_criteria['terms_with_types'][] = [
                    'term' => $this->search_terms['term'],
                    // $type is stdClass so we fetch the actual PatientIdentifierType object
                    'patient_identifier_type' => \PatientIdentifierType::model()->findByAttributes(['unique_row_string' => $patient_identifier_type])
                ];
            } else {
                $patient_criteria['terms_with_types'] = $this->getTypesForCriteria($patient_criteria);
            }

            // fetch unique keys from the array structure
            $list_of_terms = array_map(function ($type) {
                return $type['term'];
            }, $patient_criteria['terms_with_types']);

            $patient_criteria['patient_identifier_value'] = $list_of_terms;
        } else {
            // if $patient_criteria['terms_with_types'] not set no PAS query will be performed

            $search_helper = $this->setSearchHelper($this->search_terms['protocol']);

            $patient_criteria['terms_with_types'] = [];

            if ($search_helper) {
                $types = $search_helper->getTypes();
                foreach ($types as $type) {
                    $patient_criteria['terms_with_types'][] = [
                        // $type is stdClass so we fetch the actual PatientIdentifierType object
                        'patient_identifier_type' => PatientIdentifierType::model()->findByPk($type->id)
                    ];
                }
            }
        }

        return $patient_criteria;
    }

    public function setSearchHelper($protocol = null)
    {
        $search_helper = null;
        // if protocol is 'any' and the institution has any_number_search_allowed=1
        // we need to repopulate the $this->type_display_order as we search in ->ALL<- the entries in PatientIdentifierTypeDisplayOrder
        if ($protocol === 'any') {
            if (\Institution::model()->getCurrent()->any_number_search_allowed == 1) {
                $search_helper = new \AnyTypePatientSearchHelper();
            } else {
                // at the moment getValidSearchTerm() returning "error message" if
                // user uses "any:" but the feature isn't enabled
                // but we do need to check here as well if one perform search without external validation

            }
        } else {
            $search_helper = new \DefaultTypePatientSearchHelper();
        }

        return $search_helper;
    }

    /**
     * Fetching the required types and add to the criteria array
     *
     * @param array $patient_criteria
     * @return array
     * @throws Exception
     */
    private function getTypesForCriteria(array $patient_criteria): array
    {
        $this->search_helper = $this->setSearchHelper($this->search_terms['protocol']);
        if (!$this->search_helper) {

            //this happens when the protocol is 'any' but isn't allowed in institution DB table, check setSearchHelper()
            return $patient_criteria;
        }


        // this ($patient_criteria['terms_with_types']) will be passed to the PasApiObserver, there, instances will be initialised based on this array
        // $patient_criteria goes through Patient::search() and inside search() we fire/dispatch event 'patient_search_criteria'
        $terms_with_types = $this->search_helper->getSearchTermsWithTypes($this->search_terms);
        $patient_criteria['terms_with_types'] = $terms_with_types;

        // fetch unique keys from the array structure
        $list_of_terms = array_map(function ($type) {
            return $type['term'];
        }, $terms_with_types);

        $this->search_terms['patient_identifier_value'] = array_keys(array_flip($list_of_terms));

        return $terms_with_types;
    }

    /**
     * Searching for patients.
     *
     * @param $term
     * @return CActiveDataProvider
     */
    public function search($term, $patient_identifier_type = null): CActiveDataProvider
    {
        $patient = new Patient();

        if ($this->use_pas === true) {
            $patient = $patient->usePas();
        }

        $patient_criteria = $this->prepareSearch($term, $patient_identifier_type);

        return $patient->search($patient_criteria, $this->save_from_pas_by_type_id);
    }

    /**
     * Returns an array containing the relevant search information
     *
     * @return array
     */
    public function getSearchTerms(): array
    {
        return $this->search_terms;
    }

    /**
     * Tries to fetch Patient name from the search term.
     *
     * @param $term
     * @return array
     */
    public function getPatientName($term): array
    {
        $result = [];
        if (preg_match(self::PATIENT_NAME_REGEX, $term, $m)) {
            $name = $m[1];

            if (strpos($name, ',') !== false) {
                list($surname, $firstname) = explode(',', $name, 2);
            } elseif (strpos($name, ' ')) {
                list($firstname, $surname) = explode(' ', $name, 2);
            } else {
                $surname = $name;
                $firstname = '';
            }

            $result['first_name'] = trim($firstname);
            $result['last_name'] = trim($surname);
        }

        return $result;
    }

    /**
     * Checks if the term is a valid ID based on the regex stored in patient_identifier_type.validate_regex
     *
     * @param $term
     * @return array id and type of valid terms
     * @throws Exception
     */
    public function getValidSearchTerm($term, $patient_identifier_type = null): array
    {

        //@TODO: we need to review this function
        // do we need validation ?
        // if we do does this functionality below enough and works properly ?

        // this part is redundant but this function may be called outside of this class like in
        // SiteController or PatientController
        // we may come up a better solution or just not validate the search term in every controller

        $protocol = $this->fetchProtocol($term);

        // we need to strip down protocol from the beginning of the term
        $term = trim(preg_replace('/.*[:]/', '$1', $term));

        // depends on the protocol 'any' or other/none we need to load the helper function
        if ($protocol === 'any') {
            if (\Institution::model()->getCurrent()->any_number_search_allowed == 1) {
                $this->search_helper = new \AnyTypePatientSearchHelper();
            } else {
                // 'any' protocol used but not enabled, we return false to achive
                // error messages : "any:1" is not a valid search.
                return [];
            }

        } else {
            $this->search_helper = new \DefaultTypePatientSearchHelper();
        }

        $valid_types = [];

        $name = $this->getPatientName($term);

        // all name search are valid
        if ($name) {
            $valid_types[] = $term;
        } else {
            // if not a name search we need to do some validation

            if ($patient_identifier_type) {
                $types = [];
                $types[] = \PatientIdentifierType::model()->findByAttributes(['unique_row_string' => $patient_identifier_type]);
            } else {
                $types = $this->search_helper->getTypes();
            }

            // for NHS number(or others) we support formats like 000 000 0000 and 000-000-0000
            $term = str_replace([' ', '-'], '', $term);

            foreach ($types as $type) {
                if ($type->validate_regex) {
                    $padded = sprintf($type->pad ?: '%s', $term);
                    preg_match($type->validate_regex, $padded, $matches);

                    $match = $matches[0] ?? null;

                    if ($match) {
                        $valid_types[] = $match;
                    }
                }
            }
        }

        return $valid_types;
    }
}
