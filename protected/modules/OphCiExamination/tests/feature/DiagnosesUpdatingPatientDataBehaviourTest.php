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
class DiagnosesUpdatingPatientDataBehaviourTest extends \OEDbTestCase
{
    use \HasEventTypeElementAssertions;
    use \MocksSession;
    use \MakesApplicationRequests;
    use \WithFaker;
    use \WithTransactions;

    public function setUp(): void
    {
        parent::setUp();
        // will rerun the configuration application on the event manager
        // so events are restored for these tests
        \Yii::app()->event->init();
    }

    public function tearDown(): void
    {
        \Yii::app()->event->forgetAll();
        parent::tearDown();
    }

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
    public function deleting_sole_diagnosis_examination_removes_all_diagoses_from_patient()
    {
        $episode = \Episode::factory()->create();
        $patient = $episode->patient;

        $this->createOphthalmicDiagnosesElementThroughRequest($episode);
        // retrieve the event that was created with this post
        $latest_event = \Event::model()
            ->findAll(['order' => 'id DESC', 'limit' => 1])[0];

        $episode->refresh();
        $this->assertNotNull($episode->disorder_id);
        $latest_event->softDelete();
        $episode->refresh();

        $this->assertEmpty($patient->getOphthalmicDiagnoses());
        $this->assertNull($episode->disorder_id);
    }

    /** @test */
    public function deleting_more_recent_examination_with_ophthalmic_diagnoses_reverts_to_previous()
    {
        list($initial_oph_diagnoses) = $this->createExaminationWithElements([Element_OphCiExamination_Diagnoses::class]);
        $initial_event = $initial_oph_diagnoses->event;
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
            ->findAll(['order' => 'id DESC', 'limit' => 1])[0];

        $latest_event->softDelete();

        // should revert to the diagnoses from the initial event
        $this->assertSecondaryDiagnosesRecordedFor($patient, $initial_oph_diagnoses->diagnoses);
        $this->assertPrincipalDiagnosisRecordedIn($episode, $initial_oph_diagnoses->diagnoses);
    }

    /** @test */
    public function patient_ophthalmic_diagnoses_unaffected_when_older_examination_is_deleted()
    {
        list($diagnoses_to_be_deleted) = $this->createExaminationWithElements([Element_OphCiExamination_Diagnoses::class]);
        $event_to_be_deleted = $diagnoses_to_be_deleted->event;
        $episode = $event_to_be_deleted->episode;

        $most_recent_diagnoses_element_data = $this->createOphthalmicDiagnosesElementThroughRequest($episode);

        $this->assertSecondaryDiagnosesRecordedFor($episode->patient, $most_recent_diagnoses_element_data->diagnoses);
        $this->assertPrincipalDiagnosisRecordedIn($episode, $most_recent_diagnoses_element_data->diagnoses);

        $event_to_be_deleted->softDelete();

        $this->assertSecondaryDiagnosesRecordedFor($episode->patient, $most_recent_diagnoses_element_data->diagnoses);
        $this->assertPrincipalDiagnosisRecordedIn($episode, $most_recent_diagnoses_element_data->diagnoses);
    }

    /** @test */
    public function deleting_more_recent_examination_with_systemic_diagnoses_reverts_to_previous()
    {
        list($initial_sys_diagnoses) = $this->createExaminationWithElements([SystemicDiagnoses::class]);

        $episode = $initial_sys_diagnoses->event->episode;
        $patient = $episode->patient;

        // Secondary diagnosis updates are (at the time of writing) triggered through
        // the controller behaviour calling the correct methods on the diagnoses element
        // So here we are performing the POST request to trigger this.
        $diagnoses_to_be_deleted = $this->createSystemicDiagnosesElementThroughRequest($episode);

        $this->assertSecondaryDiagnosesRecordedFor($patient, $diagnoses_to_be_deleted->diagnoses, false);

        // retrieve the event that was created with this post
        $latest_event = \Event::model()
            ->findAll(['order' => 'id DESC', 'limit' => 1])[0];

        $latest_event->softDelete();

        // should revert to the diagnoses from the initial event
        $this->assertSecondaryDiagnosesRecordedFor($patient, $initial_sys_diagnoses->diagnoses, false);
    }

    /** @test */
    public function systemic_and_ophthalmic_diagnoses_are_reverted_to_most_recent_previous_event()
    {
        // old entries that should be ignored
        list($older_oph, $older_sys) = $this->createExaminationWithElements(
            [Element_OphCiExamination_Diagnoses::class, SystemicDiagnoses::class],
            null,
            '-8 weeks',
            '-6 weeks'
        );

        $episode = $older_oph->event->episode;

        list($expected_oph) = $this->createExaminationWithElements(
            [Element_OphCiExamination_Diagnoses::class],
            $episode,
            '-5 weeks',
            '-4 weeks'
        );

        list($expected_sys) = $this->createExaminationWithElements(
            [SystemicDiagnoses::class],
            $episode,
            '-5 weeks',
            '-3 weeks'
        );

        $this->createOphthalmicAndSystemicElementsThroughRequest($episode);

        // retrieve the event that was created with this post
        $latest_event = \Event::model()
            ->findAll(['order' => 'id DESC', 'limit' => 1])[0];

        $latest_event->softDelete();

        $this->assertSecondaryDiagnosesRecordedFor($episode->patient, $expected_oph->diagnoses);
        $this->assertSecondaryDiagnosesRecordedFor($episode->patient, $expected_sys->diagnoses, false);
    }

