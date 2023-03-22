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
 * @copyright Copyright (C) 2014, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

/**
 * @group sample-data
 * @group patient-identifier-type
 */
class PatientIdentifierTypeTest extends ModelTestCase
{
    use WithTransactions;
    use WithFaker;

    protected $element_cls = PatientIdentifierType::class;

    /**
     * @covers PatientIdentifierType
     */
    public function testGetNextValueForIdentifierType_IncrementsCurrentHighestValueForIdentifierType()
    {
        list($patient_identifier_type, $current_highest_identifier) = $this->initialisePatientIdentifierTypeForTesting();

        // subtracting some value from the max value and setting that as the auto increment start value
        $auto_increment_start_value = $current_highest_identifier->value - 4 ;

        $patient_identifier_type_display_order = PatientIdentifierTypeDisplayOrder::factory()
            ->create([
                'patient_identifier_type_id' => $patient_identifier_type->id,
                'auto_increment_start' => $auto_increment_start_value
                ]);

        $patient_identifier_type_next_value = PatientIdentifierType::getNextValueForIdentifierType(
            $patient_identifier_type->id,
            $patient_identifier_type_display_order->auto_increment_start
        );

        // The expected value is the highest value from  after adding one to it.
        $this->assertEquals($current_highest_identifier->value + 1, $patient_identifier_type_next_value);
    }

    /** @test */
    public function ensure_next_highest_value_returns_configured_starting_value_when_current_maximum_is_lower()
    {
        list($patient_identifier_type, $current_highest_identifier) = $this->initialisePatientIdentifierTypeForTesting();

        // subtracting some value from the max value and setting that as the auto increment start value
        $auto_increment_start_value = $current_highest_identifier->value + 4;

        $patient_identifier_type_display_order = PatientIdentifierTypeDisplayOrder::factory()
            ->create([
                'patient_identifier_type_id' => $patient_identifier_type->id,
                'auto_increment_start' => $auto_increment_start_value
                ]);

        $patient_identifier_type_next_value = PatientIdentifierType::getNextValueForIdentifierType(
            $patient_identifier_type->id,
            $patient_identifier_type_display_order->auto_increment_start
        );

        // The expected value is the highest value from  after adding one to it.
        $this->assertEquals($auto_increment_start_value, $patient_identifier_type_next_value);
    }

    protected function initialisePatientIdentifierTypeForTesting(): array
    {
        $institution = Institution::factory()->create();
        $patient_identifier_type = PatientIdentifierType::factory()->local()->create([
            'institution_id' => $institution
        ]);

        Patient::factory()
            ->count(5)
            ->withIdentifierType($patient_identifier_type, function () { return $this->faker->unique()->numerify('######'); })
            ->create();

        $current_highest_identifier = PatientIdentifierHelper::getMaxIdentifier($patient_identifier_type->id);

        return [$patient_identifier_type, $current_highest_identifier];
    }
}
