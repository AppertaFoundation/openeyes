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

namespace OEModule\OphCiExamination\factories\models;

use OE\factories\ModelFactory;
use OE\factories\models\EventFactory;
use OEModule\OphCiExamination\models\OphCiExamination_CXL_CL_Removed;
use OEModule\OphCiExamination\models\OphCiExamination_CXL_Quality_Score;
use SplitEventTypeElement;

class Element_OphCiExamination_KeratometryFactory extends ModelFactory
{
    /**
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'event_id' => EventFactory::forModule('OphCiExamination'),
            'eye_id' => $this->faker->randomElement([SplitEventTypeElement::LEFT, SplitEventTypeElement::RIGHT, SplitEventTypeElement::BOTH])
        ];
    }

    public function rightSideOnly()
    {
        return $this->state(function ($attributes) {
            return [
                'eye_id' => SplitEventTypeElement::RIGHT
            ];
        });
    }

    public function leftSideOnly()
    {
        return $this->state(function ($attributes) {
            return [
                'eye_id' => SplitEventTypeElement::LEFT
            ];
        });
    }

    public function bothSided()
    {
        return $this->state(function ($attributes) {
            return [
                'eye_id' => SplitEventTypeElement::BOTH
            ];
        });
    }

    protected function getUnresolvedAttributes()
    {
        // as a final state, we are getting generated data for the selected sided keys
        // this pattern may be abstracted to a final state definition in the future
        // if it transpires it's useful
        $this->state(
            function (array $attributes) {
                return array_merge(
                    SplitEventTypeElement::eyeHasSide('right', $attributes['eye_id']) ? $this->getAttributesForPrefix('right_', $attributes) : [],
                    SplitEventTypeElement::eyeHasSide('left', $attributes['eye_id']) ? $this->getAttributesForPrefix('left_', $attributes) : []
                );
            }
        );

        return parent::getUnresolvedAttributes();
    }

    protected function getAttributesForPrefix($prefix, $attributes = [])
    {
        $independent_attrs = [
            'ba_index_value' => $this->faker->randomFloat(1, 0, 999),
            'anterior_k1_value' => $this->faker->randomFloat(1, 0, 150),
            'axis_anterior_k1_value' => $this->faker->randomFloat(1, -150, -1),
            'axis_anterior_k2_value' => $this->faker->randomFloat(1, -150, -1),
            // no rules in the element when the factory was being created, so this is a guess
            'posterior_k2_value' => $this->faker->randomFloat(1, 1, 150),
            'thinnest_point_pachymetry_value' => $this->faker->numberBetween(10, 800),
            'quality_front' => OphCiExamination_CXL_Quality_Score::factory()->useExisting(),
            'quality_back' => OphCiExamination_CXL_Quality_Score::factory()->useExisting(),
            'cl_removed' => OphCiExamination_CXL_CL_Removed::factory()->useExisting(),
            'flourescein_value' => $this->faker->randomElement([0, 1])
        ];

        foreach ($independent_attrs as $attr => $value) {
            if (!array_key_exists("$prefix$attr", $attributes)) {
                $attributes["$prefix$attr"] = $value;
            }
        }

        if (!array_key_exists("{$prefix}anterior_k2_value", $attributes)) {
            $attributes["{$prefix}anterior_k2_value"] = $this->faker->randomFloat(1, $attributes["{$prefix}anterior_k1_value"], 150);
        }

        if (!array_key_exists("{$prefix}kmax_value", $attributes)) {
            $attributes["{$prefix}kmax_value"] = $this->faker->randomFloat(1, $attributes["{$prefix}anterior_k2_value"], 150);
        }

        return $attributes;
    }
}
