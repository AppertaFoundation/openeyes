<?php
/**
 * (C) Apperta Foundation, 2024
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2024, Apperta Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

namespace OEModule\OphTrOperationnote\test\unit\controllers;

use OEDbTestCase;
use WithTransactions;
use WithFaker;
use MocksSession;
use MakesApplicationRequests;

use Institution;
use Firm;
use User;
use Patient;
use Episode;
use Eye;
use Procedure;
use Anaesthetist;

use AnaestheticType;
use AnaestheticAgent;
use MedicationSet;

use Element_OphTrOperationnote_SiteTheatre;
use Element_OphTrOperationnote_Surgeon;
use Element_OphTrOperationnote_ProcedureList;
use Element_OphTrOperationnote_Anaesthetic;
use Element_OphTrOperationnote_PostOpDrugs;
use Element_OphTrOperationnote_Comments;
use Element_OphTrOperationnote_GenericProcedure;

use Element_OphDrPrescription_Details;

/**
 * @group sample-data
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
class DefaultControllerTest extends OEDbTestCase
{
    use WithTransactions;
    use WithFaker;
    use MocksSession;
    use MakesApplicationRequests;

    protected const URL_BASE = '/OphTrOperationnote/Default/';

    protected static ?array $generic_procedure_ids = null;

    public function lateralities()
    {
        return [
            'Left' => [Eye::LEFT],
            'Right' => [Eye::RIGHT]
        ];
    }

    /**
     * @test
     * @dataProvider lateralities
     */
    public function after_create_event_creates_prescription_with_same_laterality_as_opnote($eye_id)
    {
        $current_institution = Institution::factory()->useExisting()->create();
        $current_firm = Firm::factory()->useExisting(['institution_id' => $current_institution])->create();

        $this->mockCurrentContext($current_firm, null, $current_institution);

        $prescriber_user = User::factory()
            ->withLocalAuthForInstitution($current_institution)
            ->withAuthItems(['Edit', 'User', 'View clinical'])
            ->create();

        $patient = Patient::factory()->create();
        $episode = Episode::factory()->forPatient($patient)->forFirm($current_firm)->create();
        $anaethetist = Anaesthetist::factory()->create();

        $procedure = $this->faker->randomElement($this->getGenericProcedures());

        $data = array_merge(
            $this->getOperationNoteElementsFormData($eye_id, $anaethetist, $procedure),
            $this->map_autogenerate_prescription_data(true),
            ['patient_id' => $patient->id]
        );

        $url = self::URL_BASE . 'create?' . http_build_query([
            'patient_id' => $patient->id,
            'unbooked' => 1,
            'unbooked_type' => 'emergency'
        ]);

        $this->actingAs($prescriber_user)
             ->post($url, $data)
             ->assertRedirect();

        $episode->refresh();

        $this->assertCount(2, $episode->events);

        $prescription = Element_OphDrPrescription_Details::model()->with('event')->find('episode_id = ?', [$episode->id]);

        $this->assertNotNull($prescription, 'The generated prescription was not found');

        foreach ($prescription->items as $item) {
            $this->assertEquals($eye_id, $item->laterality, 'Eye laterality should not have diverged between operation note and prescription');
        }
    }

    protected function map_autogenerate_prescription_data($enabled, $set = null)
    {
        if ($enabled && is_null($set)) {
            $set = MedicationSet::model()->findByAttributes(['name' => 'Post-op']);
        }

        $set_id = is_object($set) ? $set->id : ($set ?? null);

        return [
            'auto_generate_prescription_after_ophtroperationnote' => $enabled,
            'auto_generate_prescription_after_ophtroperationnote_set_id' => $set_id
        ];
    }

    protected function getGenericProcedures()
    {
        if (is_null(static::$generic_procedure_ids)) {
            static::$generic_procedure_ids = \Yii::app()->db->createCommand(
                'SELECT DISTINCT proc.id FROM proc ' .
                'LEFT JOIN ophtroperationnote_procedure_element pe ON pe.procedure_id = proc.id ' .
                'WHERE pe.id IS NULL'
            )->queryColumn();
        }

        return static::$generic_procedure_ids;
    }

    protected function getOperationNoteElementsFormData($eye_id, $anaethetist, $procedure)
    {
        // Because the elements are being made instead of created, using EventFactory and makeAsFormData/makeWithFormData returns
        // an empty array because it currently relies on an afterCreate call to generate the element instances.
        // As such the elements are being made in the list below instead.
        //
        // TODO Revise this
        $elements = [
            Element_OphTrOperationnote_SiteTheatre::factory()->make(['event_id' => null]),
            Element_OphTrOperationnote_Surgeon::factory()->make(['event_id' => null]),
            Element_OphTrOperationnote_ProcedureList::factory()->withProcedures([$procedure])->make(['event_id' => null, 'eye_id' => $eye_id]),
            Element_OphTrOperationnote_GenericProcedure::factory()->forProcedure($procedure)->make(['event_id' => null]),
            Element_OphTrOperationnote_Anaesthetic::factory()->withAnaestheticType()->withAnaestheticAgent()->withAnaestheticDelivery()->make(['event_id' => null, 'anaesthetist_id' => $anaethetist]),
            Element_OphTrOperationnote_PostOpDrugs::factory()->make(['event_id' => null]),
            Element_OphTrOperationnote_Comments::factory()->make(['event_id' => null]),
        ];

        // Parahprased from mapInstanceToFormData in OphTrOperationnoteFactory
        return array_reduce(
            $elements,
            fn ($form_data, $element) => array_merge($form_data, get_class($element)::factory()::generateFormData($element)),
            []
        );
    }
}
