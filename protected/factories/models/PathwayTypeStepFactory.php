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

use OE\factories\ModelFactory;
use OE\factories\models\traits\MapsDisplayOrderForFactory;
use PathwayStepType;
use PathwayType;

class PathwayTypeStepFactory extends ModelFactory
{
    use MapsDisplayOrderForFactory;

    protected string $display_order_attribute = 'queue_order';

    public function definition(): array
    {
        return [
            'pathway_type_id' => PathwayType::factory(),
            'step_type_id' => PathwayStepType::factory()->useExisting(),
            'short_name' => $this->faker->word(),
            'long_name' => $this->faker->words(3, true)
        ];
    }

    public function task(): self
    {
        return $this->ofStepType('Task');
    }

    /**
     * This resolves PathwayStepType from the short_name attribute
     * allowing the use of standardised step types in pathway generation
     *
     * @param string $step_type_short_name
     * @return self
     */
    public function ofStepType(string $step_type_short_name): self
    {
        return $this->state(function (array $attributes) use ($step_type_short_name) {
            return [
                'step_type_id' => PathwayStepType::factory()->useExisting([
                    'short_name' => ucwords(strtolower($step_type_short_name))
                ])
            ];
        });
    }
}
