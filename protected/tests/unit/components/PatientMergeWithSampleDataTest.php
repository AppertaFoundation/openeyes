<?php
/**
 * (C) OpenEyes Foundation, 2022
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2022, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

use OE\factories\ModelFactory;

/**
 * @group sample-data
 * @group patient-merge
 * @group trial
 */
class PatientMergeWithSampleDataTest extends ModelTestCase
{
    use WithTransactions;
    use MocksSession;

    protected $element_cls = PatientMergeRequest::class;

    public function setUp(): void
    {
        parent::setUp();

        $this->mockCurrentInstitution();
    }

    /**
     * @test
     */
    public function merge_patients_on_different_trials(): void
    {
        $merge_handler = new PatientMerge();
        $merge_handler->load(new PatientMergeRequest());

        list($trial_patient_primary_patient, $trial_patient_secondary_patient)  = \TrialPatient::factory()
            ->count(2)
            ->create();

        $primary_patient = $trial_patient_primary_patient->patient;
        $secondary_patient = $trial_patient_secondary_patient->patient;

        $merge_handler->updateTrials($primary_patient, $secondary_patient);

        $trial_patient_secondary_patient_record = TrialPatient::model()->findByPk($trial_patient_secondary_patient->id);
        $this->assertEquals($primary_patient->id, $trial_patient_secondary_patient_record->patient_id);

        $trial_patient_primary_patient_record = TrialPatient::model()->findByPk($trial_patient_primary_patient->id);
        $this->assertEquals($primary_patient->id, $trial_patient_primary_patient_record->patient_id);
    }

    /**
     * @test
     */
    public function merge_patients_both_primary_and_seconday_patient_in_same_trial(): void
    {
        $merge_handler = new PatientMerge();
        $merge_handler->load(new PatientMergeRequest());

        $trial = Trial::factory()->create();

        list($trial_patient_primary_patient, $trial_patient_secondary_patient)  = \TrialPatient::factory()
            ->count(2)
            ->create(['trial_id' => $trial->id]);

        $primary_patient = $trial_patient_primary_patient->patient;
        $secondary_patient = $trial_patient_secondary_patient->patient;

        $merge_handler->updateTrials($primary_patient, $secondary_patient);

        $trial_patient_secondary_patient_record_count = TrialPatient::model()->countByAttributes(['id' => $trial_patient_secondary_patient->id]);
        /* If both secondary and primary patients exists in the same trial, then delete the
        secondary patient record and leave the primary patient trial patient record as it is. */
        $this->assertEquals(0, $trial_patient_secondary_patient_record_count);
        $trial_patient_primary_patient_records = TrialPatient::model()->findAllByAttributes(['id' => $trial_patient_primary_patient->id]);
        $this->assertEquals(1, count($trial_patient_primary_patient_records));
        $this->assertEquals($primary_patient->id, $trial_patient_primary_patient_records[0]->patient_id);
    }

    /**
     * @test
     */
    public function merge_patients_primary_patient_not_in_any_trial(): void
    {
        $primary_patient = \Patient::factory()->create();

        $merge_handler = new PatientMerge();
        $merge_handler->load(new PatientMergeRequest());

        $trial_patient_secondary_patient = \TrialPatient::factory()->create();
        $secondary_patient = $trial_patient_secondary_patient->patient;

        $merge_handler->updateTrials($primary_patient, $secondary_patient);

        $trial_patient_secondary_patient_record = TrialPatient::model()->findByPk($trial_patient_secondary_patient->id);
        // The patient_id column in trial_patient table should be of the primary_patient after the merge
        $this->assertEquals($primary_patient->id, $trial_patient_secondary_patient_record->patient_id);
    }
}
