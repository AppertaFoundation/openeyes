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

use Eye;

class Element_OphCiExamination_IntraocularPressureFactory extends ModelFactory
{
    public function definition(): array
    {
        return [
            'event_id' => EventFactory::forModule('OphCiExamination'),
            'eye_id' => Eye::factory()->useExisting(['id' => Eye::BOTH]),
            'left_comments' => '',
            'right_comments' => ''
        ];
    }

    /**
     * @param Event|EventFactory|string|int $event
     * @return Element_OphCiExamination_IntraocularPressureFactory
     */
    public function forEvent($event): self
    {
        return $this->state([
            'event_id' => $event
        ]);
    }
}
