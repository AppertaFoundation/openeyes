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

// phpunit --filter PatientMergeTest unit/components/PatientMergeTest.php
// 
// /var/www/openeyes/protected/tests>phpunit --filter PatientMerge unit/components/PatientMergeTest.php

class PatientMergeTest extends PHPUnit_Framework_TestCase
{
    
    private $primaryPatient = null;
    private $secondaryPatient = null;
    
    public function setUp()
    {
        
        $primaryPatient = new Patient();
        
        $primaryPatient->dob = '1981-02-24';
        $primaryPatient->gender = 'M';
        $primaryPatient->hos_num = 3423435;
        $primaryPatient->nhs_num = 99999999999;
        $primaryPatient->gp_id = 3;
        
        if ( !$primaryPatient->save(false) ){
            throw new Exception("Failed" . print_r($primaryPatient->errors, true));
        }
        
        
        $secondaryPatient = new Patient();
        
        $secondaryPatient->dob = '1981-02-24';
        $secondaryPatient->gender = 'M';
        $secondaryPatient->hos_num = 4353423;
        $secondaryPatient->gp_id = 3;
        
        if ( !$secondaryPatient->save(false) ){
            throw new Exception("Failed" . print_r($secondaryPatient->errors, true));
        }
        
        
        $this->primaryPatient = $primaryPatient;
        $this->secondaryPatient = $secondaryPatient;
    }
    
    public function testComparePatientDetails()
    {
        $mergeHandler = new PatientMerge;
        
        // these patients are created in setUp() 
        $primaryPatient = Patient::model()->findByAttributes(array('hos_num' => 3423435));
        $secondaryPatient = Patient::model()->findByAttributes(array('hos_num' => 4353423));
        
        $result = $mergeHandler->comparePatientDetails($primaryPatient, $secondaryPatient);
        
        $this->assertFalse($result['isConflict'], "Personal details should be the same at this point.");
        $this->assertEmpty($result['details']);
        
        // Change the dob and gender 
        $primaryPatient->gender = 'F';
        $primaryPatient->dob = '1981-12-21';
        
        $result = $mergeHandler->comparePatientDetails($primaryPatient, $secondaryPatient);
        
        $this->assertTrue($result['isConflict'], "Personal details should NOT be the same. Both DOB and Gender are different at this point.");
        
        $this->assertEquals($result['details'][0]['column'], 'dob');
        $this->assertEquals($result['details'][0]['primary'], '1981-12-21');
        $this->assertEquals($result['details'][0]['secondary'], '1981-02-24');
        
        $this->assertEquals($result['details'][1]['column'], 'gender');
        $this->assertEquals($result['details'][1]['primary'], 'F');
        $this->assertEquals($result['details'][1]['secondary'], 'M');
        
    }
    
    public function testUpdateEpisodesWhenPrimaryHasNoEpisodes()
    {
        $mergeHandler = new PatientMerge;
        
        $primaryPatient = Patient::model()->findByAttributes(array('hos_num' => 3423435));
        $secondaryPatient = Patient::model()->findByAttributes(array('hos_num' => 4353423));
        
        // here we are COPY Viloet's episode to the secondary patient, if you like, duplicate the episodes and assign it to the secondary patient
        $this->copyVioletCoffinEpisodes($secondaryPatient->id);
        
        $violetsEpisodes = Episode::model()->findAllByAttributes(array('patient_id' => 19434));
        $episodes = Episode::model()->findAllByAttributes(array('patient_id' => $secondaryPatient->id));

        $this->assertEquals(count($episodes), count($violetsEpisodes) );
        $this->assertEquals(count($episodes), count($secondaryPatient->episodes) );
        
        // at this pont the primary patient has no episodes and the secondary has
        // lets save the episode id to compare the m later
        $secondaryPatientEpisodeIds = array();
        foreach($secondaryPatient->episodes as $secondaryEpisode){
            $secondaryPatientEpisodeIds[$secondaryEpisode->id] = $secondaryEpisode->id;
        }
        
        // move the episodes , (secondary INTO primary)
        $result = $mergeHandler->updateEpisodes($primaryPatient, $secondaryPatient);
        
        // refresh the relation $primaryPatient->episodes
        $primaryPatient->refresh();
        
        //the episodes were copied from Violet coffin originally so we can test her count
        $this->assertEquals( count($violetsEpisodes), count($primaryPatient->episodes) );
        
        foreach($primaryPatient->episodes as $primaryEpisode){
            $this->assertTrue( in_array($primaryEpisode->id, $secondaryPatientEpisodeIds) );
        }
        
    }
    
