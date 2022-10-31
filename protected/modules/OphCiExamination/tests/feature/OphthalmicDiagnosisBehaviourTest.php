<?php
/**
 * (C) Apperta Foundation, 2022
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2022, Apperta Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

namespace OEModule\OphCiExamination\tests\feature;
use CHtml;
use OE\factories\models\EventFactory;
use OEModule\OphCiExamination\models\Element_OphCiExamination_Diagnoses;
use OEModule\OphCiExamination\models\OphCiExamination_Diagnosis;
use SecondaryDiagnosis;

class OphthalmicDiagnosisBehaviourTest extends \OEDbTestCase
{
    use \HasEventTypeElementAssertions;
    use \MocksSession;
    use \MakesApplicationRequests;
    use \WithFaker;
    use \WithTransactions;

    /** @test */
    public function entries_are_saved_and_reflected_in_patient_record()
    {
        // set up patient and episode for new event to be attached to
        $episode = \Episode::factory()->create();
        $patient = $episode->patient;

        $test_diagnoses = Element_OphCiExamination_Diagnoses::factory()
        ->withBilateralDiagnoses(1)
        ->withRightDiagnoses(1)
        ->withLeftDiagnoses(1)
        ->make([
            'event_id' => null
        ]);

        list($user, $institution) = $this->createUserWithInstitution();
        $this->mockCurrentContext($episode->firm, null, $institution);
        $form_data = [
            CHtml::modelName($test_diagnoses) => $this->mapElementToFormData($test_diagnoses),
            'principal_diagnosis_row_key' => $this->findPrincipalRowKey($test_diagnoses->diagnoses),
            'patient_id' => $patient->id
        ];

        $response = $this->actingAs($user, $institution)
            ->post('/OphCiExamination/Default/create', $form_data);

        $response->assertRedirectContains('view', 'Expected to redirect to a view of the created event');
        $this->assertEventTypeElementCreatedFor(
            $patient,
            Element_OphCiExamination_Diagnoses::class,
            []
        );
        $this->assertExaminationDiagnosesRecordedFor($patient, $test_diagnoses->diagnoses);
        $this->assertSecondaryDiagnosesRecordedFor($patient, $test_diagnoses->diagnoses);
        $this->assertPrincipalDiagnosisRecordedIn($episode, $test_diagnoses->diagnoses);
    }

    /**
     * Because the secondary diagnoses are only setup via the POST data handling of the
     * examination module, we have to use the request approach to verify the behaviour
     *
     * @test
     */
    public function deleting_more_recent_examination_reverts_to_previous()
    {
        $initial_event = EventFactory::forModule('OphCiExamination')
        ->create([
            'event_date' => $this->faker->dateTimeBetween('-4 weeks', '-2 weeks')->format('Y-m-d')
        ]);

        $initial_diagnoses = Element_OphCiExamination_Diagnoses::factory()
            ->withBilateralDiagnoses(1)
            ->withRightDiagnoses(1)
            ->withLeftDiagnoses(1)
            ->create(['event_id' => $initial_event->id]);
        $episode = $initial_event->episode;
        $patient = $episode->patient;

        list($user, $institution) = $this->createUserWithInstitution();
        $this->mockCurrentContext($episode->firm, null, $institution);

        $diagnoses_to_be_deleted = Element_OphCiExamination_Diagnoses::factory()
            ->withBilateralDiagnoses(1)
            ->withRightDiagnoses(1)
            ->withLeftDiagnoses(1)
            ->make(['event_id' => null]);

        $form_data = [
            CHtml::modelName($diagnoses_to_be_deleted) => $this->mapElementToFormData($diagnoses_to_be_deleted),
            'principal_diagnosis_row_key' => $this->findPrincipalRowKey($diagnoses_to_be_deleted->diagnoses),
            'patient_id' => $patient->id
        ];

        $response = $this->actingAs($user, $institution)
            ->post('/OphCiExamination/Default/create', $form_data);

        $response->assertRedirectContains('view', 'Expected to redirect to a view of the created event');

        $this->assertSecondaryDiagnosesRecordedFor($patient, $diagnoses_to_be_deleted->diagnoses);
        $this->assertPrincipalDiagnosisRecordedIn($episode, $diagnoses_to_be_deleted->diagnoses);

        $latest_event = \Event::model()
            ->findAll(['order' => 'created_date DESC', 'limit' => 1])[0];

        $latest_event->softDelete();

        $this->assertSecondaryDiagnosesRecordedFor($patient, $initial_diagnoses->diagnoses);
        $this->assertPrincipalDiagnosisRecordedIn($episode, $initial_diagnoses->diagnoses);
    }

    /** @test */
    public function patient_diagnoses_unaffected_when_older_examination_is_deleted()
    {
        $this->markTestIncomplete();
    }

    protected function mapElementToFormData($element): array
    {
        $result = ['entries' => []];
        foreach ($element->diagnoses as $i => $entry) {
            $result['entries'][] = [
                'disorder_id' => $entry->disorder_id,
                'right_eye' => ($entry->eye_id & \Eye::RIGHT) === \Eye::RIGHT,
                'left_eye' => ($entry->eye_id & \Eye::LEFT) === \Eye::LEFT,
                'date' => $entry->date,
                'row_key' => $i
            ];
        }
        return $result;
    }

    protected function findPrincipalRowKey(array $diagnoses): int
    {
        for ($i = 0; $i < count($diagnoses); $i++) {
            if ((bool) $diagnoses[$i]->principal) {
                return $i;
            }
        }

        throw new \InvalidArgumentException('No principal entry found');
    }

    // TODO: refactor (see PCRRiskTest)
    protected function createUserWithInstitution()
    {
        $user = \User::model()->findByAttributes(['first_name' => 'admin']);

        $institution = \Institution::factory()
            ->withUserAsMember($user)
            ->create();

        return [$user, $institution];
    }

    protected function assertExaminationDiagnosesRecordedFor(\Patient $patient, array $diagnoses)
    {
        $element_table = Element_OphCiExamination_Diagnoses::model()->tableName();
        $record_table = OphCiExamination_Diagnosis::model()->tableName();

        foreach ($diagnoses as $diagnosis) {
            $diagnosis_data = [
                'disorder_id' => $diagnosis->disorder_id,
                'eye_id' => $diagnosis->eye_id,
                'date' => $diagnosis->date,
                'principal' => $diagnosis->principal
            ];
            $query = $this->generateDatabaseCountQuery($record_table, $diagnosis_data);
            $query->join = "join $element_table el on $record_table.element_diagnoses_id = el.id";
            $this->joinPatientToEventTypeElementQuery($patient, $query, "el");
            $this->assertGreaterThanOrEqual(1, $query->queryScalar(), "$record_table does not contain " . print_r($diagnosis_data, true) . "for given patient.");
        }
    }

    protected function assertSecondaryDiagnosesRecordedFor(\Patient $patient, $diagnoses): void
    {
        $non_principal = array_filter($diagnoses, function ($diagnosis) {
            return !$diagnosis->principal;
        });

        $table = SecondaryDiagnosis::model()->tableName();

        foreach ($non_principal as $diagnosis) {
            $attributes = [
                'disorder_id' => $diagnosis->disorder_id,
                'eye_id' => $diagnosis->eye_id,
                'date' => $diagnosis->date,
                'patient_id' => $patient->id
            ];

            $this->assertDatabaseHas($table, $attributes);
        }
    }

    protected function assertPrincipalDiagnosisRecordedIn(\Episode $episode, $diagnoses): void
    {
        $episode->refresh();
        $principal = array_values(
            array_filter($diagnoses, function ($diagnosis) {
                return (bool) $diagnosis->principal;
            })
        )[0];

        $this->assertEquals($principal->disorder_id, $episode->disorder_id, "Principal diagnosis disorder has not been set correctly on the episode");
        $this->assertEquals($principal->eye_id, $episode->eye_id, "Principal diagnosis eye has not been set correctly on the episode");
        $this->assertEquals($principal->date, $episode->disorder_date, "Principal diagnosis datehas not been set correctly on the episode");
    }
}
