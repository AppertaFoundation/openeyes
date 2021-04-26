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

use OEModule\OphCiExamination\models\Retinoscopy;
use OEModule\OphCiExamination\models\Retinoscopy_WorkingDistance;

trait InteractsWithRetinoscopy
{
    use \WithFaker;
    use \InteractsWithEventTypeElements;

    protected function generateRetinoscopyData($attrs = [])
    {
        return array_merge([
            'right_working_distance_id' => $this->getRandomLookup(Retinoscopy_WorkingDistance::class)->id,
            'right_angle' => $this->faker->numberBetween(0, 180),
            'right_power1' => $this->faker->randomFloat(2, -30, 30),
            'right_power2' => $this->faker->randomFloat(2, -30, 30),
            'right_dilated' => $this->faker->randomElement(['1', '0']),
            'right_refraction' => $this->fakeRefraction(),
            'right_eyedraw' => $this->faker->word(),
            'right_comments' => $this->faker->words(12, true),
            'left_working_distance_id' => $this->getRandomLookup(Retinoscopy_WorkingDistance::class)->id,
            'left_angle' => $this->faker->numberBetween(0, 180),
            'left_power1' => $this->faker->randomFloat(2, -30, 30),
            'left_power2' => $this->faker->randomFloat(2, -30, 30),
            'left_dilated' => $this->faker->randomElement(['1', '0']),
            'left_refraction' => $this->fakeRefraction(),
            'left_eyedraw' => $this->faker->word(),
            'left_comments' => $this->faker->words(12, true)
        ], $attrs);
    }

    /**
     * Should only be used in conjunction with WithTransactions trait
     *
     * @param array $data
     * @return mixed
     * @throws \Exception
     */
    protected function generateSavedRetinoscopyElementWithReadings($data = [])
    {
        $element = new Retinoscopy();
        if (!isset($attrs['eye_id'])) {
            $element->setHasLeft();
            $element->setHasRight();
        }

        $element->setAttributes($this->generateRetinoscopyData($data));

        return $this->saveElement($element);
    }
}