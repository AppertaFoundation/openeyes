<?php
/**
 * OpenEyes.
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2008-2011, Moorfields Eye Hospital NHS Foundation Trust
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */

// phpunit --filter PatientMergeTest unit/components/PatientMergeTest.php
// 
// /var/www/openeyes/protected/tests>phpunit --filter PatientMerge unit/components/PatientMergeTest.php

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
            'patient_allergy_assignment' => 'patientAllergyAssignment',
    );

    public function setUp()
    {
        parent::setUp();
    }

    public function testComparePatientDetails()
    {
        $mergeHandler = new PatientMerge();

        $primaryPatient = $this->patients('patient7');
        $secondaryPatient = $this->patients('patient8');

        $result = $mergeHandler->comparePatientDetails($primaryPatient, $secondaryPatient);

        $this->assertTrue(is_array($result));
        $this->assertFalse($result['isConflict'], 'Personal details should be the same at this point.');
        $this->assertEmpty($result['details']);

        // Change the dob and gender 
        $primaryPatient->gender = 'M';
        $primaryPatient->dob = '1981-12-21';

        $primaryPatient->save();

        $result = $mergeHandler->comparePatientDetails($primaryPatient, $secondaryPatient);

        $this->assertTrue($result['isConflict'], 'Personal details should NOT be the same. Both DOB and Gender are different at this point.');

        $this->assertEquals($result['details'][0]['column'], 'dob');
        $this->assertEquals($result['details'][0]['primary'], '1981-12-21');
        $this->assertEquals($result['details'][0]['secondary'], '1977-01-01');

        $this->assertEquals($result['details'][1]['column'], 'gender');
        $this->assertEquals($result['details'][1]['primary'], 'M');
        $this->assertEquals($result['details'][1]['secondary'], 'F');
    }

    public function testUpdateEpisodesWhenPrimaryHasNoEpisodes()
    {
        $mergeHandler = new PatientMerge();

        $primaryPatient = $this->patients('patient7');
        $secondaryPatient = $this->patients('patient8');

        $episode7 = $this->episodes('episode7');
        $episode7->patient_id = 1;
        $episode7->save();

        $episode8 = $this->episodes('episode8');
        $episode8->patient_id = 1;
        $episode8->save();

        $primaryPatient->refresh();

        // primary has no episodes
        $this->assertEquals(count($primaryPatient->episodes), 0);

        // at this pont the primary patient has no episodes and the secondary has

        // move the episodes , (secondary INTO primary)
        $result = $mergeHandler->updateEpisodes($primaryPatient, $secondaryPatient);

        $this->assertTrue($result);

        $episode9 = $this->episodes('episode9');
        $this->assertEquals($episode9->patient_id, 7);

        $episode10 = $this->episodes('episode10');
        $this->assertEquals($episode10->patient_id, 7);

        $secondaryPatient->refresh();

        // secondary has no episodes
        $this->assertEquals(count($secondaryPatient->episodes), 0);
    }

    public function testUpdateEpisodesWhenBothHaveEpisodesNoConflict()
    {
        $mergeHandler = new PatientMerge();

        $primaryPatient = $this->patients('patient7');
        $secondaryPatient = $this->patients('patient8');

        // this episode conflicts with episode7, so assign it to a different user to avoid the conflict
        $eposode9 = $this->episodes('episode9');
        $eposode9->patient_id = 1;
        $eposode9->save();

        $secondaryPatient->refresh();

        // now primary has Episode7 and Episode8
        //secondary has Episode 10

        $eposode7 = $this->episodes('episode7');
        $this->assertEquals($eposode7->patient_id, 7);

        $eposode8 = $this->episodes('episode8');
        $this->assertEquals($eposode8->patient_id, 7);

        $eposode10 = $this->episodes('episode10');
        $this->assertEquals($eposode10->patient_id, 8);

        $this->assertEquals(2, count($primaryPatient->episodes));
        $this->assertEquals(1, count($secondaryPatient->episodes));

        $result = $mergeHandler->updateEpisodes($primaryPatient, $secondaryPatient);

        $this->assertTrue($result);

        $eposode7->refresh();
        $eposode8->refresh();
        $eposode10->refresh();

        $this->assertEquals($eposode7->patient_id, 7);
        $this->assertEquals($eposode8->patient_id, 7);
        $this->assertEquals($eposode10->patient_id, 7);
    }

    /**
     * We have to keep the newer/most recent episode
     * so if the Secondary Episode is the older one than we flag it as deleted
     */
    public function testUpdateEpisodesWhenBothHaveEpisodesConflict_secondaryEpisodeOlder()
    {
        $mergeHandler = new PatientMerge();

        // $primaryPatient has episode7 and episode8
        $primaryPatient = $this->patients('patient7'); // episode7

        // $secondaryPatient has episode9, episode10
        $secondaryPatient = $this->patients('patient8'); //episode9

        $episode7 = $this->episodes('episode7');
        $episode7->created_date = date('Y-m-d', strtotime('-15 days'));
        $episode7->save();

        $episode9 = $this->episodes('episode9');
        $episode9->created_date = date('Y-m-d', strtotime('-30 days'));
        $episode9->save();

        // conflicting episodes :
        // episode7 <-> episode9

        $this->assertTrue($episode7->created_date > $episode9->created_date);

        $this->assertEquals(count($primaryPatient->episodes), 2);
        $this->assertEquals(count($secondaryPatient->episodes), 2);

        // move the episodes , (secondary INTO primary)
        $result = $mergeHandler->updateEpisodes($primaryPatient, $secondaryPatient);

        $this->assertTrue($result, 'Merge result FALSE.');

        // The conflicting episodes:
        // episode9 created 30 days ago and the episode7 created 15 days ago
        // as we keep the older episode we move events from episode7 to episode9 than move episode9 to Patient7

        $this->assertEquals(count($primaryPatient->episodes), 2);

        $event16 = $this->events('event16');
        $this->assertEquals($event16->episode_id, 7);

        $event17 = $this->events('event17');
        $this->assertEquals($event17->episode_id, 7);

        $episode8 = $this->episodes('episode8');
        $episode8->refresh();
        $this->assertEquals($episode8->patient_id, 7); // has not changed

        $episode9 = $this->episodes('episode9');
        $episode9->refresh();
        $this->assertEquals($episode9->patient_id, 8); // will be deleted

        $event20 = $this->events('event20');
        $this->assertEquals($event20->episode_id, 7);

        $event21 = $this->events('event21');
        $this->assertEquals($event21->episode_id, 7);

        $episode10 = $this->episodes('episode10');
        $episode10->refresh();
        $this->assertEquals($episode10->patient_id, 7);

        $secondaryPatient->refresh();
        $this->assertEquals(count($secondaryPatient->episodes), 0);

        $primaryPatient->refresh();
        $this->assertEquals(count($primaryPatient->episodes), 3);
    }

    public function testUpdateEpisodesWhenBothHaveEpisodesConflict_primaryEpisodeOlder()
    {
        $mergeHandler = new PatientMerge();

        // $primaryPatient has episode7 and episode8
        $primaryPatient = $this->patients('patient7');

        // $secondaryPatient has episode9, episode10
        $secondaryPatient = $this->patients('patient8');

        // conflicting episodes :
        // episode7 <-> episode9

        $episode7 = $this->episodes('episode7');
        $episode9 = $this->episodes('episode9');
        $this->assertTrue($episode7->created_date < $episode9->created_date);

        $this->assertEquals(count($primaryPatient->episodes), 2);
        $this->assertEquals(count($secondaryPatient->episodes), 2);

        $result = $mergeHandler->updateEpisodes($primaryPatient, $secondaryPatient);

        $this->assertTrue($result, 'Merge result FALSE.');

        $event16 = $this->events('event16');
        $this->assertEquals($event16->episode_id, 9);

        $event17 = $this->events('event17');
        $this->assertEquals($event17->episode_id, 9);

        $event20 = $this->events('event20');
        $this->assertEquals($event20->episode_id, 9);

        $event21 = $this->events('event21');
        $this->assertEquals($event21->episode_id, 9);

        $episode7->refresh();
        $this->assertEquals(count($episode7->events), 0);

        $episode10 = $this->episodes('episode10');
        $this->assertEquals($episode10->patient_id, 7);

        $this->assertEquals($episode7->patient_id, 7);

        $episode8 = $this->episodes('episode8');
        $this->assertEquals($episode8->patient_id, 7);

        $secondaryPatient->refresh();
        $this->assertEquals(count($secondaryPatient->episodes), 0);

        $primaryPatient->refresh();
        $this->assertEquals(count($primaryPatient->episodes), 3);
    }

    public function testUpdateLegacyEpisodes_primaryNoLegacyEpisodes()
    {
        $mergeHandler = new PatientMerge();

        // $primaryPatient has episode7 and episode8
        $primaryPatient = $this->patients('patient7');

        // $secondaryPatient has episode9, episode10
        $secondaryPatient = $this->patients('patient8');

        // Lets modify the episodes to have a legacy episode

        $episode7 = $this->episodes('episode7');
        $episode9 = $this->episodes('episode9');

        // Case : Secondary has legacy episode, Primary doesent have
        $episode9->legacy = 1;
        $episode9->save();
        $this->assertEquals(1, $episode9->legacy);

        $primaryPatient->refresh();

        $result = $mergeHandler->updateLegacyEpisodes($primaryPatient, $secondaryPatient);

        $this->assertTrue($result, 'Merge result FALSE.');

        // test the legacy
        $episode9->refresh();
        $episode9 = $this->episodes('episode9');
        $this->assertEquals($episode9->patient_id, 7);

        $secondaryPatient->refresh();
        $this->assertEquals(count($secondaryPatient->legacyepisodes), 0);

        $primaryPatient->refresh();
        $this->assertEquals(count($primaryPatient->legacyepisodes), 1);
    }

    public function testUpdateLegacyEpisodes_bothHaveLegacyEpisodes_secondaryOlder()
    {
        $mergeHandler = new PatientMerge();

        // $primaryPatient has episode7 and episode8
        $primaryPatient = $this->patients('patient7');

        // $secondaryPatient has episode9, episode10
        $secondaryPatient = $this->patients('patient8');

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

        $result = $mergeHandler->updateLegacyEpisodes($primaryPatient, $secondaryPatient);

        $this->assertTrue($result, 'Merge result FALSE.');

        $event16 = $this->events('event16');
        $this->assertEquals($event16->episode_id, 9);

        $event17 = $this->events('event17');
        $this->assertEquals($event17->episode_id, 9);

        $episode9 = $this->episodes('episode9');
        $episode9->refresh();
        $this->assertEquals($episode9->patient_id, 7);
        $this->assertEquals(count($episode9->events), 4);

        $primaryPatient->refresh();
        $this->assertEquals(count($primaryPatient->legacyepisodes), 1);
        $this->assertEquals(count($primaryPatient->legacyepisodes[0]->events), 4);

        $secondaryPatient->refresh();
        $this->assertEquals(count($secondaryPatient->legacyepisodes), 0);
    }

    public function testUpdateLegacyEpisodes_bothHaveLegacyEpisodes_primaryOlder()
    {
        $mergeHandler = new PatientMerge();

        // $primaryPatient has episode7 and episode8
        $primaryPatient = $this->patients('patient7');

        // $secondaryPatient has episode9, episode10
        $secondaryPatient = $this->patients('patient8');

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

        $result = $mergeHandler->updateLegacyEpisodes($primaryPatient, $secondaryPatient);

        $this->assertTrue($result, 'Merge result FALSE.');

        $event20 = $this->events('event20');
        $this->assertEquals($event20->episode_id, 7);

        $event21 = $this->events('event21');
        $this->assertEquals($event20->episode_id, 7);

        $episode7->refresh();
        $this->assertEquals(count($episode7->events), 4);

        $episode9->refresh();
        $this->assertEquals(count($episode9->events), 0);

        $primaryPatient->refresh();
        $this->assertEquals(count($primaryPatient->legacyepisodes), 1);

        $secondaryPatient->refresh();
        $this->assertEquals(count($secondaryPatient->legacyepisodes), 0);
    }

    public function testUpdateAllergyAssignments_primaryHasNoAllergyAssignments()
    {
        $mergeHandler = new PatientMerge();

        $assignment1 = $this->patient_allergy_assignment('assignment1');
        $assignment2 = $this->patient_allergy_assignment('assignment2');

        $assignment1->patient_id = 1;
        $assignment1->save();

        $primaryPatient = $this->patients('patient7');
        $secondaryPatient = $this->patients('patient8');

        $this->assertEquals(count($primaryPatient->allergyAssignments), 0);
        $this->assertEquals(count($secondaryPatient->allergyAssignments), 1);

        $result = $mergeHandler->updateAllergyAssignments($primaryPatient, $secondaryPatient);

        $primaryPatient->refresh();
        $secondaryPatient->refresh();

        $this->assertTrue($result, 'Update Allergy Assigmant FAILED.');

        $assignment2->refresh();
        $this->assertEquals($assignment2->patient_id, 7);

        $this->assertEquals(count($primaryPatient->allergyAssignments), 1);
        $this->assertEquals(count($secondaryPatient->allergyAssignments), 0);
    }

    public function testUpdateAllergyAssignments_bothHaveDifferentAllergyAssignments()
    {
        $mergeHandler = new PatientMerge();

        $assignment1 = $this->patient_allergy_assignment('assignment1');
        $assignment2 = $this->patient_allergy_assignment('assignment2');

        $primaryPatient = $this->patients('patient7');
        $secondaryPatient = $this->patients('patient8');

        $this->assertEquals(count($primaryPatient->allergyAssignments), 1);
        $this->assertEquals(count($secondaryPatient->allergyAssignments), 1);

        $result = $mergeHandler->updateAllergyAssignments($primaryPatient, $secondaryPatient);

        $primaryPatient->refresh();
        $secondaryPatient->refresh();

        $this->assertTrue($result, 'Update Allergy Assigmant FAILED.');

        $assignment1->refresh();
        $this->assertEquals($assignment1->patient_id, 7);

        $assignment2->refresh();
        $this->assertEquals($assignment2->patient_id, 7);

        $this->assertEquals(count($primaryPatient->allergyAssignments), 2);
        $this->assertEquals(count($secondaryPatient->allergyAssignments), 0);
    }

    public function testUpdateAllergyAssignments_bothHaveSameAllergyAssignments()
    {
        $mergeHandler = new PatientMerge();

        $assignment1 = $this->patient_allergy_assignment('assignment1');
        $assignment2 = $this->patient_allergy_assignment('assignment2');

        $assignment2->allergy_id = 1;
        $assignment2->save();
        $assignment2->refresh();

        $this->assertEquals($assignment1->allergy_id, $assignment2->allergy_id);

        $primaryPatient = $this->patients('patient7');
        $secondaryPatient = $this->patients('patient8');

        $this->assertEquals(count($primaryPatient->allergyAssignments), 1);
        $this->assertEquals(count($secondaryPatient->allergyAssignments), 1);

        $result = $mergeHandler->updateAllergyAssignments($primaryPatient, $secondaryPatient);

        $primaryPatient->refresh();
        $secondaryPatient->refresh();

        $assignment1->refresh();
        $this->assertEquals($assignment1->patient_id, 7);

        $this->assertEquals($assignment1->comments, 'comment 1 ; comment 2');
        $this->assertEquals($assignment1->other, 'other 1 ; other 2');
    }

    public function testUpdateRiskAssignments()
    {
    }

    public function testUpdatePreviousOperations()
    {
    }

    public function testIsSecondaryPatientDeleted()
    {
    }

    public function testUpdateEpisodesPatientId()
    {
    }

    public function testUpdateEventsEpisodeId()
    {
    }

    public function testLoad()
    {
    }

    public function testMerge()
    {
    }

    public function tearDown()
    {
        parent::tearDown();
    }
}
