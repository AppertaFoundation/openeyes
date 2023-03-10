<?php
/**
 * (C) Apperta Foundation, 2022
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2022, Apperta Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

namespace OE\factories\models;

use Institution;
use OE\factories\ModelFactory;
use PathwayType;
use PatientIdentifier;
use PatientIdentifierType;
use WorklistDefinition;

class WorklistDefinitionFactory extends ModelFactory
{
    public function definition(): array
    {
        return [
            'name' => $this->faker->words(3, true),
            'worklist_name' => $this->faker->words(3, true),
            'start_time' => '09:00',
            'end_time' => '17:00',
            'active_from' => $this->faker->dateTimeBetween('-1 week')->format('Y-m-d'),
            'pathway_type_id' => function (array $attributes) {
                return PathwayType::factory()->state([
                    'institution_id' => $attributes['patient_identifier_type_id']->institution_id
                ]);
            },
            'patient_identifier_type_id' => PatientIdentifierType::factory()->useExisting()
        ];
    }

    /**
     * A state to specify the step types that should be part of the default pathway
     * for the PathwayType of the generated WorklistDefinition.
     *
     * @see PathwayTypeStepFactory
     * @param array $type_short_names
     * @return self
     */
    public function withStepsOfType(array $type_short_names = []): self
    {
        return $this->state(function (array $attributes) use ($type_short_names) {
            if (($attributes['pathway_type_id'] ?? null) instanceof ModelFactory) {
                $attributes['pathway_type_id'] = $attributes['pathway_type_id']
                    ->withStepsOfType($type_short_names);
            }

            return $attributes;
        });
    }

    public function forInstitution(Institution $institution): self
    {
        return $this->state([
            'pathway_type_id' => PathwayType::factory()->state([
                'institution_id' => $institution->id
            ]),
            'patient_identifier_type_id' => PatientIdentifierType::factory()->useExisting([
                'institution_id' => $institution->id
            ])
        ]);
    }
}
