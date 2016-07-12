<?php
/**
 * OpenEyes
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2008-2011, Moorfields Eye Hospital NHS Foundation Trust
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */

class PatientSearch 
{
    
    // NHS number (assume 10 digit number is an NHS number)
    const NHS_NUMBER_REGEX_1 = '/^(N|NHS)\s*[:;]\s*([0-9\- ]+)$/i';
    const NHS_NUMBER_REGEX_2 = '/^([0-9]{3}[- ]?[0-9]{3}[- ]?[0-9]{4})$/i';
    
    // Hospital number (assume a < 10 digit number is a hosnum)
    const HOSPITAL_NUMBER_REGEX = '/^(H|Hosnum)\s*[:;]\s*([0-9\-]+)$/i';
       
    // Patient name
    const PATIENT_NAME_REGEX = '/^(?:P(?:atient)?[:;\s]*)?([\a-zA-Z-]+[ ,]?[\a-zA-Z-]*)$/';
    
    private $searchTerms = array();
    
    /**
     * Checking the search term if it is a NHS number, Hospital number or Patient name
     * @param string $term
     */
    public function parseTerm($term)
    {
        $term = trim($term);
        
        $search_terms = array(
            'hos_num' => null,
            'nhs_num' => null,
            'first_name' => null,
            'last_name' => null,
        );

        // NHS number
        if( $nhs = $this->getNHSnumber($term) ){
            $search_terms['nhs_num'] = $nhs;
            
        // Hospital number (assume a < 10 digit number is a hosnum)
        } else if( $hos_num = $this->getHospitalNumber($term) ){
            $search_terms['hos_num'] = $hos_num;
            
        // Patient name
        } else if( $name = $this->getPatientName($term) ){
            
            $search_terms['first_name'] = trim($name['first_name']);
            $search_terms['last_name'] = trim($name['last_name']);
        }
        
        $this->searchTerms = CHtml::encodeArray($search_terms);
        
        return $this->searchTerms;
    }
    
    /**
     * Searching for patients
     * 
     * @param string $term search term
     */
    public function search($term)
    {
        $search_terms = $this->parseTerm($term);

        $model = new Patient();
        
        $model->hos_num = $search_terms['hos_num'];
        $model->nhs_num = $search_terms['nhs_num'];
        
        // Get the valuse from URL
        $currentPage = Yii::app()->request->getParam('currentPage');
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
            'sortDir'=> $sortDir,
            'first_name' => CHtml::decode($search_terms['first_name']),
            'last_name' => CHtml::decode($search_terms['last_name']),
        );
        
        $dataProvider = $model->search($patientCriteria);
        
        return $dataProvider;
        
    }
    
    public function getSearchTerms(){
        return $this->searchTerms;
    }
    
    /**
     * Tries to fetch NHS Number from the search term 
     * @param array|null $result
     */
    public function getNHSnumber($term)
    {
        $result = null;
        if(preg_match(self::NHS_NUMBER_REGEX_1, $term, $matches) || preg_match(self::NHS_NUMBER_REGEX_2, $term, $matches)) {
            $nhs = (isset($matches[2])) ? $matches[2] : $matches[1];
            $nhs = str_replace(array('-',' '),'',$nhs);
            $result = $nhs;
        }
        
        return $result;
    }
    
     /**
     * Tries to fetch Hospital Number from the search term 
     * @param array|null $result
     */
    public function getHospitalNumber($term)
    {
        $result = null;
        if(preg_match(self::HOSPITAL_NUMBER_REGEX, $term,$matches) || preg_match(Yii::app()->params['hos_num_regex'], $term, $matches)) {        
            $hosnum = (isset($matches[2])) ? $matches[2] : $matches[1];
            $result = sprintf('%07s', $hosnum);
        }
        
        return $result;
    }
    
    /**
     * Tries to fetch Patient name from the search term 
     * @param array|null $result
     */
    public function getPatientName($term)
    {
        $result = null;
        if (preg_match(self::PATIENT_NAME_REGEX, $term, $m)) {
                $name = $m[1];

                if (strpos($name, ',') !== false) {
                    list ($surname, $firstname) = explode(',', $name, 2);
                } else if(strpos($name, ' ')) {
                    list ($firstname, $surname) = explode(' ', $name, 2);
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
     * Checks if the term is a NHS number, Hospital number or Patient name
     * @param string $term
     * @return boolean
     */
    public function isValidSearchTerm($term)
    {
        if( $this->getNHSnumber($term) || $this->getHospitalNumber($term) || $this->getPatientName($term) ){
            return true;
        }
        return false;
    }
    
    
    
}