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
use OE\seeders\resources\SeededPatientResource;
use OELog;
use OEModule\OphCiExamination\components\ExaminationHelper;
use OEModule\OphCiExamination\models\{
    OphCiExamination_ElementSet,
    OphCiExamination_Workflow
};

class LoadAllElementsSeeder extends BaseSeeder
{
    public function __invoke(): array
    {
        $examination_event_type_id = \EventType::model()->findByAttributes(['name' => 'Examination'])->id;
        $examination_element_types = \ElementType::model()->findAllByAttributes(['event_type_id' => $examination_event_type_id]);

        $filter_list = ExaminationHelper::elementFilterList();

        $filtered_examination_element_types = array_filter($examination_element_types, function ($element_type) use ($filter_list) {
            return !in_array($element_type->class_name, $filter_list);
        });

        $all_element_names = array_map(
            function ($element_type) {
                return $element_type->name;
            },
            $filtered_examination_element_types
        );

        $patient = \Patient::factory()->create();

        return [
            'elements' => array_values($all_element_names),
            'patient' => SeededPatientResource::from($patient)->toArray(),
        ];
    }
}
