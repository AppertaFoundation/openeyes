<?php

/**
 * (C) Apperta Foundation, 2020
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2020, Apperta Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

namespace OEModule\OphCiExamination\tests\traits;

use OEModule\OphCiExamination\models\NinePositions;
use OEModule\OphCiExamination\models\NinePositions_AlignmentForGaze;
use OEModule\OphCiExamination\models\NinePositions_HorizontalEDeviation;
use OEModule\OphCiExamination\models\NinePositions_HorizontalXDeviation;
use OEModule\OphCiExamination\models\NinePositions_Movement;
use OEModule\OphCiExamination\models\NinePositions_MovementForGaze;
use OEModule\OphCiExamination\models\NinePositions_Reading;
use OEModule\OphCiExamination\models\NinePositions_VerticalDeviation;

trait InteractsWithNinePositions
{
    use \InteractsWithEventTypeElements;
    use \WithFaker;

    /**
     * @param int $reading_count
     * @return NinePositions
     * @throws \Exception
     */
    protected function generateSavedNinePositionsWithReadings($reading_count = 1)
    {
        $element = new NinePositions();
        $readings = [];
        for ($i = 0; $i < $reading_count; $i++) {
            $readings[] = $this->generateNinePositionsReadingData();
        }
        $element->readings = $readings;

        return $this->saveElement($element);
    }

    protected function generateNinePositionsReadingData()
    {
        $reading_data = [
            'with_head_posture' => $this->faker->randomElement(
                [NinePositions_Reading::$WITH_HEAD_POSTURE, NinePositions_Reading::$WITHOUT_HEAD_POSTURE]
            ),
            'with_correction' => $this->faker->randomElement(
                [NinePositions_Reading::WITH_CORRECTION, NinePositions_Reading::WITHOUT_CORRECTION]
            ),
            'wong_supine_positive' => $this->faker->randomElement(['1', '0']),
            'hess_chart' => $this->faker->randomElement(['1', '0']),
            'comments' => $this->faker->words(rand(3, 12), true),
            'right_dvd' => $this->faker->numberBetween(0, 50),
            'left_dvd' => $this->faker->numberBetween(0, 50),
            'right_eyedraw' => 'foo',
            'left_eyedraw' => 'foo'
        ];

        $reading_data['alignments'] = array_map(
            function ($gaze_type) {
                return $this->generateAlignmentForGazeData($gaze_type);
            },
            $this->getRandomNumberOfUniqueElements(NinePositions_AlignmentForGaze::GAZE_TYPES)
        );

        $reading_data['movements'] = array_map(
            function ($gaze_type) {
                return $this->generateMovementForGazeData($gaze_type);
            },
            $this->getRandomNumberOfUniqueElements(NinePositions_MovementForGaze::GAZE_TYPES)
        );

        return $reading_data;
    }

    /**
     * @return NinePositions_AlignmentForGaze
     */
    protected function generateAlignmentForGaze()
    {

        $alignment = new NinePositions_AlignmentForGaze();
        $alignment->setAttributes($this->generateAlignmentForGazeData());

        return $alignment;
    }

    protected function generateAlignmentForGazeData($gaze_type = null)
    {
        $horizontal_attribute = $this->faker->randomElement([
            'horizontal_prism_position',
            'horizontal_e_deviation_id',
            'horizontal_x_deviation_id'
        ]);
        $vertical_attribute = $this->faker->randomElement(['vertical_prism_position', 'vertical_deviation_id']);

        $data = $this->getValidAttributesForAlignment([
            'gaze_type',
            'horizontal_angle',
            $horizontal_attribute,
            'vertical_angle',
            $vertical_attribute
        ]);

        if ($gaze_type !== null) {
            $data['gaze_type'] = $gaze_type;
        }

        return $data;
    }

    protected function getValidAttributesForAlignment($attributes = [])
    {
        return array_filter([
            'gaze_type' => $this->faker->randomElement(NinePositions_AlignmentForGaze::GAZE_TYPES),
            'horizontal_angle' => $this->faker->numberBetween(0, 50),
            'horizontal_prism_position' => $this->faker->randomElement(NinePositions_AlignmentForGaze::HORIZONTAL_PRISMS),
            'horizontal_e_deviation_id' => $this->getRandomLookup(NinePositions_HorizontalEDeviation::class)->id,
            'horizontal_x_deviation_id' => $this->getRandomLookup(NinePositions_HorizontalXDeviation::class)->id,
            'vertical_angle' => $this->faker->numberBetween(0, 20),
            'vertical_prism_position' => $this->faker->randomElement(NinePositions_AlignmentForGaze::VERTICAL_PRISMS),
            'vertical_deviation_id' => $this->getRandomLookup(NinePositions_VerticalDeviation::class)->id
        ], function ($key) use ($attributes) {
            return in_array($key, $attributes);
        }, ARRAY_FILTER_USE_KEY);
    }

    /**
     * @return NinePositions_MovementForGaze
     */
    protected function generateMovementForGaze()
    {
        $movement = new NinePositions_MovementForGaze();
        $movement->setAttributes($this->generateMovementForGazeData());

        return $movement;
    }

    protected function generateMovementForGazeData($gaze_type = null)
    {
        $data = [
            'movement_id' => $this->getRandomLookup(NinePositions_Movement::class)->id,
            'eye_id' => $this->faker->randomElement([\Eye::RIGHT, \Eye::LEFT])
        ];
        $data['gaze_type'] = ($gaze_type === null)
            ? $this->faker->randomElement(NinePositions_MovementForGaze::GAZE_TYPES)
            : $gaze_type;

        return $data;
    }

}