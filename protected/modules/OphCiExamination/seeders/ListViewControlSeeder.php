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


namespace OEModule\OphCiExamination\seeders;

use OE\seeders\BaseSeeder;
use OE\factories\models\EventFactory;
use OE\seeders\resources\SeededEventResource;

use Eye;

use OEModule\OphCiExamination\models\{
    Element_OphCiExamination_IntraocularPressure,
    OphCiExamination_IntraocularPressure_Value,
    Allergies
};

class ListViewControlSeeder extends BaseSeeder
{
    public function __invoke(): array
    {
        $existing_event = EventFactory::forModule('OphCiExamination')->create();

        $iop_element = Element_OphCiExamination_IntraocularPressure::factory()->forEvent($existing_event)->create();
        $left_iop_value = OphCiExamination_IntraocularPressure_Value::factory()->forElement($iop_element)->forEye(Eye::LEFT)->create();
        $right_iop_value = OphCiExamination_IntraocularPressure_Value::factory()->forElement($iop_element)->forEye(Eye::RIGHT)->create();

        $allergies_element = Allergies::factory()->withEntries()->create(['event_id' => $existing_event]);

        return [
            'existingEvent' => SeededEventResource::from($existing_event)->toArray(),
            'historyIOP' => [
                'left' => $left_iop_value->reading->name,
                'right' => $right_iop_value->reading->name,
            ]
        ];
    }
}
