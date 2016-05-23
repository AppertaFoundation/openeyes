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
    /**
     * @var Patient AR
     */
    private $primaryPatient;
    
    /**
     * @var Patient AR
     */
    private $secondaryPatient;
    
    /**
     * @var array
     */
    private $log = array();
    
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
    
    public function addLog($msg){
        $this->log[] = $msg;
    }
    
    /**
     * Returns the log messages
     * 
     * @return type
     */
    public function getLog(){
        return $this->log;
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
     * @return array
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
     * Do the actual merging by calling separated functions to move episodes, events etc...
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
        
        // Update legacy episodes
        $isMerged = $isMerged && $this->updateLegacyEpisodes($this->primaryPatient, $this->secondaryPatient);

        // Update allergyAssignments
        $isMerged = $isMerged && $this->updateAllergyAssignments($this->primaryPatient, $this->secondaryPatient);
 
        // Updates riskAssignments
        $isMerged = $isMerged && $this->updateRiskAssignments($this->primaryPatient->id, $this->secondaryPatient->riskAssignments);
        
        // Update previousOperations
        $isMerged = $isMerged && $this->updatePreviousOperations($this->primaryPatient->id, $this->secondaryPatient->previousOperations);
        
        if($isMerged) {
            $secondaryPatient = $this->secondaryPatient;

            $secondaryPatient->deleted = 1;

            if($secondaryPatient->save()){
                $msg = "Patient hos_num: " . $this->secondaryPatient->hos_num . " flagged as deleted.";
                $this->addLog($msg);
                Audit::add('Patient Merge', $msg);
                $isMerged = $isMerged && true;
            } else {
                throw new Exception("Failed to update Patient: " . print_r($secondaryPatient->errors, true));
            }
        }
        
        return $isMerged;
    }
    
  
    
    public function updateEpisodes(Patient $primaryPatient, Patient $secondaryPatient)
    {
        $primaryHasEpisodes = $primaryPatient->episodes;
        $secondaryHasEpisodes = $secondaryPatient->episodes;
        
        // if primary has no episodes than we just assign the secondary patient's episodes to the primary
        if( !$primaryHasEpisodes && $secondaryHasEpisodes){
            // this case is fine, we can assign the episodes from secondary to primary
            $this->updateEpisodesPatientId($primaryPatient->id, $secondaryPatient->episodes);

        } else if ( $primaryHasEpisodes && !$secondaryHasEpisodes ){
            // primary has episodes but secondary has not, nothing to do here

        } else {
            // Both have episodes, we have to compare the subspecialties
            
            foreach($secondaryPatient->episodes as $secondaryEpisode){
                $secondary_subspecialty = $secondaryEpisode->getSubspecialtyID();

                $isSameSubspecialty = false;
                foreach($primaryHasEpisodes as $primaryEpisode){
                    $primary_subspecialty = $primaryEpisode->getSubspecialtyID();

                    if( $secondary_subspecialty == $primary_subspecialty ){
                        
                        /** We need to keep the older episode so we compare the dates **/ 
                        
                        if( $primaryEpisode->created_date < $secondaryEpisode->created_date ){
                            // the primary episode is older than the secondary so we move the events from the Secondary into the Primary
                            $this->updateEventsEpisodeId($primaryEpisode->id, $secondaryEpisode->events);
                            
                            // after all events are moved we flag the secondary episode as deleted
                            $secondaryEpisode->deleted = 1;
                            if( $secondaryEpisode->save()){
                                $msg = "Episode " . $secondaryEpisode->id . " marked as deleted, events moved under the primary patient's same firm episode.";
                                $this->addLog($msg);
                                Audit::add('Patient Merge', $msg);
                            } else {
                                throw new Exception("Failed to update Episode: " . $secondaryEpisode->id . " " . print_r($secondaryEpisode->errors, true));
                            }
                            
                        } else {
                            
                            // the secondary episode is older than the primary so we move the events from the Primary into the Secondary
                            $this->updateEventsEpisodeId($secondaryEpisode->id, $primaryEpisode->events);

                            /** BUT do not forget we have to delete the primary episode AND move the secondary episode to the primary patient **/
                            $primaryEpisode->deleted = 1;
                     
                            if( $primaryEpisode->save()){
                                $msg = "Episode " . $primaryEpisode->id . " marked as deleted, events moved under the secondary patient's same firm episode.";
                                $this->addLog($msg);
                                Audit::add('Patient Merge', $msg);
                            } else {
                                throw new Exception("Failed to update Episode: " . $primaryEpisode->id . " " . print_r($primaryEpisode->errors, true));
                            }
                                                        
                            //then we move the episode to the pri1mary
                            $this->updateEpisodesPatientId($primaryPatient->id, array($secondaryEpisode));
                        }
                        
                        $isSameSubspecialty = true;
                    }
                }
                
                // if there is no conflict we still need to move the secondary episode to the primary patient
                if( !$isSameSubspecialty ){
                    $this->updateEpisodesPatientId($primaryPatient->id, array($secondaryEpisode));
                } else {
                    // there was a conflict and the episode was already moved in the foreach above
                }
            }
        }
        
        // if the save() functions not throwing errors than we can just return true
        return true;
    }
    
    public function updateLegacyEpisodes($primaryPatient, $secondaryPatient)
    {
        // if the secondary patient has legacy episodes
        if( $secondaryPatient->legacyepisodes ){
            
            // if primary patient doesn't have legacy episode we can just update the episode's patient_id to assign it to the primary patient
            if( !$primaryPatient->legacyepisodes ){

                // Patient can have only one legacy episode
                $legacyEpisode = $secondaryPatient->legacyepisodes[0];

                $legacyEpisode->patient_id = $primaryPatient->id;
                if( $legacyEpisode->save() ){
                    $msg = "Legacy Episode " . $legacyEpisode->id . " moved from patient " . $secondaryPatient->id . " to " . $primaryPatient->id;
                    $this->addLog($msg);
                    Audit::add('Patient Merge', $msg);
                } else {
                    throw new Exception("Failed to update (legacy) Episode: " . $legacyEpisode->id . " " . print_r($legacyEpisode->errors, true));
                }

            } else {
                
                $primaryLegacyEpisode = $primaryPatient->legacyepisodes[0];
                $secondaryLegacyEpisode = $secondaryPatient->legacyepisodes[0];
                
                if( $primaryLegacyEpisode->created_date < $secondaryLegacyEpisode->created_date ){
                    // we move the events from the secondaty patient's legacy episod to the primary patient's legacy epiode
                    $this->updateEventsEpisodeId($primaryLegacyEpisode->id, $secondaryLegacyEpisode->events);

                    // Flag secondary patient's legacy episode deleted as it will be empty
                    
                    $secondaryLegacyEpisode->deleted = 1;
                    if( $secondaryLegacyEpisode->save()){
                        $msg = "Legacy Episode " . $secondaryLegacyEpisode->id . "marked as deleted, events moved under the primary patient's same firm episode.";
                        $this->addLog($msg);
                        Audit::add('Patient Merge', $msg);
                    } else {
                        throw new Exception("Failed to update (legacy) Episode: " . $secondaryLegacyEpisode->id . " " . print_r($secondaryLegacyEpisode->errors, true));
                    }
                } else {
                    // in this case the secondary legacy episode is older than the primary
                    // so move the primary legacy episode's events to the secondary legacy episode
                    // then move the secondary legacy episode to the Primary patient
                    // then flag the primary's legacy episode as deleted // as only 1 legacy episode can be assigned to the patient
                    
                    $this->updateEventsEpisodeId($secondaryLegacyEpisode->id, $primaryLegacyEpisode->events);
                    
                    $primaryLegacyEpisode->deleted = 1;
                     
                    if( $primaryLegacyEpisode->save()){
                        $msg = "Legacy Episode " . $primaryLegacyEpisode->id . "marked as deleted, events moved under the secondary patient's same firm episode.";
                        $this->addLog($msg);
                        Audit::add('Patient Merge', $msg);
                    } else {
                        throw new Exception("Failed to update (legacy) Episode: " . $primaryLegacyEpisode->id . " " . print_r($primaryLegacyEpisode->errors, true));
                    }
                    
                    //then we move the episode to the pri1mary
                    $this->updateEpisodesPatientId($primaryPatient->id, array($secondaryLegacyEpisode));
                }
            }
        }
        
        // if the save() functions not throwing errors than we can just return true
        return true;
    }
    
    /**
     * Updates the patient id in the Allergy Assigment
     * 
     * @param int $newPatientId Primary patient id
     * @param array of AR $allergies
     * @throws Exception AllergyAssigment cannot be saved
     */
    public function updateAllergyAssignments($primaryPatient, $secondaryPatient)
    {
        $primaryAssignments = $primaryPatient->allergyAssignments;
        $secondaryAssignments = $secondaryPatient->allergyAssignments;
        
        if( !$primaryAssignments && $secondaryAssignments ){
            
            foreach($secondaryAssignments as $allergyAssignment){
                $msg = "AllergyAssignment " . $allergyAssignment->id ." moved from patient " . $allergyAssignment->patient_id . " to " . $primaryPatient->id;
                $allergyAssignment->patient_id = $primaryPatient->id;
                if( $allergyAssignment->save() ){
                    $this->addLog($msg);
                    Audit::add('Patient Merge', $msg);
                } else {
                    throw new Exception("Failed to update AllergyAssigment: " . $allergyAssignment->id . " " . print_r($allergyAssignment->errors, true));
                }
            }
        } else if( $primaryAssignments && $secondaryAssignments ){
            
            foreach($secondaryAssignments as $secondaryAssignment){
                
                $sameAssignment = false;
                foreach($primaryAssignments as $primaryAssignment)
                {
                    if( $primaryAssignment->allergy_id ==  $secondaryAssignment->allergy_id){
                        // the allergy is already present in the primary patient's record so we just update the 'comment' and 'other' fields
                        
                        $sameAssignment = true;
                        
                        $comments = $primaryAssignment->comments . " ; " . $secondaryAssignment->comments;
                        $other = $primaryAssignment->other . " ; " . $secondaryAssignment->other;
          
                        $primaryAssignment->comments = $comments;
                        $primaryAssignment->other = $other;
                        
                        if( $primaryAssignment->save() ){
                            $msg = "AllergyAssignment 'comments' and 'other' updated";
                            $this->addLog($msg);
                            Audit::add('Patient Merge', $msg);
                        } else {
                            throw new Exception("Failed to update AllergyAssigment: " . $primaryAssignment->id . " " . print_r($primaryAssignment->errors, true));
                        }
                        
                        // as we just copied the comments and other fields we remove the assignment
                        $secondaryAssignment->delete();
                        
                    }
                }
                
                // This means we have to move the assignment from secondary to primary
                if(!$sameAssignment){
                    $secondaryAssignment->patient_id = $primaryPatient->id;
                    if( $secondaryAssignment->save() ){
                        $msg = "AllergyAssignment " . $secondaryAssignment->id ." moved from patient " . $secondaryPatient->id . " to " . $primaryPatient->id;
                        $this->addLog($msg);
                        Audit::add('Patient Merge', $msg);
                    } else {
                        throw new Exception("Failed to update AllergyAssigment: " . $allergyAssignment->id . " " . print_r($allergyAssignment->errors, true));
                    }
                }   
            }
        }
        
        return true;
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
                $this->addLog($msg);
                Audit::add('Patient Merge', $msg);
            } else {
                throw new Exception("Failed to update RiskAssigment: " . $riskAssignment->id . " " . print_r($riskAssignment->errors, true));
            }
        }
        
        return true;
    }
    
    public function updatePreviousOperations($newPatientId, $previousOperations)
    {
        foreach($previousOperations as $previousOperation){
            $msg = "Previous Operation " . $previousOperation->id ." moved from Patient " . $previousOperation->patient_id . " to " . $newPatientId;
            $previousOperation->patient_id = $newPatientId;
            if( $previousOperation->save() ){
                $this->addLog($msg);
                Audit::add('Patient Merge', $msg);
            } else {
                throw new Exception("Failed to update Previous Operation: " . $previousOperation->id . " " . print_r($previousOperation->errors, true));
            }
        }
        
        return true;
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
                $this->addLog($msg);
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
                $this->addLog($msg);
                Audit::add('Patient Merge', $msg);
            } else {
                throw new Exception("Failed to save Event: " . print_r($event->errors, true));
            }
        }
        
        return true;
    }
}
