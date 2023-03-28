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

namespace OEModule\OphGeneric\factories\models;

use OE\factories\ModelFactory;
use OEModule\OphGeneric\models\HFA;
use OEModule\OphGeneric\models\HFAEntry;
use \Eye;

class HFAEntryFactory extends ModelFactory
{
    /**
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'eye_id' => Eye::factory()->useExisting()->create()->id,
            'element_id' => HFA::factory(),
            'mean_deviation' => $this->faker->numberBetween(HFAEntry::MEAN_DEVIATION_MIN, HFAEntry::MEAN_DEVIATION_MAX),
            'visual_field_index' => $this->faker->numberBetween(HFAEntry::VISUAL_FIELD_INDEX_MIN, HFAEntry::VISUAL_FIELD_INDEX_MAX)
        ];
    }
}
