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

use OE\factories\ModelFactory;
use ElementType;
use EventSubtype;
use EventSubtypeElementEntry;

class EventSubtypeFactory extends ModelFactory
{
    public function definition(): array
    {
        return [
            'event_subtype' => $this->faker->word(),
            'dicom_modality_code' => $this->faker->word(),
            'icon_name' => $this->faker->word(),
            'display_name' => $this->faker->words(rand(1,3), true),
            'manual_entry' => false
        ];
    }

    public function allowManualEntry(): self
    {
        return $this->state(function () {
            return [
                'manual_entry' => true
            ];
        });
    }

    public function withElementTypes(array $element_types = []): self
    {
        return $this->afterCreating(function (EventSubtype $event_subtype) use ($element_types) {
            $element_type_entries = [];
            foreach ($element_types as $element_type) {
                $element_type_entries[] = EventSubtypeElementEntry::factory()->create([
                                                'element_type_id' => ElementType::factory()->useExisting([
                                                    'class_name' => $element_type
                                                ]),
                                                'event_subtype' => $event_subtype->event_subtype
                                            ]);
            }
            $event_subtype->element_type_entries = $element_type_entries;
        });
    }
}
