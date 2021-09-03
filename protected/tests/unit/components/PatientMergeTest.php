<?php

/**
 * OpenEyes.
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

// phpunit --filter PatientMergeTest unit/components/PatientMergeTest.php
//
// /var/www/openeyes/protected/tests>phpunit --filter PatientMerge unit/components/PatientMergeTest.php

/**
 * Class PatientMergeTest
 * @method episodes($fixtureId)
 * @method patients($fixtureId)
 * @method events($fixtureId)
 * @method genetics_patient($fixtureId)
 * @method genetics_patient_relationship($fixtureId)
 * @method genetics_patient_diagnosis($fixtureId)
 * @method genetics_patient_pedigree($fixtureId)
 * @method genetics_study_subject($fixtureId)
 * @method secondary_diagnosis($fixtureId)
 */
class PatientMergeTest extends CDbTestCase
{
    public $fixtures = array(
            'patients' => 'Patient',
            'episodes' => 'Episode',
            'events' => 'Event',
            'firms' => 'Firm',
            'service_subspecialty_assignment' => 'ServiceSubspecialtyAssignment',
            'services' => 'Service',
            'specialties' => 'Specialty',
            'secondary_diagnosis' => 'SecondaryDiagnosis',
            'disorder' => 'Disorder',
    );

    public $genetic_fixtures = array(
        'genetics_patient' => 'GeneticsPatient',
        'genetics_patient_relationship' => 'GeneticsPatientRelationship',
        'genetics_patient_diagnosis' => 'GeneticsPatientDiagnosis',
        'pedigree' => 'Pedigree',
        'genetics_patient_pedigree' => 'GeneticsPatientPedigree',
        'genetics_study_subject' => 'GeneticsStudySubject'
    );

    public function shouldTestGenetics()
    {
        // TODO: [OE-11556] fix genetics tests
        // return Yii::app()->getModule('Genetics') !== null;
        return false;
    }

    public function setUp()
    {
        if ($this->shouldTestGenetics()) {
            $this->fixtures = array_merge($this->fixtures, $this->genetic_fixtures);
        }
        parent::setUp();
    }

    /**
     * @covers PatientMerge
     */
    public function testComparePatientDetails()
    {
        $merge_handler = new PatientMerge();

        $primary_patient = $this->patients('patient7');
        $secondary_patient = $this->patients('patient8');

        $result = $merge_handler->comparePatientDetails($primary_patient, $secondary_patient);

        $this->assertTrue(is_array($result));
        $this->assertFalse($result['is_conflict'], 'Personal details should be the same at this point.');
        $this->assertEmpty($result['details']);

        // Change the dob and gender
        $primary_patient->gender = 'M';
        $primary_patient->dob = '1981-12-21';

        $primary_patient->save();

        $result = $merge_handler->comparePatientDetails($primary_patient, $secondary_patient);

        $this->assertTrue($result['is_conflict'], 'Personal details should NOT be the same. Both DOB and Gender are different at this point.');

        $this->assertEquals('dob', $result['details'][0]['column']);
        $this->assertEquals('1981-12-21', $result['details'][0]['primary']);
        $this->assertEquals('1977-03-04', $result['details'][0]['secondary']);

        $this->assertEquals('gender', $result['details'][1]['column']);
        $this->assertEquals('M', $result['details'][1]['primary']);
        $this->assertEquals('F', $result['details'][1]['secondary']);
    }

    /**
     * @covers PatientMerge
     * @throws Exception
     */
    public function testUpdateEpisodesWhenPrimaryHasNoEpisodes()
    {
        $merge_handler = new PatientMerge();

        $primary_patient = $this->patients('patient7');
        $secondary_patient = $this->patients('patient8');

        $episode7 = $this->episodes('episode7');
        $episode7->patient_id = 1;
        $episode7->save();

        $episode8 = $this->episodes('episode8');
        $episode8->patient_id = 1;
        $episode8->save();

        $primary_patient->refresh();

        // primary has no episodes
        $this->assertEquals(0, count($primary_patient->episodes));

        // at this pont the primary patient has no episodes and the secondary has

        // move the episodes , (secondary INTO primary)
        $result = $merge_handler->updateEpisodes($primary_patient, $secondary_patient);

        $this->assertTrue($result);

        $episode9 = $this->episodes('episode9');
        $this->assertEquals(7, $episode9->patient_id);

        $episode10 = $this->episodes('episode10');
        $this->assertEquals(7, $episode10->patient_id);

        $secondary_patient->refresh();

        // secondary has no episodes
        $this->assertEquals(0, count($secondary_patient->episodes));
    }

