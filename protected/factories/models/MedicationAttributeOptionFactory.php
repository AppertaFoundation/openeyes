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

use MedicationAttribute;
use OE\factories\ModelFactory;

class MedicationAttributeOptionFactory extends ModelFactory
{
    public function definition(): array
    {
        return [
            'medication_attribute_id' => MedicationAttribute::factory()->useExisting(),
            // not expected to have much utility, but added for completeness
            'value' => $this->faker->words(3, true),
            'description' => $this->faker->sentences(2, true)
        ];
    }

    public function forAttribute(string $attribute_label): self
    {
        return $this->state([
            'medication_attribute_id' => MedicationAttribute::factory()
                ->useExisting([
                    'name' => $attribute_label
                ]),
        ]);
    }
}
