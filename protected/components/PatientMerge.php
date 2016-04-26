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

class PatientMerge
{
    private $primaryPatient;
    private $secondaryPatient;
    
    /**
     * Set primary patient by id
     * @param int $id
     */
    public function setPrimaryPatientById($id){
        $this->primaryPatient = Patient::model()->findByPk($id);
    }
    
    /**
     * Returns the Primary patient
     * @return Patient AR record
     */
    public function getPrimaryPatient(){
        return $this->primaryPatient;
    }
    
    /**
     * Set secondaty patient by id
     * @param int $id
     */
    public function setSecondaryPatientById($id){
        $this->secondaryPatient = Patient::model()->findByPk($id);
    }
    
    /**
     * Returns the secondary patient
     * @return Patient AR record
     */
    public function getSecondaryPatient(){
        return $this->secondaryPatient;
    }
    
    
    
    /**
     * Load data from PatientMergeRequest AR record
     * 
     * @param PatientMergeRequest $request
     */
    public function load(PatientMergeRequest $request){
        $this->setPrimaryPatientById( $request->primary_id );
        $this->setSecondaryPatientById( $request->secondary_id );
    }
    
    /**
     * Compare data in the patient table
     * 
     * @param patient AR record $primary
     * @param patient AR record $secondary
     */
    public function comparePatientDetails(Patient $primary, Patient $secondary)
    {
        
        //columns to be compared in patient table
        $columns = array(
            'dob', 'gender', /*'hos_num',*/ 'nhs_num', 'date_of_death', 'ethnic_group_id', 'contact_id',
        );
        
        $conflict = array();
        
        foreach($columns as $column){
            if( $this->primaryPatient->$column !== $this->secondaryPatient->$column ){
                $conflict[] = array(
                    'column' => $column,
                    'primary' => $this->primaryPatient->$column,
                    'secondary' => $this->secondaryPatient->$column,
                );
            }
        }
        
        return array(
            'isConflict' => !empty($conflict),
            'details' => $conflict
        );
    }
    
    public function merge()
    {
        
        $mergeSuccess = false;
        
        $patinet_details_result = $this->comparePatientDetails($this->primaryPatient, $this->secondaryPatient);
        
        // handle episodes here, eg.: reassign, update patient ID...
        
        if( isset($patinet_details_result['isConflict']) && $patinet_details_result['isConflict'] === true){
            
            $mergeSuccess = false;
            
            //null NHS num, hos num
            
            echo "<pre>" . print_r($patinet_details_result, true) . "</pre>";
            
            die;

        } else {
            
            // no conflict
            
            // do the actual merging
                        
        }
        
        return $mergeSuccess;
    }
}