    /**
     * @covers PatientMerge
     * @throws Exception
     */
    public function testUpdateEpisodesWhenBothHaveEpisodesNoConflict()
    {
        $merge_handler = new PatientMerge();

        $primary_patient = $this->patients('patient7');
        $secondary_patient = $this->patients('patient8');

        // this episode conflicts with episode7, so assign it to a different user to avoid the conflict
        $eposode9 = $this->episodes('episode9');
        $eposode9->patient_id = 1;
        $eposode9->save();

        $secondary_patient->refresh();

        // now primary has Episode7 and Episode8
        //secondary has Episode 10

        $eposode7 = $this->episodes('episode7');
        $this->assertEquals(7, $eposode7->patient_id);

        $eposode8 = $this->episodes('episode8');
        $this->assertEquals(7, $eposode8->patient_id);

        $eposode10 = $this->episodes('episode10');
        $this->assertEquals(8, $eposode10->patient_id);

        $this->assertEquals(2, count($primary_patient->episodes));
        $this->assertEquals(1, count($secondary_patient->episodes));

        $result = $merge_handler->updateEpisodes($primary_patient, $secondary_patient);

        $this->assertTrue($result);

        $eposode7->refresh();
        $eposode8->refresh();
        $eposode10->refresh();

        $this->assertEquals(7, $eposode7->patient_id);
        $this->assertEquals(7, $eposode8->patient_id);
        $this->assertEquals(7, $eposode10->patient_id);
    }

    /**
     * @covers PatientMerge
     *
     * We have to keep the episode with greater status
     * so if the Secondary Episode has greater status we flag it as deleted
     * @throws Exception
     */
    public function testUpdateEpisodesWhenBothHaveEpisodesConflict_secondaryEpisodeHasLessStatus()
    {
        $merge_handler = new PatientMerge();

        // $primary_patient has episode7 and episode8
        $primary_patient = $this->patients('patient7'); // episode7

        // $secondary_patient has episode9, episode10
        $secondary_patient = $this->patients('patient8'); //episode9

        $episode7 = $this->episodes('episode7');
        $episode7->episode_status_id = 5;
        $episode7->start_date = date('Y-m-d', strtotime('-30 days'));
        $episode7->end_date = date('Y-m-d', strtotime('-15 days'));
        $episode7->save();

        $episode9 = $this->episodes('episode9');
        $episode9->episode_status_id = 2;
        $episode9->start_date = date('Y-m-d', strtotime('-20 days'));
        $episode9->end_date = date('Y-m-d', strtotime('-10 days'));
        $episode9->save();

        $this->assertTrue($episode7->status->order > $episode9->status->order);


        $this->assertEquals(2, count($primary_patient->episodes));
        $this->assertEquals(2, count($secondary_patient->episodes));

        // move the episodes , (secondary INTO primary)
        $result = $merge_handler->updateEpisodes($primary_patient, $secondary_patient);

        $episode7->refresh();
        $this->assertEquals(date('Y-m-d 00:00:00', strtotime('-30 days')), $episode7->start_date);
        $this->assertEquals(date('Y-m-d 00:00:00', strtotime('-10 days')), $episode7->end_date);

        $this->assertTrue($result, 'Merge result FALSE.');

        $this->assertEquals(2, count($primary_patient->episodes));

        $event16 = $this->events('event16');
        $this->assertEquals(7, $event16->episode_id); // has not changed

        $event17 = $this->events('event17');
        $this->assertEquals(7, $event17->episode_id); // has not changed

        $episode8 = $this->episodes('episode8');
        $episode8->refresh();
        $this->assertEquals(7, $episode8->patient_id); // has not changed

        $episode9 = $this->episodes('episode9');
        $episode9->refresh();
        $this->assertEquals(8, $episode9->patient_id); // will be deleted

        $event20 = $this->events('event20');
        $this->assertEquals(7, $event20->episode_id);

        $event21 = $this->events('event21');
        $this->assertEquals(7, $event21->episode_id);

        $episode10 = $this->episodes('episode10');
        $episode10->refresh();
        $this->assertEquals(7, $episode10->patient_id);

        $secondary_patient->refresh();
        $this->assertEquals(0, count($secondary_patient->episodes));

        $primary_patient->refresh();
        $this->assertEquals(3, count($primary_patient->episodes));
    }

