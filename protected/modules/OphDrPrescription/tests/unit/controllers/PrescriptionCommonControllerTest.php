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

use OEModule\OphDrPGDPSD\models\{
    OphDrPGDPSD_PGDPSD,
    OphDrPGDPSD_PGDPSDMeds
};

/**
 * @group sample-data
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
class PrescriptionCommonControllerTest extends OEDbTestCase
{
    use WithTransactions;
    use MakesApplicationRequests;
    use MocksSession;
    use HasFormAssertions;

    public const URL_BASE = '/OphDrPrescription/PrescriptionCommon/';

    protected $admin_user;
    protected $institution;
    protected $firm;

    public function setUp(): void
    {
        parent::setUp();

        $this->institution = Institution::factory()->useExisting()->create();
        $this->firm = Firm::factory()->useExisting(['institution_id' => $this->institution])->create();
        $this->admin_user = User::factory()->useExisting(['id' => 1])->create();

        $this->mockCurrentContext($this->firm, null, $this->institution);
    }

    /** @test */
    public function set_form_ignores_existing_operation_note_laterality()
    {
        $patient = $this->createOperationNoteProcedureListPatient($this->firm);

        $set = MedicationSet::factory()->create();

        $this->createMedicationSetItemWithLaterality($set);
        $this->createMedicationSetItemWithLaterality($set);

        $url = self::URL_BASE . 'SetForm?' . http_build_query([
            'key' => 0,
            'patient_id' => $patient->id,
            'set_id' => $set->id
        ]);

        $results = $this->actingAs($this->admin_user)
                        ->get($url)
                        ->assertSuccessful()
                        ->crawl();

        $this->assertEmptyLateralities(2, $results);
    }

    /** @test */
    public function item_form_ignores_existing_operation_note_laterality()
    {
        $patient = $this->createOperationNoteProcedureListPatient($this->firm);

        $drug = $this->createMedicationSetItemWithLaterality();

        $url = self::URL_BASE . 'ItemForm?' . http_build_query([
            'key' => 0,
            'patient_id' => $patient->id,
            'drug_id' => $drug->medication_id,
        ]);

        $results = $this->actingAs(User::model()->findByPk(1))
                        ->get($url)
                        ->assertSuccessful()
                        ->crawl();

        $this->assertEmptyLateralities(1, $results);
    }

    /** @test */
    public function pgd_form_ignores_existing_operation_note_laterality()
    {
        $patient = $this->createOperationNoteProcedureListPatient($this->firm);

        $pgd = $this->createPGDSetWithLaterality();

        $url = self::URL_BASE . 'PGDForm?' . http_build_query([
            'key' => 0,
            'patient_id' => $patient->id,
            'pgd_id' => $pgd->id,
        ]);

        $results = $this->actingAs(User::model()->findByPk(1))
                        ->get($url)
                        ->assertSuccessful()
                        ->crawl();

        $this->assertEmptyLateralities(1, $results);
    }

    protected function createOperationNoteProcedureListPatient($firm)
    {
        $event = Event::factory()->forModule('OphTrOperationnote')
                                 ->forFirm($firm)
                                 ->withElement(Element_OphTrOperationnote_ProcedureList::class)
                                 ->create();

        return $event->episode->patient;
    }

    protected function createMedicationSetItemWithLaterality($owning_set = null)
    {
        $factory = MedicationSetItem::factory();

        if ($owning_set) {
            $factory->forMedicationSet($owning_set);
        }

        return $factory->create([
            'default_route_id' => MedicationRoute::factory()->useExisting(['has_laterality' => true]),
            'default_dose' => 1.0,
            'default_frequency_id' => MedicationFrequency::factory()->useExisting(),
            'default_duration_id' => MedicationDuration::factory()->useExisting()
        ]);
    }

    protected function createPGDSetWithLaterality()
    {
        $pgd = OphDrPGDPSD_PGDPSD::factory()
            ->pgd()
            ->forInstitution($this->institution)
            ->create();

        OphDrPGDPSD_PGDPSDMeds::factory()
            ->forPGDPSD($pgd)
            ->create([
                'route_id' => MedicationRoute::factory()->useExisting(['has_laterality' => true]),
            ]);

        return $pgd;
    }

    protected function assertEmptyLateralities($expected_result_count, $crawled_results)
    {
        $results = $this->gatherSelectInputs($crawled_results->filter('[name*="[laterality]"]'));

        $this->assertCount($expected_result_count, $results);

        foreach ($results as $result) {
            list($name, $value) = $result;

            $this->assertEmpty($value, $name . ' has inherited the value of ' . $value . ' from the created Element_OphTrOperationnote_ProcedureList');
        }
    }
}
