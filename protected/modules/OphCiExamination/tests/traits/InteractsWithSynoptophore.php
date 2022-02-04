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

use OEModule\OphCiExamination\models\interfaces\SidedData;
use OEModule\OphCiExamination\models\Synoptophore;
use OEModule\OphCiExamination\models\Synoptophore_Deviation;
use OEModule\OphCiExamination\models\Synoptophore_Direction;
use OEModule\OphCiExamination\models\Synoptophore_ReadingForGaze;

trait InteractsWithSynoptophore
{
    use \InteractsWithEventTypeElements;
    use \WithFaker;

    protected function generateSynoptophoreData()
    {
        return [
            'angle_from_primary' => $this->faker->randomElement(Synoptophore::ANGLES_FROM_PRIMARY),
            'comments' => $this->faker->sentences(2, true)
        ];
    }

    protected function generateSynoptophoreReading()
    {
        $reading = new Synoptophore_ReadingForGaze();
        $reading->setAttributes($this->generateSynoptophoreReadingData());
        return $reading;
    }

    protected function generateSynoptophoreReadingData($attrs = [])
    {
        return array_merge(
            [
                'gaze_type' => $this->getRandomGazeType(),
                'horizontal_angle' => $this->faker->numberBetween(-40, 40),
                'vertical_power' => $this->faker->numberBetween(0, 50),
                'torsion' => $this->faker->numberBetween(0, 60),
                'direction_id' => $this->getRandomLookup(Synoptophore_Direction::class)->getPrimaryKey(),
                'deviation_id' => $this->getRandomLookup(Synoptophore_Deviation::class)->getPrimaryKey(),
                'eye_id' => $this->faker->randomElement([SidedData::RIGHT, SidedData::LEFT])
            ],
            $attrs
        );
    }

    protected function getRandomGazeType()
    {
        return $this->faker->randomElement(Synoptophore_ReadingForGaze::model()->getValidGazeTypes());
    }
}
