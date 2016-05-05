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
# Episode ids : 600430, 600432, 600435, 600451, 600452, 600454

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
            'dob', 'gender', /*'hos_num', 'nhs_num', 'date_of_death', 'ethnic_group_id', 'contact_id', */
        );
        
        $conflict = array();
        
        foreach($columns as $column){
            if( $primary->$column !== $secondary->$column ){
                Yii::app()->user->setFlash("warning.merge_error_$column", "Patients have different personal details : $column");
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
    
    /**
     * Do the actual merging
     * 
     * @return boolean $isMerged success or fail
     */
    public function merge()
    {
        
        $isMerged = false;
        
        /** This is handled in the controller, we ask extra confirmation if there are conflicts in personal details **/
        // Compare personal details, now we only check DOB and Gender
        //$isPatientConflict = $this->comparePatientDetails($this->primaryPatient, $this->secondaryPatient);
        
        // Update Episode
        $isMerged = $this->updateEpisodes($this->primaryPatient, $this->secondaryPatient);

        // Update allergyAssignments
        $isMerged = $isMerged && $this->updateAllergyAssignments($this->primaryPatient->id, $this->secondaryPatient->allergyAssignments);
 
        // Updates riskAssignments
        $isMerged = $isMerged && $this->updateRiskAssignments($this->primaryPatient->id, $this->secondaryPatient->riskAssignments);
        
        // Update previousOperations
        $isMerged = $isMerged && $this->updatePreviousOperations($this->primaryPatient->id, $this->secondaryPatient->previousOperations);
        
        if($isMerged) {
            $secondaryPatient = $this->secondaryPatient;

            $secondaryPatient->deleted = 1;

            if($secondaryPatient->save()){
                Audit::add('Patient Merge', "Patient id: " . $this->secondaryPatient->id . " flagged as deleted.");
                $isMerged = $isMerged && true;
            } else {
                throw new Exception("Failed to update Patient: " . print_r($secondaryPatient->errors, true));
            }
        }
        
        return $isMerged;
    }
    
    public function updateEpisodes(Patient $primaryPatient, Patient $secondaryPatient)
    {
        $result = false;
        $primaryHasEpisodes = $primaryPatient->episodes;
        $secondaryHasEpisodes = $secondaryPatient->episodes;
        
        // if primary has no episodes than we just assign the secondary patient's episodes to the primary
        if( !$primaryHasEpisodes && $secondaryHasEpisodes){
            // this case is fine, we can assign the episodes from secondary to primary
            $result = $this->updateEpisodesPatientId($primaryPatient->id, $secondaryPatient->episodes);
                    
        } else if ( $primaryHasEpisodes && !$secondaryHasEpisodes ){
            // primary has episodes but secondary has not, nothing to do here
            $result = true;
        } else {
            // Both have episodes, we have to compare the subspecialties
            
            foreach($secondaryPatient->episodes as $secondaryEpisode){
                $secondary_subspecialty = $secondaryEpisode->getSubspecialtyID();

                foreach($primaryHasEpisodes as $primaryEpisode){
                    $primary_subspecialty = $primaryEpisode->getSubspecialtyID();

                    if( $secondary_subspecialty == $primary_subspecialty ){
                        // Both primary and secondary patient have episodes
                        $result = $result && $this->updateEventsEpisodeId($primaryEpisode->id, $secondaryEpisode);
                    }
                }
                
                $this->updateEpisodesPatientId($primaryPatient->id, $secondaryPatient->episodes);
            }
        }
    }
    
    /**
     * Updates the patient id in the Allergy Assigment
     * 
     * @param int $newPatientId Primary patient id
     * @param array of AR $allergies
     * @throws Exception AllergyAssigment cannot be saved
     */
    public function updateAllergyAssignments($newPatientId, $allergyAssignments)
    {
        foreach($allergyAssignments as $allergyAssignment){
            $msg = "AllergyAssignment " . $allergyAssignment->id ." moved from patient " . $allergyAssignment->patient_id . " to " . $newPatientId;
            $allergyAssignment->patient_id = $newPatientId;
            if( $allergyAssignment->save() ){
                Audit::add('Patient Merge', $msg);
            } else {
                throw new Exception("Failed to update AllergyAssigment: " . $allergyAssignment->id . " " . print_r($allergyAssignment->errors, true));
            }
        }
    }
    
    /**
     * Updates patient id in Risk Assignment
     * 
     * @param int $newPatientId
     * @param array of AR $risks
     * @throws Exception Failed to save RiskAssigment
     */
    public function updateRiskAssignments($newPatientId, $riskAssignments)
    {
        foreach($riskAssignments as $riskAssignment){
            $msg = "RiskAssignment " . $riskAssignment->id ." moved from patient " . $riskAssignment->patient_id . " to " . $newPatientId;
            $riskAssignment->patient_id = $newPatientId;
            if( $riskAssignment->save() ){
                Audit::add('Patient Merge', $msg);
            } else {
                throw new Exception("Failed to update RiskAssigment: " . $riskAssignment->id . " " . print_r($riskAssignment->errors, true));
            }
        }
    }
    
    public function updatePreviousOperations($newPatientId, $previousOperations)
    {
        foreach($previousOperations as $previousOperation){
            $msg = "Previous Operation " . $previousOperation->id ." moved from Patient " . $previousOperation->patient_id . " to " . $newPatientId;
            $previousOperation->patient_id = $newPatientId;
            if( $previousOperation->save() ){
                Audit::add('Patient Merge', $msg);
            } else {
                throw new Exception("Failed to update Previous Operation: " . $previousOperation->id . " " . print_r($previousOperation->errors, true));
            }
        }
    }
    
    /**
     * Assign episodes to a new paient id
     * @param int $patientId the primary Patient Id
     * @param array of AR $episodes
     * @return boolean true if no error thrown
     */
    public function updateEpisodesPatientId($newPatientId, $episodes){
        
        foreach($episodes as $episode){
            
            $msg = "Episode " . $episode->id . " moved from patient " . $episode->patient_id . " to " . $newPatientId;
            $episode->patient_id = $newPatientId;
            
            if( $episode->save() ){
                Audit::add('Patient Merge', $msg);
            } else {
                throw new Exception("Failed to save Episode: " . print_r($secondaryPatient->errors, true));
            }
        }
        
        return true;
    }

    public function updateEventsEpisodeId($newEpisodeId, $events)
    {
        foreach($events as $event){
            
            $msg = "Event " . $event->id . " moved from Episode " . $event->episode_id . " to " . $newEpisodeId;
        
            $event->episode_id = $newEpisodeId;
            
            if($event->save()){
                Audit::add('Patient Merge', $msg);
            } else {
                throw new Exception("Failed to save Event: " . print_r($event->errors, true));
            }
        }
        
        $episode = Episode::model()->findByPk($event->episode_id);
        $episode->deleted = 1;
        
        if( $episode->save() ){
            Audit::add('Patient Merge', "Episode deleted: " . $episode->id );
            return true;
        } else {
            throw new Exception("Failed to save Episode: " . print_r($episode->errors, true));
        }
    }
}
