<?php

/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2023
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2023, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

use OE\factories\ModelFactory;
use OphDRPrescription_DispenseCondition;
use OphDrPrescription_DispenseCondition_Institution;
use OphDrPrescription_DispenseLocation;

class OphDrPrescription_DispenseConditionFactory extends ModelFactory
{
    public function definition(): array
    {
        return [
            'name' => $this->faker->word(),
        ];
    }

    /**
     * @param Institution|InstitutionFactory|string|int $institution
     */
    public function withInstitution($institution)
    {
        return $this->afterCreating(function (OphDRPrescription_DispenseCondition $dispense_condition) use ($institution) {
            ModelFactory::factoryFor(OphDRPrescription_DispenseCondition_Institution::class)
                ->create([
                    'dispense_condition_id' => $dispense_condition->id,
                    'institution_id' => $institution
                ]);
        });
    }

    /**
     * @param Institution|InstitutionFactory|string|int $institution
     */
    public function withDispenseLocation($institution)
    {
        return $this->afterCreating(function (OphDrPrescription_DispenseCondition $dispense_condition) use ($institution) {
            $dispense_location = ModelFactory::factoryFor(OphDrPrescription_DispenseLocation::class)
                ->withInstitution($institution)
                ->create();

            $dispense_condition_institution = OphDrPrescription_DispenseCondition_Institution::model()->find(
                "dispense_condition_id=:dispense_condition_id AND institution_id=:institution_id",
                [
                    ":dispense_condition_id" => $dispense_condition->id,
                    ":institution_id" => $institution->id
                ]
            );

            $dispense_condition_institution->dispense_location_institutions = [$dispense_location];
            $dispense_condition_institution->save();
        });
    }
}