    /**
     * @covers PatientMerge
     * @throws Exception
     */
    public function testUpdateEpisodesWhenBothHaveEpisodesConflict_primaryEpisodeHasLessStatus()
    {
        $merge_handler = new PatientMerge();

        // $primary_patient has episode7 and episode8
        $primary_patient = $this->patients('patient7');

        // $secondary_patient has episode9, episode10
        $secondary_patient = $this->patients('patient8');

        // conflicting episodes :
        // episode7 <-> episode9

        $episode7 = $this->episodes('episode7');
        $episode7->episode_status_id = 2;
        $episode7->start_date = date('Y-m-d', strtotime('-20 days'));
        $episode7->end_date = date('Y-m-d', strtotime('-10 days'));
        $episode7->save();

        $episode9 = $this->episodes('episode9');
        $episode9->episode_status_id = 5;
        $episode9->start_date = date('Y-m-d', strtotime('-30 days'));
        $episode9->end_date = null;
        $episode9->save();
        
        $this->assertTrue($episode7->status->order < $episode9->status->order);

        $this->assertEquals(2, count($primary_patient->episodes));
        $this->assertEquals(2, count($secondary_patient->episodes));

        $result = $merge_handler->updateEpisodes($primary_patient, $secondary_patient);

        $episode9->refresh();
        $this->assertEquals(date('Y-m-d 00:00:00', strtotime('-30 days')), $episode9->start_date);
        $this->assertEquals(null, $episode9->end_date);

        $this->assertTrue($result, 'Merge result FALSE.');

        $event16 = $this->events('event16');
        $this->assertEquals(9, $event16->episode_id);

        $event17 = $this->events('event17');
        $this->assertEquals(9, $event17->episode_id);

        $event20 = $this->events('event20');
        $this->assertEquals(9, $event20->episode_id);

        $event21 = $this->events('event21');
        $this->assertEquals(9, $event21->episode_id);

        $episode7->refresh();
        $this->assertEquals(0, count($episode7->events));
        
        $episode9->refresh();
        $this->assertEquals(4, count($episode9->events));

        $episode10 = $this->episodes('episode10');
        $this->assertEquals(7, $episode10->patient_id);

        $this->assertEquals(7, $episode7->patient_id);

        $episode8 = $this->episodes('episode8');
        $this->assertEquals(7, $episode8->patient_id);

        $secondary_patient->refresh();
        $this->assertEquals(0, count($secondary_patient->episodes));

        $primary_patient->refresh();
        $this->assertEquals(3, count($primary_patient->episodes));
    }

    /**
     * @covers PatientMerge
     * @throws Exception
     */
    public function testUpdateLegacyEpisodes_primaryNoLegacyEpisodes()
    {
        $merge_handler = new PatientMerge();

        // $primary_patient has episode7 and episode8
        $primary_patient = $this->patients('patient7');

        // $secondary_patient has episode9, episode10
        $secondary_patient = $this->patients('patient8');

        // Lets modify the episodes to have a legacy episode

        $episode9 = $this->episodes('episode9');

        // Case : Secondary has legacy episode, Primary doesent have
        $episode9->legacy = 1;
        $episode9->save();
        $this->assertEquals(1, $episode9->legacy);

        $primary_patient->refresh();

        $result = $merge_handler->updateLegacyEpisodes($primary_patient, $secondary_patient);

        $this->assertTrue($result, 'Merge result FALSE.');

        // test the legacy
        $episode9->refresh();
        $episode9 = $this->episodes('episode9');
        $this->assertEquals(7, $episode9->patient_id);

        $secondary_patient->refresh();
        $this->assertEquals(0, count($secondary_patient->legacyepisodes));

        $primary_patient->refresh();
        $this->assertEquals(1, count($primary_patient->legacyepisodes));
    }

