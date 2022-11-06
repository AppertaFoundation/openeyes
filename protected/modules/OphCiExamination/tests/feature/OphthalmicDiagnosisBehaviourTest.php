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
use OEModule\OphCiExamination\models\SystemicDiagnoses;
use SecondaryDiagnosis;

/**
 * @group sample-data
 * @group system-events
 * @group disorder
 * @group diagnoses
 */
class OphthalmicDiagnosisBehaviourTest extends \OEDbTestCase
{
    use \HasEventTypeElementAssertions;
    use \MocksSession;
    use \MakesApplicationRequests;
    use \WithFaker;
    // use \WithTransactions;

    /** @test */
    public function ophthalmic_entries_are_saved_and_reflected_in_patient_record()
    {
        // set up patient and episode for new event to be attached to
        $episode = \Episode::factory()->create();
        $patient = $episode->patient;

        $test_diagnoses = $this->createOphthalmicDiagnosesElementThroughRequest($episode);

        $this->assertEventTypeElementCreatedFor(
            $patient,
            Element_OphCiExamination_Diagnoses::class,
            []
        );
        $this->assertExaminationDiagnosesRecordedFor($patient, $test_diagnoses->diagnoses);
        $this->assertSecondaryDiagnosesRecordedFor($patient, $test_diagnoses->diagnoses);
        $this->assertPrincipalDiagnosisRecordedIn($episode, $test_diagnoses->diagnoses);
    }

    /** @test */
    public function systemic_diagnoses_are_saved_and_reflected_in_patient_record()
    {
        // set up patient and episode for new event to be attached to
        $episode = \Episode::factory()->create();
        $patient = $episode->patient;

        $test_diagnoses = $this->createSystemicDiagnosesElementThroughRequest($episode);

        $this->assertEventTypeElementCreatedFor(
            $patient,
            SystemicDiagnoses::class,
            []
        );

        $this->assertSecondaryDiagnosesRecordedFor($patient, $test_diagnoses->diagnoses, false);
    }

    /** @test */
    public function deleting_more_recent_examination_reverts_to_previous()
    {
        $initial_event = EventFactory::forModule('OphCiExamination')
        ->create([
            'event_date' => $this->faker->dateTimeBetween('-4 weeks', '-2 weeks')->format('Y-m-d')
        ]);

        $initial_oph_diagnoses = Element_OphCiExamination_Diagnoses::factory()
            ->withBilateralDiagnoses(1)
            ->withRightDiagnoses(1)
            ->withLeftDiagnoses(1)
            ->create(['event_id' => $initial_event->id]);

        $episode = $initial_event->episode;
        $patient = $episode->patient;

        // Secondary diagnosis updates are (at the time of writing) triggered through
        // the controller behaviour calling the correct methods on the diagnoses element
        // So here we are performing the POST request to trigger this.
        $diagnoses_to_be_deleted = $this->createOphthalmicDiagnosesElementThroughRequest($episode);

        $this->assertSecondaryDiagnosesRecordedFor($patient, $diagnoses_to_be_deleted->diagnoses);
        $this->assertPrincipalDiagnosisRecordedIn($episode, $diagnoses_to_be_deleted->diagnoses);

        // retrieve the event that was created with this post
        $latest_event = \Event::model()
            ->findAll(['order' => 'created_date DESC', 'limit' => 1])[0];

        $latest_event->softDelete();

        // should revert to the diagnoses from the initial event
        $this->assertSecondaryDiagnosesRecordedFor($patient, $initial_oph_diagnoses->diagnoses);
        $this->assertPrincipalDiagnosisRecordedIn($episode, $initial_oph_diagnoses->diagnoses);
    }

    /** @test */
    public function patient_diagnoses_unaffected_when_older_examination_is_deleted()
    {
        $event_to_be_deleted = EventFactory::forModule('OphCiExamination')
        ->create([
            'event_date' => $this->faker->dateTimeBetween('-4 weeks', '-2 weeks')->format('Y-m-d')
        ]);

        Element_OphCiExamination_Diagnoses::factory()
            ->withBilateralDiagnoses(1)
            ->withRightDiagnoses(1)
            ->withLeftDiagnoses(1)
            ->create(['event_id' => $event_to_be_deleted->id]);
        $episode = $event_to_be_deleted->episode;

        $most_recent_diagnoses_element_data = $this->createOphthalmicDiagnosesElementThroughRequest($episode);

        $this->assertSecondaryDiagnosesRecordedFor($episode->patient, $most_recent_diagnoses_element_data->diagnoses);
        $this->assertPrincipalDiagnosisRecordedIn($episode, $most_recent_diagnoses_element_data->diagnoses);

        $event_to_be_deleted->softDelete();

        $this->assertSecondaryDiagnosesRecordedFor($episode->patient, $most_recent_diagnoses_element_data->diagnoses);
        $this->assertPrincipalDiagnosisRecordedIn($episode, $most_recent_diagnoses_element_data->diagnoses);
    }

