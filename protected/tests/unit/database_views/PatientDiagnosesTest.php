<?php

/**
 * (C) Copyright Apperta Foundation 2021
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2021, Apperta Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

use OE\factories\ModelFactory;
use OEModule\OphCiExamination\models\Element_OphCiExamination_Diagnoses;
use OEModule\OphCiExamination\models\SystemicDiagnoses;

/**
 * @group sample-data
 * @group disorder
 * @group diagnoses
 */
class PatientDiagnosesTest extends \OEDbTestCase
{
    use \HasDatabaseAssertions;
    use \WithFaker;
    use \WithTransactions;
    use \MocksSession;

    /** @test */
    public function db_view_shows_icd10_attributes_for_opthalmology()
    {
        $icd10_disorder = ModelFactory::factoryFor(Disorder::class)
            ->forOpthalmology()
            ->withICD10()
            ->create();

        $diagnoses_element = Element_OphCiExamination_Diagnoses::factory()
            ->onEventDate($this->faker->dateTimeBetween("-1 year", '-6 months'))
            ->withBilateralDiagnoses([$icd10_disorder])
            ->create();

        $this->assertDatabaseHas('v_patient_diagnoses', [
            'patient_id' => $diagnoses_element->event->episode->patient_id,
            'icd10_code' => $icd10_disorder->icd10_code,
            'icd10_term' => $icd10_disorder->icd10_term,
            'side' => 'B'
        ]);
    }

    /** @test */
    public function db_view_will_discard_previous_diagnoses_when_newer_event_is_created_for_opthalmic_disorders()
    {
        $original_disorder = ModelFactory::factoryFor(Disorder::class)
            ->forOpthalmology()
            ->create();

        $initial_diagnoses_element = Element_OphCiExamination_Diagnoses::factory()
            ->onEventDate($this->faker->dateTimeBetween("-1 year", '-6 months'))
            ->withBilateralDiagnoses([$original_disorder])
            ->create();

        $patient = $initial_diagnoses_element->event->episode->patient;

        $earlier_diagnosis_view_data = [
            'patient_id' => $patient->id,
            'disorder_id' => $original_disorder->id,
            'side' => 'B'
        ];

        $this->assertDatabaseHas('v_patient_diagnoses', $earlier_diagnosis_view_data);

        // now override with new examination and diagnoses
        $later_disorders = ModelFactory::factoryFor(Disorder::class)
            ->forOpthalmology()
            ->count(2)
            ->create();

        Element_OphCiExamination_Diagnoses::factory()
            ->forPatient($patient)
            ->onEventDate($this->faker->dateTimeBetween("-5 months"))
            ->withLeftDiagnoses([$later_disorders[0]])
            ->withRightDiagnoses([$later_disorders[1]])
            ->create();


        $this->assertDatabaseHas('v_patient_diagnoses', [
            'patient_id' => $patient->id,
            'disorder_id' => $later_disorders[0]->id,
            'side' => 'L'
        ]);

        $this->assertDatabaseHas('v_patient_diagnoses', [
            'patient_id' => $patient->id,
            'disorder_id' => $later_disorders[1]->id,
            'side' => 'R'
        ]);

        $this->assertDatabaseDoesntHave('v_patient_diagnoses', $earlier_diagnosis_view_data);
    }

    /** @test */
    public function db_view_shows_icd10_attributes_for_non_opthalmology()
    {
        $icd10_disorder = ModelFactory::factoryFor(Disorder::class)
            ->withICD10()
            ->create();

        $diagnoses_element = SystemicDiagnoses::factory()
            ->onEventDate($this->faker->dateTimeBetween("-1 year", '-6 months'))
            ->withDiagnoses([$icd10_disorder])
            ->create();

        $this->assertDatabaseHas('v_patient_diagnoses', [
            'patient_id' => $diagnoses_element->event->episode->patient_id,
            'icd10_code' => $icd10_disorder->icd10_code,
            'icd10_term' => $icd10_disorder->icd10_term
        ]);
    }

    /** @test */
    public function db_view_will_discard_previous_diagnoses_when_newer_event_is_created_for_non_opthalmic_disorders()
    {
        $original_disorder = ModelFactory::factoryFor(Disorder::class)
            ->create();

        $initial_diagnoses_element = SystemicDiagnoses::factory()
            ->onEventDate($this->faker->dateTimeBetween("-1 year", '-6 months'))
            ->withDiagnoses([$original_disorder])
            ->create();

        $patient = $initial_diagnoses_element->event->episode->patient;

        $earlier_diagnosis_view_data = [
            'patient_id' => $patient->id,
            'disorder_id' => $original_disorder->id,
        ];

        $this->assertDatabaseHas('v_patient_diagnoses', $earlier_diagnosis_view_data);

        // now override with new examination and diagnoses
        $later_disorders = ModelFactory::factoryFor(Disorder::class)
            ->count(2)
            ->create();

        SystemicDiagnoses::factory()
            ->forPatient($patient)
            ->onEventDate($this->faker->dateTimeBetween("-5 months"))
            ->withDiagnoses($later_disorders)
            ->create();


        $this->assertDatabaseHas('v_patient_diagnoses', [
            'patient_id' => $patient->id,
            'disorder_id' => $later_disorders[0]->id
        ]);

        $this->assertDatabaseHas('v_patient_diagnoses', [
            'patient_id' => $patient->id,
            'disorder_id' => $later_disorders[1]->id
        ]);

        $this->assertDatabaseDoesntHave('v_patient_diagnoses', $earlier_diagnosis_view_data);
    }
}
