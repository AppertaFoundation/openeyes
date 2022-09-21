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
use OE\factories\models\traits\HasEventTypeElementStates;
use OE\factories\models\EventFactory;
use OEModule\OphCiExamination\models\SystemicDiagnoses;
use Eye;
use Disorder;
use OEModule\OphCiExamination\models\SystemicDiagnoses_Diagnosis;

class SystemicDiagnosesFactory extends ModelFactory
{
    use HasEventTypeElementStates;

    public function definition(): array
    {
        return [
            'event_id' => EventFactory::forModule('OphCiExamination'),
        ];
    }

    /**
     * @param mixed $disorders - array of disorders, or integer to represent the number of diagnoses to assign
     * @return void
     */
    public function withDiagnoses($disorders = []): self
    {
        return $this->afterCreating(function (SystemicDiagnoses $element) use ($disorders) {
            if (is_int($disorders)) {
                $disorders = Disorder::factory()->count($disorders)->create();
            }
            foreach ($disorders as $disorder) {
                SystemicDiagnoses_Diagnosis::factory()->create([
                    'element_id' => $element->id,
                    'disorder_id' => $disorder->id
                ]);
            }
        });
    }
}