    /**
     * @covers PatientMerge
     * @throws Exception
     */
    public function testUpdateLegacyEpisodes_bothHaveLegacyEpisodes_secondaryOlder()
    {
        $merge_handler = new PatientMerge();

        // $primary_patient has episode7 and episode8
        $primary_patient = $this->patients('patient7');

        // $secondary_patient has episode9, episode10
        $secondary_patient = $this->patients('patient8');

        // Lets modify the episodes to have a legacy episode

        $episode7 = $this->episodes('episode7');
        $episode9 = $this->episodes('episode9');

        // Case : Both Primary and Secondary have legacy episode, so we keep the older episode and move the events
        $episode7->legacy = 1;
        $episode7->created_date = date('Y-m-d', strtotime('-15 days'));
        $episode7->save();
        $this->assertEquals(1, $episode7->legacy);

        $episode9->legacy = 1;
        $episode9->created_date = date('Y-m-d', strtotime('-30 days'));
        $episode9->save();
        $this->assertEquals(1, $episode9->legacy);

        $this->assertTrue($episode7->created_date > $episode9->created_date);

        $result = $merge_handler->updateLegacyEpisodes($primary_patient, $secondary_patient);

        $this->assertTrue($result, 'Merge result FALSE.');

        $event16 = $this->events('event16');
        $this->assertEquals(9, $event16->episode_id);

        $event17 = $this->events('event17');
        $this->assertEquals(9, $event17->episode_id);

        $episode9 = $this->episodes('episode9');
        $episode9->refresh();
        $this->assertEquals(7, $episode9->patient_id);
        $this->assertEquals(4, count($episode9->events));

        $primary_patient->refresh();
        $this->assertEquals(1, count($primary_patient->legacyepisodes));
        $this->assertEquals(4, count($primary_patient->legacyepisodes[0]->events));

        $secondary_patient->refresh();
        $this->assertEquals(0, count($secondary_patient->legacyepisodes));
    }

    /**
     * @covers PatientMerge
     * @throws Exception
     */
    public function testUpdateLegacyEpisodes_bothHaveLegacyEpisodes_primaryOlder()
    {
        $merge_handler = new PatientMerge();

        // $primary_patient has episode7 and episode8
        $primary_patient = $this->patients('patient7');

        // $secondary_patient has episode9, episode10
        $secondary_patient = $this->patients('patient8');

        // Lets modify the episodes to have a legacy episode

        $episode7 = $this->episodes('episode7');
        $episode9 = $this->episodes('episode9');

        // Case : Both Primary and Secondary have legacy episode, so we keep the older episode and move the events
        $episode7->legacy = 1;
        $episode7->save();
        $this->assertEquals(1, $episode7->legacy);

        $episode9->legacy = 1;
        $episode9->save();
        $this->assertEquals(1, $episode9->legacy);

        $this->assertTrue($episode7->created_date < $episode9->created_date);

        $result = $merge_handler->updateLegacyEpisodes($primary_patient, $secondary_patient);

        $this->assertTrue($result, 'Merge result FALSE.');

        $event20 = $this->events('event20');
        $this->assertEquals(7, $event20->episode_id);

        $event21 = $this->events('event21');
        $this->assertEquals(7, $event21->episode_id);

        $episode7->refresh();
        $this->assertEquals(4, count($episode7->events));

        $episode9->refresh();
        $this->assertEquals(0, count($episode9->events));

        $primary_patient->refresh();
        $this->assertEquals(1, count($primary_patient->legacyepisodes));

        $secondary_patient->refresh();
        $this->assertEquals(0, count($secondary_patient->legacyepisodes));
    }

    /**
     * @covers PatientMerge
     * @throws Exception
     */
    public function testUpdateOphthalmicDiagnoses()
    {
        $merge_handler = new PatientMerge();
        
        $primary_patient = $this->patients('patient7');
        $secondary_patient = $this->patients('patient8');
        
        $secondary_diagnoses8 = $this->secondary_diagnosis('secondaryDiagnoses8');
        $secondary_diagnoses8->patient_id = 8;
        $secondary_diagnoses8->save();
        $secondary_diagnoses8->refresh();
        
        // Before we update the Ophthalmic Diagnoses we check if the patient id is equals to the secondary patient id
        $this->assertEquals(8, $secondary_diagnoses8->patient_id);
        
        $secondary_patient->refresh();
        $this->assertTrue(is_array($secondary_patient->ophthalmicDiagnoses));
        $this->assertEquals(1, count($secondary_patient->ophthalmicDiagnoses));
        
        $merge_handler->updateOphthalmicDiagnoses($primary_patient, $secondary_patient->ophthalmicDiagnoses);
        
        $secondary_diagnoses8->refresh();
        $secondary_patient->refresh();
        
        $this->assertEquals(0, count($secondary_patient->ophthalmicDiagnoses));
        
        $this->assertEquals(7, $secondary_diagnoses8->patient_id);
    }

