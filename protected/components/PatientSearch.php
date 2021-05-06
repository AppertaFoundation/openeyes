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
 * @link http://www.open
 * eyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright 2017, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
class PatientSearch
{
    const HOSPITAL_NUMBER_SEARCH_PREFIX = '(H|Hosnum)\s*[:;]\s*';

    // Hospital number (assume a < 10 digit number is a hosnum)0
    const HOSPITAL_NUMBER_REGEX = '/^(H|Hosnum)\s*[:;]\s*([0-9\-]+)$/i';

    // Patient name
    const PATIENT_NAME_REGEX = '/^(?:P(?:atient)?[:;\s]+)?([a-zA-Z-]+[ ,]?[a-zA-Z-]*)(\s\d{1,2}[\/\-]\d{1,2}[\/\-]\d{2,4})?$/';

    private $searchTerms = array();

    public $use_pas = true;

    /**
     * Suppress PAS integration.
     *
     * @return PatientSearch
     */
    public function noPas()
    {
        // Clone to avoid singleton problems with use_pas flag
        $model = clone $this;
        $model->use_pas = false;

        return $model;
    }

    /**
     * Checking the search term if it is a NHS number, Hospital number or Patient name.
     *
     * @param string $term
     * @return array
     */
    public function parseTerm($term)
    {
        $term = trim($term);

        $search_terms = array(
            'hos_num' => null,
            'nhs_num' => null,
            'dob' => null,
            'first_name' => null,
            'last_name' => null,
        );

        // NHS number
        $nhs = $this->getNHSnumber($term);
        if ($nhs) {
            $search_terms['nhs_num'] = $nhs;
        }

       $hos_num = $this->getHospitalNumber($term);
       if ($hos_num) {
            $search_terms['hos_num'] = $hos_num;
       }

        // Patient name
        $name = $this->getPatientName($term);
        if (!$nhs && !$hos_num && $name) {
            $search_terms['first_name'] = trim($name['first_name']);
            $search_terms['last_name'] = trim($name['last_name']);
            $search_terms['dob'] = trim($name['dob']);
        }

        $this->searchTerms = CHtml::encodeArray($search_terms);

        return $this->searchTerms;
    }

    /**
     * Searching for patients.
     *
     * @param string $term search term
     * @return CActiveDataProvider
     */
    public function search($term)
    {
        $search_terms = $this->parseTerm($term);

        $patient = new Patient();

        $patient->hos_num = $search_terms['hos_num'];
        $patient->nhs_num = $search_terms['nhs_num'];

        // Get the valuse from URL
        $currentPage = Yii::app()->request->getParam('Patient_page');
        $pageSize = Yii::app()->request->getParam('pageSize', 20);

        // if no GET param we try to fetch the value from the $criteria, default value 0 is none of them set
        $sortDir = Yii::app()->request->getParam('sort_dir', 0);
        $sortDir = ($sortDir == 0 || $sortDir == 'asc') ? 'asc' : 'desc';

        $sortBy = Yii::app()->request->getParam('sort_by');
        switch ($sortBy) {
            case 0:
                $sortBy = 'hos_num*1';
                break;
            case 1:
                $sortBy = 'title';
                break;
            case 2:
                $sortBy = 'first_name';
                break;
            case 3:
                $sortBy = 'last_name';
                break;
            case 4:
                $sortBy = 'dob';
                break;
            case 5:
                $sortBy = 'gender';
                break;
            case 6:
                $sortBy = 'nhs_num*1';
                break;
            default:
                $sortBy = 'hos_num*1';
        }

        $patientCriteria = array(
            'pageSize' => $pageSize,
            'sortBy' => $sortBy,
            'sortDir' => $sortDir,
            'currentPage' => $currentPage,
            'first_name' => CHtml::decode($search_terms['first_name']),
            'last_name' => CHtml::decode($search_terms['last_name']),
            'dob' => CHtml::decode($search_terms['dob']),
        );

        if ( $this->use_pas == false ){
            $patient->use_pas = false;
        }

        return $patient->search($patientCriteria);
    }

    public function getSearchTerms()
    {
        return $this->searchTerms;
    }

    /**
     * Tries to fetch NHS Number from the search term.
     *
     * @param $term
     * @return string|null
     */
    public function getNHSnumber($term)
    {
        // NHS number (assume 10 digit number is an NHS number)
        $NHS_NUMBER_REGEX_1 = '/^(N|NHS)\s*[:;]\s*([0-9\- ]+)$/i';
        $NHS_NUMBER_REGEX_2 = isset(Yii::app()->params['nhs_num_length'])
            ? '/^([0-9]{' . Yii::app()->params['nhs_num_length'] . '})$/i'
            : '/^([0-9]{3}[- ]?[0-9]{3}[- ]?[0-9]{4})$/i';

        $result = null;
        if (preg_match($NHS_NUMBER_REGEX_1, $term, $matches) || preg_match($NHS_NUMBER_REGEX_2, $term, $matches)) {
            $nhs = $matches[2] ?? $matches[1];
            $nhs = str_replace(array('-', ' '), '', $nhs);
            $result = $nhs;
        }

        return $result;
    }

    /**
     * Tries to fetch Hospital Number from the search term.
     *
     * @param $term
     * @return string|null
     */
    public function getHospitalNumber($term)
    {
        $result = null;

        $unprefixed_term = strtoupper(preg_replace('/' . self::HOSPITAL_NUMBER_SEARCH_PREFIX . '/i', '', $term));

        if (preg_match(self::HOSPITAL_NUMBER_REGEX, $term, $matches) || preg_match(
                Yii::app()->params['hos_num_regex'],
                $unprefixed_term,
                $matches
            )) {
            $hosnum = $matches[2] ?? $matches[1];
            $result = sprintf(Yii::app()->params['pad_hos_num'], $hosnum);
        }

        return $result;
    }

    /**
     * Tries to fetch Patient name from the search term.
     *
     * @param string $term
     * @return string[]|null
     */
    public function getPatientName(string $term)
    {
        $result = null;
        if (preg_match(self::PATIENT_NAME_REGEX, $term, $m)) {
            $name = $m[1];
            $name = trim(preg_replace('/\s?\d[\/\-]\d[\/\-]\d/', '', $name));

            if (strpos($name, ',') !== false) {
                [$surname, $firstname] = explode(',', $name, 2);
            } elseif (strpos($name, ' ')) {
                [$firstname, $surname] = explode(' ', $name, 2);
            } else {
                $surname = $name;
                $firstname = '';
            }
            $dob = $m[2] ?? '';

            $result['first_name'] = trim($firstname);
            $result['last_name'] = trim($surname);
            $result['dob'] = trim($dob);
        }

        return $result;
    }

    /**
     * Checks if the term is a NHS number, Hospital number or Patient name.
     *
     * @param string $term
     *
     * @return bool
     */
    public function isValidSearchTerm($term)
    {
        return $this->getNHSnumber($term) || $this->getHospitalNumber($term) || $this->getPatientName($term);
    }
}
