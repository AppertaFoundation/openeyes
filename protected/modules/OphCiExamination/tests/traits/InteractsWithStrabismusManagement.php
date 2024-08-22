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
use OEModule\OphCiExamination\models\StrabismusManagement;

trait InteractsWithStrabismusManagement
{
    use \InteractsWithEventTypeElements;
    use \WithFaker;

    protected function generateSavedStrabismusManagementWithEntries($entry_count = 1)
    {
        $element = new StrabismusManagement();
        $element->setAttributes($this->generateStrabismusManagementData($entry_count));

        return $this->saveElement($element);
    }

    protected function generateStrabismusManagementData($entry_count = 1)
    {
        $data = [
            'comments' => $this->faker->words(9, true),
            'entries' => []
        ];
        for ($i = 1; $i <= $entry_count; $i++) {
            $data['entries'][] = $this->generateStrabismusManagementEntryData();
        }

        return $data;
    }

    public function generateStrabismusManagementEntryData($attributes = [])
    {
        return array_merge(
            [
                'eye_id' => $this->faker->randomElement([SidedData::LEFT, SidedData::RIGHT, SidedData::LEFT | SidedData::RIGHT]),
                'treatment' => $this->faker->words(5, true),
                'treatment_options' => $this->faker->words(random_int(0, 8), true),
                'treatment_reason' => $this->faker->words(random_int(0, 3), true)
            ],
            $attributes
        );
    }
}