    /**
     * @covers PatientMerge
     * @throws Exception
     */
    public function testUpdateSystemicDiagnoses()
    {
        $merge_handler = new PatientMerge();
        
        $primary_patient = $this->patients('patient7');
        $secondary_patient = $this->patients('patient8');
        
        $secondary_diagnoses8 = $this->secondary_diagnosis('secondaryDiagnoses8');
        $secondary_diagnoses8->patient_id = 8;
        $secondary_diagnoses8->disorder_id = 5;
        $secondary_diagnoses8->save();
        $secondary_diagnoses8->refresh();

        // Before we update the Ophthalmic Diagnoses we check if the patient id is equals to the secondary patient id
        $this->assertEquals(8, $secondary_diagnoses8->patient_id);
        $this->assertEquals(5, $secondary_diagnoses8->disorder_id);
        
        $secondary_patient->refresh();
        $this->assertTrue(is_array($secondary_patient->systemicDiagnoses));
        $this->assertEquals(1, count($secondary_patient->systemicDiagnoses));
        
        $merge_handler->updateOphthalmicDiagnoses($primary_patient, $secondary_patient->systemicDiagnoses);
        
        $secondary_diagnoses8->refresh();
        
        $this->assertEquals(7, $secondary_diagnoses8->patient_id);
        
        $this->assertEquals(0, count($secondary_patient->systemicDiagnoses));
        $this->assertEquals(1, count($primary_patient->systemicDiagnoses));
    }

    /**
     * @covers PatientMerge
     * @throws Exception
     */
    public function testUpdateGenetics_Primary_not_genetics()
    {
        if (!$this->shouldTestGenetics()) {
            $this->markTestSkipped('Genetics module needs to be enabled for this test.');
        }
        $merge_handler = new PatientMerge();

        $primary_patient = $this->patients('patient3'); // is not a genetics patient
        $secondary_patient = $this->patients('patient2'); // it is a genetics patient

        $genetics_primary_patient = null;
        $genetics_secondary_patient = $this->genetics_patient('genetics_patient2');

        $this->assertEquals($secondary_patient->id, $genetics_secondary_patient->patient_id);

        $merge_handler->updateGenetics($primary_patient, $secondary_patient);

        // as the primary is not a genetics user we can just update the Geneticspatient->patient_id attribute

        $genetics_secondary_patient->refresh();
        $this->assertEquals($primary_patient->id, $genetics_secondary_patient->patient_id);

    }

    /**
     * If secondary patient is not a genetics patient we have nothing to do here
     */
    /**
     * @covers PatientMerge
     */
    public function testUpdateGenetics_Secondary_not_genetics(){
        if (!$this->shouldTestGenetics()) {
            $this->markTestSkipped('Genetics module needs to be enabled for this test.');
        }
        $this->markTestIncomplete('Not been written yet');
    }

