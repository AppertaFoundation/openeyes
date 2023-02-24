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

namespace OEModule\OphCiExamination\factories\models;

use OE\factories\ModelFactory;
use OE\factories\models\EventFactory;
use OEModule\OphCiExamination\models\interfaces\SidedData;
use OEModule\OphCiExamination\models\Retinoscopy;
use OEModule\OphCiExamination\models\Retinoscopy_WorkingDistance;

class RetinoscopyFactory extends ModelFactory
{
    /**
     * @return array
     */
    public function definition(): array
    {
        return [
            'event_id' => EventFactory::forModule('OphCiExamination'),
            'eye_id' => $this->faker->randomElement([SidedData::RIGHT, SidedData::LEFT, SidedData::BOTH])
        ];
    }

    public function rightSideOnly()
    {
        return $this->state(function ($attributes) {
            return [
                'eye_id' => SidedData::RIGHT
            ];
        });
    }

    public function leftSideOnly()
    {
        return $this->state(function ($attributes) {
            return [
                'eye_id' => SidedData::LEFT
            ];
        });
    }

    public function bothSided()
    {
        return $this->state(function ($attributes) {
            return [
                'eye_id' => SidedData::BOTH
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
                    Retinoscopy::isValueForRight($attributes['eye_id']) ? $this->getAttributesForPrefix('right_', $attributes) : [],
                    Retinoscopy::isValueForLeft($attributes['eye_id']) ? $this->getAttributesForPrefix('left_', $attributes) : []
                );
            }
        );

        return parent::getUnresolvedAttributes();
    }

    protected function getAttributesForPrefix($prefix, array $attributes = []): array
    {
        $definition = [
            'working_distance_id' => Retinoscopy_WorkingDistance::factory()->useExisting(),
            'angle' => $this->faker->numberBetween(0, 180),
            'power1' => $this->faker->randomFloat(2, -30, 30),
            'power2' => $this->faker->randomFloat(2, -30, 30),
            'dilated' => $this->faker->boolean(),
            'refraction' => $this->faker->regexify('[\+-]\d\.\d\d \/ [\+-]\d\.\d\d x \d\d'),
            'eyedraw' => '[{"scaleLevel": 1,"version":1.1,"subclass":"RetinoscopyPowerCross","rotation":360,"powerSign1":"+","powerSign2":"+","powerInt1":0.00,"powerInt2":0.00,"powerDp1":"00","powerDp2":"00","order":0}, {"tags":[]}]',
        ];

        foreach ($definition as $attr => $value) {
            if (!array_key_exists("$prefix$attr", $attributes)) {
                $attributes["$prefix$attr"] = $value;
            }
        }

        return $attributes;
    }
}