    public function testUpdateEpisodesWhenBothHaveEpisodesNoConflict()
    {
        $mergeHandler = new PatientMerge;
        
        // primary patient  ST LOUIS-ROBERTS, Gordon	(60)  has 3 episodes that are not conflicting with Violet's episodes
        $primaryPatient = Patient::model()->findByAttributes(array('hos_num' => 3423435));
        
        $secondaryPatient = Patient::model()->findByAttributes(array('hos_num' => 4353423));
        
        // Copy Violet's Episodes to the secondary 
        $this->copyVioletCoffinEpisodes($secondaryPatient->id);
        
        $violetsEpisodes = Episode::model()->findAllByAttributes(array('patient_id' => 19434));
        $secondaryEpisodes = Episode::model()->findAllByAttributes(array('patient_id' => $secondaryPatient->id));    
        $this->assertEquals(count($secondaryEpisodes), count($violetsEpisodes) );
        $this->assertEquals(count($secondaryEpisodes), count($secondaryPatient->episodes) );
        
        /**  **/
        
        
        // lets copy ST LOUIS-ROBERTS, Gordon 's episodes to the primary patients
            
        $gordon = Patient::model()->findByPk(1985666);
        
        foreach($gordon->episodes as $gEpisode){

            $newEpisode = new Episode;
            
            $newEpisode->attributes = $gEpisode->attributes;
            $newEpisode->patient_id = $primaryPatient->id;
            
            $newEpisode->save();
        }
        
        $primaryPatient->refresh();
        $secondaryPatient->refresh();
        
        // lets check the counts of the episodes, this could be done in a more proper way but now it is just enough
        $this->assertEquals(count($gordon->episodes), count($primaryPatient->episodes) );
        
        
        /** at this point we have a primary and secondary patient with non conflicting episodes **/
        
        
        $secondaryEpisodeIds = array();
        foreach($secondaryPatient->episodes as $sEpisode){
            $secondaryEpisodeIds[$sEpisode->id] =  $sEpisode->id;
        }
        
        // move the episodes , (secondary INTO primary)
        $result = $mergeHandler->updateEpisodes($primaryPatient, $secondaryPatient);
        
        $primaryPatient->refresh();
        
        // collect the primary 
        $primaryEpisodeIds = array();
        foreach($primaryPatient->episodes as $pEpisodes){
            $primaryEpisodeIds[$pEpisodes->id] = $pEpisodes->id;
        }
        
        //lets check if the secondary episodes are assigned to the primary
        foreach($secondaryEpisodeIds as $sEpisodeId){
            if( !in_array($sEpisodeId, $primaryEpisodeIds) ){
                $this->fail("Episode $sEpisodeId not found in primary patient's episode");
            }
        }
    }
    
    public function testUpdateEpisodesWhenBothHaveEpisodesConflict()
    {
        
    }
    
    public function testUpdateLegacyEpisodes(){}
    
    public function testUpdateAllergyAssignments(){}
    
    public function testUpdateRiskAssignments(){}
    
    public function testUpdatePreviousOperations(){}
    
    public function testIsSecondaryPatientDeleted(){}
    
    public function testUpdateEpisodesPatientId(){}
    
    public function testUpdateEventsEpisodeId(){}
    
    public function testLoad(){}
    
    public function testMerge(){}
        
    
    public function tearDown()
    {
        $criteria = new CDbCriteria;
        $criteria->compare('patient_id', $this->primaryPatient->id);
        Episode::model()->deleteAll($criteria);
        
        $criteria = new CDbCriteria;
        $criteria->compare('patient_id', $this->secondaryPatient->id);
        Episode::model()->deleteAll($criteria);     
        
        $primaryPatient = Patient::model()->findByAttributes(array(
            'dob' => '1981-02-24',
            'gender' => 'M',
            'hos_num' => 3423435,
            'nhs_num' => 99999999999,
            'gp_id' => 3
        ));
        
        $primaryPatient->delete();
        
        $secondaryPatient = Patient::model()->findByAttributes(array(
            'dob' => '1981-02-24',
            'gender' => 'M',
            'hos_num' => 4353423,
            'gp_id' => 3
        ));
        
        $secondaryPatient->delete();
    }
    
    private function copyVioletCoffinEpisodes($patientId)
    {
        // violet coffin hos num :  1009465 , id : 19434
        
        //lets copy violet's episodes to out patient
        $episodes = Episode::model()->findAllByAttributes(array('patient_id' => 19434));
        
        foreach($episodes as $episode){
            
            $newEpisode = new Episode;
            
            $newEpisode->attributes = $episode->attributes;
            $newEpisode->patient_id = $patientId;
            
            $newEpisode->save();
            
            
            
        }
        
        
    }
    
}