    /**
     * @covers PatientMerge
     */
    public function testUpdateGenetics_Both_are_genetics()
    {
        if (!$this->shouldTestGenetics()) {
            $this->markTestSkipped('Genetics module needs to be enabled for this test.');
        }
        $merge_handler = new PatientMerge();

        $primary_patient = $this->patients('patient1');
        $secondary_patient = $this->patients('patient2');

        $genetics_primary_patient_model = GeneticsPatient::model()->findByPk($primary_patient->id);
        $genetics_secondary_patient_model = GeneticsPatient::model()->findByPk($secondary_patient->id);

        $genetics_primary_patient = $this->genetics_patient('genetics_patient1');
        $genetics_secondary_patient = $this->genetics_patient('genetics_patient2');

        $this->assertEquals($primary_patient->id, $genetics_primary_patient->patient_id);
        $this->assertEquals($secondary_patient->id, $genetics_secondary_patient->patient_id);

        $this->assertEquals(1, count($genetics_primary_patient_model->relationships));
        $this->assertEquals(1, count($genetics_primary_patient_model->diagnoses));

        $this->assertEquals(1, count($genetics_primary_patient_model->pedigrees));
        $this->assertEquals(1, count($genetics_secondary_patient_model->pedigrees));

        $merge_handler->updateGenetics($primary_patient, $secondary_patient);

        // genetics relation
        $genetics_primary_patient_model->refresh();
        $this->assertEquals(2, count($genetics_primary_patient_model->relationships));

        $relation1 = $this->genetics_patient_relationship('genetics_patient_relationship1');
        $relation2 = $this->genetics_patient_relationship('genetics_patient_relationship2');

        $this->assertEquals($genetics_primary_patient->id, $relation1->patient_id); // this patient id is actually the genetics_patient.id
        $this->assertEquals($genetics_primary_patient->id, $relation2->patient_id);

        // genetics diagnosis
        $this->assertEquals(2, count($genetics_primary_patient_model->diagnoses));

        $diagnoses1 = $this->genetics_patient_diagnosis('genetics_patient_diagnosis1');
        $diagnoses2 = $this->genetics_patient_diagnosis('genetics_patient_diagnosis2');

        $this->assertEquals($genetics_primary_patient->id, $diagnoses1->patient_id); // this patient id is actually the genetics_patient.id
        $this->assertEquals($genetics_primary_patient->id, $diagnoses2->patient_id);

        //pedigree
        $pedigree1 = $this->genetics_patient_pedigree('genetics_patient_pedigree1');
        $this->assertEquals($genetics_primary_patient->id, $pedigree1->patient_id); // this patient id is actually the genetics_patient.id

        //$pedigree2 is null for some reason
        //$pedigree2 = $this->genetics_patient_pedigree('genetics_patient_pedigree2');
        //$this->assertEquals(1, $pedigree2->patient_id);

        //study
        $subject1 = $this->genetics_study_subject('genetics_study_subject1');
        $this->assertEquals($genetics_primary_patient->id, $subject1->subject_id);

        $subject2 = $this->genetics_study_subject('genetics_study_subject2');
        $this->assertEquals($genetics_primary_patient->id, $subject2->subject_id);

    }

    /**
     * @covers PatientMerge
     */
    public function testGetTwoEpisodesStartEndDate()
    {
        $merge_handler = new PatientMerge();

        $episode7 = $this->episodes('episode7');
        $episode7->start_date = date('Y-m-d', strtotime('-30 days'));
        $episode7->end_date = date('Y-m-d', strtotime('-15 days'));
        $episode7->save();

        $episode9 = $this->episodes('episode9');
        $episode9->start_date = date('Y-m-d', strtotime('-20 days'));
        $episode9->end_date = date('Y-m-d', strtotime('-10 days'));
        $episode9->save();

        list($start_date, $end_date) = $merge_handler->getTwoEpisodesStartEndDate($episode7, $episode9);

        $this->assertEquals($episode7->start_date, $start_date);
        $this->assertEquals($episode9->end_date, $end_date);

        /******/

        $episode7->start_date = date('Y-m-d', strtotime('-20 days'));
        $episode7->save();

        $episode9->start_date = date('Y-m-d', strtotime('-30 days'));
        $episode9->save();

        list($start_date, $end_date) = $merge_handler->getTwoEpisodesStartEndDate($episode7, $episode9);

        $this->assertEquals($episode9->start_date, $start_date);
        $this->assertEquals($episode9->end_date, $end_date);

        /******/

        $episode7->end_date = null;
        $episode7->save();

        list($start_date, $end_date) = $merge_handler->getTwoEpisodesStartEndDate($episode7, $episode9);

        $this->assertEquals($episode9->start_date, $start_date);
        $this->assertEquals(null, $end_date);

        /******/

        $episode7->end_date = date('Y-m-d', strtotime('-15 days'));
        $episode7->save();

        $episode9->end_date = null;
        $episode9->save();

        list($start_date, $end_date) = $merge_handler->getTwoEpisodesStartEndDate($episode7, $episode9);

        $this->assertEquals($episode9->start_date, $start_date);
        $this->assertEquals(null, $end_date);

        /******/

        $episode7->end_date = date('Y-m-d', strtotime('-10 days'));
        $episode7->save();

        $episode9->end_date = date('Y-m-d', strtotime('-15 days'));
        $episode9->save();

        list($start_date, $end_date) = $merge_handler->getTwoEpisodesStartEndDate($episode7, $episode9);

        $this->assertEquals($episode9->start_date, $start_date);
        $this->assertEquals($episode7->end_date, $end_date);
    }
}
