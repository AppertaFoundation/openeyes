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

use OEModule\OphCiExamination\models\CorrectionType;
use OEModule\OphCiExamination\models\PrismFusionRange;
use OEModule\OphCiExamination\models\PrismFusionRange_Entry;

trait InteractsWithPrismFusionRange
{
    use \WithFaker;
    use \InteractsWithEventTypeElements;

    protected function generateSavedPrismFusionRangeWithEntries($entry_count = 1)
    {
        $element = new PrismFusionRange();
        $element->setAttributes($this->generatePrismFusionRangeData($entry_count));

        return $this->saveElement($element);
    }

    protected function generatePrismFusionRangeData($entry_count = 1)
    {
        $data = [
            'comments' => $this->faker->words(9, true),
            'entries' => []
        ];
        for ($i = 0; $i < $entry_count; $i++) {
            $data['entries'][] = $this->generatePrismFusionRangeEntryData();
        }

        return $data;
    }

    protected function generatePrismFusionRangeEntryData()
    {
        return [
            'prism_over_eye_id' => $this->faker->randomElement([\Eye::RIGHT, \Eye::LEFT]),
            'near_bo' => $this->faker->numberBetween(1, 45),
            'near_bi' => $this->faker->numberBetween(1, 45),
            'near_bu' => $this->faker->numberBetween(1, 25),
            'near_bd' => $this->faker->numberBetween(1, 25),
            'distance_bo' => $this->faker->numberBetween(1, 45),
            'distance_bi' => $this->faker->numberBetween(1, 45),
            'distance_bu' => $this->faker->numberBetween(1, 25),
            'distance_bd' => $this->faker->numberBetween(1, 25),
            'correctiontype_id' => $this->getRandomLookup(CorrectionType::class)->id,
            'with_head_posture' => $this->faker->randomElement(['0', '1'])
        ];
    }
}