    /**
     * This abstraction simply wraps the generation and POSTing of sample diagnoses element data for the given episode
     */
    protected function createOphthalmicDiagnosesElementThroughRequest(\Episode $episode): Element_OphCiExamination_Diagnoses
    {
        $diagnoses_data_element = Element_OphCiExamination_Diagnoses::factory()
            ->withBilateralDiagnoses(1)
            ->withRightDiagnoses(1)
            ->withLeftDiagnoses(1)
            ->make(['event_id' => null]);

        $form_data = [
            CHtml::modelName($diagnoses_data_element) => $this->mapOphthalmicDiagnosesElementToFormData($diagnoses_data_element),
            'principal_diagnosis_row_key' => $this->findPrincipalRowKey($diagnoses_data_element->diagnoses),
            'patient_id' => $episode->patient_id
        ];

        $this->createExaminationEventWithFormData($episode, $form_data);

        return $diagnoses_data_element;
    }

    protected function createSystemicDiagnosesElementThroughRequest(\Episode $episode): SystemicDiagnoses
    {
        $diagnoses_data_element = SystemicDiagnoses::factory()
            ->withDiagnoses(2)
            ->make(['event_id' => null]);

        $form_data = [
            CHtml::modelName($diagnoses_data_element) => $this->mapSystemicDiagnosesElementToFormData($diagnoses_data_element),
            'patient_id' => $episode->patient_id
        ];

        $this->createExaminationEventWithFormData($episode, $form_data);

        return $diagnoses_data_element;
    }

    protected function mapOphthalmicDiagnosesElementToFormData(Element_OphCiExamination_Diagnoses $element): array
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

    protected function mapSystemicDiagnosesElementToFormData(SystemicDiagnoses $element): array
    {
        $result = [
            'entries' => [],
            'present' => "1"
        ];
        foreach ($element->diagnoses as $i => $entry) {
            $result['entries'][] = [
                'has_disorder' => "1",
                'disorder_id' => $entry->disorder_id,
                'date' => $entry->date,
                'has_disorder' => $entry->has_disorder,
                // assume no laterality in initial tests
                'na_eye' => "-9"
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

    protected function createExaminationEventWithFormData(\Episode $episode, array $form_data): void
    {
        list($user, $institution) = $this->createUserWithInstitution();
        $this->mockCurrentContext($episode->firm, null, $institution);

        $response = $this->actingAs($user, $institution)
            ->post('/OphCiExamination/Default/create', $form_data);

        $response->assertRedirectContains('view', 'Expected to redirect to a view of the created event');
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

    protected function assertSecondaryDiagnosesRecordedFor(\Patient $patient, $diagnoses, bool $remove_principal = true): void
    {
        if ($remove_principal) {
            $diagnoses = array_filter($diagnoses, function ($diagnosis) {
                return !$diagnosis->principal;
            });
        }

        $table = SecondaryDiagnosis::model()->tableName();

        foreach ($diagnoses as $diagnosis) {
            $attributes = [
                'disorder_id' => $diagnosis->disorder_id,
                'eye_id' => (property_exists($diagnosis, 'eye_id') ? $diagnosis->eye_id : $diagnosis->side_id) ?? null,
                'date' => $diagnosis->date ?? null,
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

    /**
     * Helper method for debugging purposes when tests fail
     *
     * @param [type] $diagnoses
     * @return void
     */
    private function debugDiagnoses($diagnoses): void
    {
        fwrite(STDERR, print_r(
            array_map(
                function ($diagnosis) {
                    return [
                        'disorder_id' => $diagnosis->disorder_id,
                        'eye_id' => $diagnosis->eye_id,
                        'principal' => $diagnosis->principal ?? 0,
                        'date' => $diagnosis->date
                    ];
                },
                $diagnoses->diagnoses
            ),
            true
            )
        );
    }
}
