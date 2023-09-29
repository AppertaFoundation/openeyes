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

namespace OEModule\OphCiPhasing\seeders;

use OE\seeders\BaseSeeder;
use OE\factories\models\EventFactory;
use OE\seeders\resources\SeededEventResource;

use Patient;
use OEModule\OphCiPhasing\models\{
    Element_OphCiPhasing_IntraocularPressure,
    OphCiPhasing_Instrument
};

class SavingSeeder extends BaseSeeder
{
    public function __invoke(): array
    {
        $patient = Patient::factory()->create();

        $instrument = OphCiPhasing_Instrument::factory()->useExisting(['active' => true])->create();

        $left_reading = $this->getApp()->dataGenerator->faker()->numberBetween(1, 100);
        $right_reading = $this->getApp()->dataGenerator->faker()->numberBetween(1, 100);

        if ($this->getSeederAttribute('for') === 'create') {
            return [
                'patientId' => $patient->id,
                'instrumentId' => $instrument->id,
                'newLeftReading' => $left_reading,
                'newRightReading' => $right_reading,
            ];
        } else {
            $event = EventFactory::forModule('OphCiPhasing')
                   ->forPatient($patient)
                   ->withElement(
                       Element_OphCiPhasing_IntraocularPressure::class,
                       [
                           ['forLeftInstrument', $instrument],
                           ['forRightInstrument', $instrument],
                           ['withReadingsOnBothSides', 1]
                       ]
                   )
                   ->create();

            return [
                'patientId' => $patient->id,
                'event' => SeededEventResource::from($event)->toArray(),
                'instrumentId' => $instrument->id,
                'newLeftReading' => $left_reading,
                'newRightReading' => $right_reading,
            ];
        }
    }
}
