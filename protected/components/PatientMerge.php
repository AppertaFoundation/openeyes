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

# Violet Coffin hos_num = 1009465 , patient.id = 19434
# Episode ids : 600430 600432 600435 600451 600452 600454

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
            if( $primary->$column !== $secondary->$column ){
                $conflict[] = array(
                    'column' => $column,
                    'primary' => $primary->$column,
                    'secondary' => $secondary->$column,
                );
            }
        }
        
        return array(
            'isConflict' => !empty($conflict),
            'details' => $conflict
        );
    }
    
    public function isMegeable(Patient $primaryPatient, Patient $secondaryPatient)
    {
        $episode_merge_result = $this->compareEpisodes($primaryPatient, $secondaryPatient);
        $patinet_details_result = $this->comparePatientDetails($primaryPatient, $secondaryPatient);
        
        return ($episode_merge_result && $patinet_details_result );
        
    }
    
    /**
     * Do the actual merging
     * 
     * @return boolean $isMerged success or fail
     */
    public function merge()
    {
        $isMerged = false;
        // check if we can merge, compare personal details and episodes
        $isMergeable = $this->isMegeable($this->primaryPatient, $this->secondaryPatient);
        
        if( $isMergeable ){
            
            $secondaryPatient = $this->secondaryPatient;
            
            $secondaryPatient->deleted = 1;
            
            if($secondaryPatient->save()){
                Audit::add('Patient Merge', "Patient id: " . $this->secondaryPatient->id . " flagged as deleted.");
                $isMerged = true;
            }
            
            // TODO refactor
            // have to check the what $this->secondaryPatient->episodes  returns when empty null ? empty array ?
            $secondary_episodes = $this->secondaryPatient->episodes ? $this->secondaryPatient->episodes : array();
            
            if ( $this->updateEpisodesPatientId($this->primaryPatient->id, $secondary_episodes) ){
                $isMerged = $isMerged && true;
            }

        } else {
            $isMerged = false;
        }
        
        return $isMerged;
    }
    
    /**
     * Merging the episodes of Primary and Secondary Patients
     * 
     * @param Patient $primaryPatient
     * @param Patient $secondaryPatient
     */
    public function compareEpisodes(Patient $primaryPatient, Patient $secondaryPatient)
    {
        $conflict = array();
        
        $primaryHasEpisodes = $primaryPatient->episodes;
        $secondaryHasEpisodes = $secondaryPatient->episodes;
        
        // if primary has no episodes than we just assign the secondary patient's episodes to the primary
        if( !$primaryHasEpisodes && $secondaryHasEpisodes){
            // this case is fine, we can assign the episodes from secondary to primary
        } else if ( $primaryHasEpisodes && !$secondaryHasEpisodes ){
            // primary has episodes but secondary has not, nothing to do here
        } else {
            
            foreach($secondaryHasEpisodes as $secondaryHasEpisode){
                $secondary_subspecialty = $secondaryHasEpisode->getSubspecialtyID();
                
                foreach($primaryHasEpisodes as $primaryHasEpisode){
                    $primary_subspecialty = $primaryHasEpisode->getSubspecialtyID();
                    
                    if( $secondary_subspecialty == $primary_subspecialty ){
                        // Both primary and secondary patient have episodes
                        // at this time auto mere is not supported
                        $conflict = array(
                            'type' => 'episodes conflict',
                            'message' => 'Primary and Secundary patient has the same episode subspecialty',
                            'subspecialtyID' => $secondary_subspecialty
                        );
                    }
                }
            }
        }
        
        return array(
            'isConflict' => !empty($conflict),
            'details' => $conflict
        );
    }
    
    /**
     * Assign episodes to a new paient id
     * @param type $patientId
     * @param type $episodes
     * @return boolean
     */
    public function updateEpisodesPatientId($newPatientId, $episodes){
        
        foreach($episodes as $episode){
            $episode->patient_id = $newPatientId;
            
            if( $episode->save() ){
                Audit::add('Patient Merge', "Episode " . $episode->id . " moved from patient " . $episode->patient_id . " to " . $newPatientId);
            }
        }
    }
}