    /** @test */
    public function not_at_tip_ophthalmic_diagnoses_can_not_be_modified()
    {
        list($old_oph) = $this->createExaminationWithElements(
            [Element_OphCiExamination_Diagnoses::class],
            null,
            '-8 weeks',
            '-6 weeks'
        );

        $episode = $old_oph->event->episode;

        list($new_oph) = $this->createExaminationWithElements(
            [Element_OphCiExamination_Diagnoses::class],
            $episode,
            '-5 weeks',
            '-4 weeks'
        );

        $this->assertTrue(
            $old_oph->save(),
            "Should be able to save old ophthalmic diagnoses when no changes made"
        );

        // remove one diagnoses from old record
        $old_oph->diagnoses = array_slice($old_oph->diagnoses, 1);

        $this->assertFalse(
            $old_oph->save(),
            "Should not be able save altered diagnoses when not at tip"
        );
    }

    /** @test */
    public function model_is_dirty_after_changes() {
        list($oph) = $this->createExaminationWithElements(
            [Element_OphCiExamination_Diagnoses::class],
            null,
            '-8 weeks',
            '-6 weeks'
        );
        $oph->no_ophthalmic_diagnoses_date = date('Y-m-d H:i:s');
        $this->assertTrue($oph->isModelDirty(), "Element_OphCiExamination_Diagnoses model is not dirty after setting no ophthalmic diagnoses date");

        // assert the associated diagnosis if there is at least one record
        if (count($oph->diagnoses) > 0) {
            $oph->diagnoses[0]->principal = !$oph->diagnoses[0]->principal;

            $this->assertTrue($oph->diagnoses[0]->isModelDirty(), "OphCiExamination_Diagnosis model is not dirty after changing the principal state");

            $this->assertTrue($oph->isModelDirty(), "Element_OphCiExamination_Diagnoses model is not dirty after changing the first diagnosis principal state");
        }
    }

    protected function createExaminationWithElements(
        array $element_classes,
        ?\Episode $episode = null,
        $after = '-4 weeks',
        $before = '-2 weeks'
    )
    {
        $event_attrs = [
            'event_date' => $this->faker->dateTimeBetween($after, $before)->format('Y-m-d')
        ];
        if ($episode) {
            $event_attrs['episode_id'] = $episode->id;
        }
        $event = EventFactory::forModule('OphCiExamination')
            ->create($event_attrs);

        $elements = [];
        foreach ($element_classes as $element_class) {
            if ($element_class === Element_OphCiExamination_Diagnoses::class) {
                $factory = Element_OphCiExamination_Diagnoses::factory()
                    ->withBilateralDiagnoses(1)
                    ->withRightDiagnoses(1)
                    ->withLeftDiagnoses(1);
            } else {
                $factory = SystemicDiagnoses::factory()
                ->withDiagnoses(2);
            }
            $elements[] = $factory
                ->create(['event_id' => $event->id]);
        }
        return $elements;
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
        list($diagnoses_data_element, $element_form_data) = SystemicDiagnoses::factory()
            ->withDiagnoses(2)
            ->makeWithFormData(['event_id' => null]);

        $form_data = array_merge(
            [
                'patient_id' => $episode->patient_id
            ],
            $element_form_data
        );

        $this->createExaminationEventWithFormData($episode, $form_data);

        return $diagnoses_data_element;
    }

    protected function createOphthalmicAndSystemicElementsThroughRequest(\Episode $episode): array
    {
        $ophthalmic = Element_OphCiExamination_Diagnoses::factory()
            ->withBilateralDiagnoses(2)
            ->make(['event_id' => null]);
        list($systemic_element, $systemic_form_data) = SystemicDiagnoses::factory()
            ->withDiagnoses(2)
            ->makeWithFormData(['event_id' => null]);

        $form_data = array_merge(
            [
                CHtml::modelName($ophthalmic) => $this->mapOphthalmicDiagnosesElementToFormData($ophthalmic),
                'principal_diagnosis_row_key' => $this->findPrincipalRowKey($ophthalmic->diagnoses),
                'patient_id' => $episode->patient_id
            ],
            $systemic_form_data
        );

        $this->createExaminationEventWithFormData($episode, $form_data);

        return [$ophthalmic, $systemic_element];
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

    /**
     * Checks the given \Patient has SecondaryDiagnoses recorded for given $diagnoses - a list of
     * OphCiExamination_Diagnosis or \OEModule\OphCiExamination\models\SystemicDiagnoses_Diagnosis
     *
     * @param \Patient $patient
     * @param OphCiExamination_Diagnosis[]|\OEModule\OphCiExamination\models\SystemicDiagnoses_Diagnosis[] $diagnoses
     * @param boolean $remove_principal
     * @return void
     */
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
                'eye_id' => ($diagnosis->hasAttribute('eye_id') ? $diagnosis->eye_id : $diagnosis->side_id) ?? null,
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
