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
use OphDrPrescription_DispenseLocation;
use OphDrPrescription_DispenseLocation_Institution;

class OphDrPrescription_DispenseLocationFactory extends ModelFactory
{
    public function definition(): array
    {
        return [
            'name' => implode("-", $this->faker->words(3)),
            'display_order' => $this->faker->randomDigit()
        ];
    }

    /**
     * @param Institution|InstitutionFactory|string|int $institution
     */
    public function withInstitution($institution)
    {
        return $this->afterCreating(function (OphDrPrescription_DispenseLocation $dispense_location) use ($institution) {
            ModelFactory::factoryFor(OphDrPrescription_DispenseLocation_Institution::class)
                ->create([
                    'dispense_location_id' => $dispense_location->id,
                    'institution_id' => $institution
                ]);
        });
    }
}
