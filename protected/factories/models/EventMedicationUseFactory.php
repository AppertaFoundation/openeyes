<?php

/**
 * (C) Copyright Apperta Foundation 2023
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

namespace OE\factories\models;

use Event;
use Eye;
use Medication;
use MedicationDuration;
use MedicationFrequency;
use MedicationRoute;
use OphDrPrescription_DispenseCondition;
use OphDrPrescription_DispenseLocation;
use OE\factories\ModelFactory;

class EventMedicationUseFactory extends ModelFactory
{
    public function definition(): array
    {
        return [
            'event_id' => Event::factory(),
            'medication_id' => Medication::factory(),
            'start_date' => date("Y-m-d"),
            'dose' => $this->faker->randomDigit(),
            'dose_unit_term' => $this->faker->word(),
            'route_id' => MedicationRoute::factory()->useExisting(),
            'frequency_id' => MedicationFrequency::factory()->useExisting()
        ];
    }

    public function configure()
    {
        return $this->afterMaking(function ($instance) {
            if ($instance->route && $instance->route->isEyeRoute()) {
                $instance->laterality = $this->faker->randomElement([Eye::LEFT, Eye::RIGHT, Eye::BOTH]);
            }
        });
    }

    public function forEvent($event): self
    {
        return $this->state([
            'event_id' => $event
        ]);
    }

    public function prescribed(): self
    {
        return $this->state([
            'medication_id' => Medication::factory()->prescribable(),
            'prescribe' => 1,
            'duration_id' => MedicationDuration::factory()->useExisting(),
            'dispense_condition_id' => OphDrPrescription_DispenseCondition::factory(),
            'dispense_location_id' => OphDrPrescription_DispenseLocation::factory()
        ]);
    }

    public function forUsageType($usage_type): self
    {
        return $this->state([
            'usage_type' => $usage_type
        ]);
    }

    public function forUsageSubtype($usage_subtype): self
    {
        return $this->state([
            'usage_subtype' => $usage_subtype
        ]);
    }
}
