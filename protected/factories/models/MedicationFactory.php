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

namespace OE\factories\models;

use Medication;
use MedicationAttributeAssignment;
use MedicationAttributeOption;
use OE\factories\ModelFactory;

class MedicationFactory extends ModelFactory
{
    public function definition(): array
    {
        return [
            'source_type' => $this->faker->randomElement([Medication::SOURCE_TYPE_DMD, Medication::SOURCE_TYPE_LOCAL]),
            'preferred_term' => $this->faker->words(2, true),
            // prefixed with 'F' to prevent collision with existing code numerics
            'preferred_code' => $this->faker->regexify('F\d{5,9}')
        ];
    }

    public function local(): self
    {
        return $this->state([
            'source_type' => Medication::SOURCE_TYPE_LOCAL
        ]);
    }

    public function dmd(): self
    {
        return $this->state([
            'source_type' => Medication::SOURCE_TYPE_DMD
        ]);
    }


    public function preservativeFree(): self
    {
        return $this->afterCreating(function (Medication $medication) {
            MedicationAttributeAssignment::factory()->create([
                'medication_id' => $medication->id,
                'medication_attribute_option_id' => MedicationAttributeOption::factory()->forAttribute(Medication::ATTR_PRESERVATIVE_FREE)
            ]);
            // ensure relation will be loaded in any use of the generated model
            $medication->refresh();
        });
    }

    public function prescribable(): self
    {
        return $this->state([
            'is_prescribable' => true
        ]);
    }
}
