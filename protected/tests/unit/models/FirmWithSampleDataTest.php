<?php
/**
 * (C) Apperta Foundation, 2023
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2023, Apperta Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */


/**
 * @group sample-data
 * @group firm
 */
class FirmWithSampleDataTest extends OEDbTestCase
{
    use MocksSession;
    use WithTransactions;
    use HasModelAssertions;

    /** @test */
    public function default_service_firm_returns_with_string_id()
    {
        list($not_owning_firm, $expected_firm) = $this->getFirmsForDefaultServiceFirmTests();

        $this->assertModelIs(
            $expected_firm,
            Firm::getDefaultServiceFirmForSubspecialty(
                (string) $not_owning_firm->subspecialty_id,
                $not_owning_firm->institution
            )
        );
    }

    /** @test */
    public function default_service_firm_returns_with_int_id()
    {
        list($not_owning_firm, $expected_firm) = $this->getFirmsForDefaultServiceFirmTests();

        $this->assertModelIs(
            $expected_firm,
            Firm::getDefaultServiceFirmForSubspecialty(
                (int) $not_owning_firm->subspecialty_id,
                $not_owning_firm->institution
            )
        );
    }

    /** @test */
    public function default_service_firm_returns_with_subspecialty_instance()
    {
        list($not_owning_firm, $expected_firm) = $this->getFirmsForDefaultServiceFirmTests();

        $this->assertModelIs(
            $expected_firm,
            Firm::getDefaultServiceFirmForSubspecialty(
                $not_owning_firm->subspecialty,
                $not_owning_firm->institution
            )
        );
    }

    /** @test */
    public function firm_returns_the_default_service_firm_for_its_subspecialty_when_current_institution_matches()
    {
        list($not_owning_firm, $expected_firm) = $this->getFirmsForDefaultServiceFirmTests();
        $this->mockCurrentInstitution($not_owning_firm->institution);

        $this->assertModelIs($expected_firm, $not_owning_firm->getDefaultServiceFirm());
    }

    /** @test */
    public function firm_returns_null_default_service_firm_with_no_subspecialty()
    {
        $firm = Firm::factory()->create(['subspecialty_id' => null]);

        $this->assertNull($firm->getDefaultServiceFirm());
    }

    protected function getFirmsForDefaultServiceFirmTests(): array
    {
        $not_owning_firm = Firm::factory()
            ->withNewSubspecialty()
            ->cannotOwnEpisode()
            ->create([
                'institution_id' => Institution::factory()
            ]);

        $owning_firm = Firm::factory()
            ->canOwnEpisode()
            ->create([
                'subspecialty_id' => $not_owning_firm->subspecialty_id,
                'institution_id' => $not_owning_firm->institution_id
            ]);

        return [$not_owning_firm, $owning_firm];
    }

    /** @test */
    public function update_duplicated_firm_should_not_pass_the_validation()
    {
        $original_firm = Firm::factory()->create();

        $firm_form_update = Firm::factory()->create([
            "institution_id" => $original_firm->institution_id,
            "subspecialty_id" => $original_firm->subspecialty_id
        ]);

        $firm_form_update->name = $original_firm->name;

        $this->assertAttributeInvalid($firm_form_update, 'name', 'already exists');
    }
}
