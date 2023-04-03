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


use OEModule\OphCiExamination\models\Element_OphCiExamination_Refraction;
use OEModule\OphCiExamination\models\interfaces\SidedData;
use OEModule\OphCiExamination\models\OphCiExamination_Refraction_Reading;
use OEModule\OphCiExamination\models\OphCiExamination_Refraction_Type;

trait InteractsWithRefraction
{
    use \WithFaker;
    use \InteractsWithEventTypeElements;

    protected function generateSavedRefractionWithReadings($attrs = [])
    {
        $element = new Element_OphCiExamination_Refraction();
        $element->setAttributes($this->generateRefractionData($attrs));

        return $this->saveElement($element);
    }

    protected function generateRefractionData($attrs = [])
    {
        if (!isset($attrs['eye_id'])) {
            $attrs['eye_id'] = SidedData::RIGHT | SidedData::LEFT;
        }

        foreach (['right' => SidedData::RIGHT, 'left' => SidedData::LEFT] as $side => $eye_id) {
            if (((int)$attrs['eye_id'] & SidedData::LEFT) === SidedData::LEFT) {
                if (!isset($attrs["{$side}_notes"])) {
                    $attrs["{$side}_notes"] = $this->faker->sentences(2, true);
                }
                if (!isset($attrs["{$side}_readings"])) {
                    $attrs["{$side}_readings"] = [$this->generateRefractionReading(['eye_id' => $eye_id])];
                }
            }
        }

        return $attrs;
    }

    protected function generateRefractionReadingData($attrs = [])
    {
        $data = array_merge([
            'sphere' => $this->faker->unique()->numberBetween(-45, 45),
            'cylinder' => $this->faker->unique()->numberBetween(-25, 25),
            'axis' => $this->faker->unique()->numberBetween(-180, 180),
            'type_id' => $this->getRandomLookup(OphCiExamination_Refraction_Type::class)->getPrimaryKey()
        ], $attrs);

        if (array_key_exists('type_other', $data)) {
            $data['type_id'] = null;
        }

        return $data;
    }

    protected function generateRefractionReading($attrs = [])
    {
        $reading = new OphCiExamination_Refraction_Reading();
        $reading->setAttributes($this->generateRefractionReadingData($attrs));

        return $reading;
    }
}
