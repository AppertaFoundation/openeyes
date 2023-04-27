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

namespace OEModule\OphTrOperationbooking\seeders;

use OE\factories\models\EventFactory;
use OE\seeders\BaseSeeder;
use OE\seeders\resources\SeededEventResource;

class OpBookingWithProceduresSeeder extends BaseSeeder
{
    public function __invoke(): array
    {
        $episode = \Episode::factory()
            ->create([
                'patient_id' => $this->getSeederAttribute('patient_id'),
                'firm_id' => $this->app_context->getSelectedFirm()->id
            ]);

        $procedures = array_map(function (string $procedure_name) {
            return \Procedure::factory()->useExisting(['term' => $procedure_name])->create();
        }, $this->getSeederAttribute('procedure_names', []));

        $event = EventFactory::forModule('OphTrOperationbooking')
            ->bookedWithStates([
                'withSingleEye',
                'withRequiresScheduling'
            ]);

        $event = $event->create(['episode_id' => $episode->id]);

        $operation_element = \Element_OphTrOperationbooking_Operation::model()
            ->findbyAttributes(['event_id' => $event->id]);

        foreach ($procedures as $procedure) {
            \OphTrOperationbooking_Operation_Procedures::factory()->create([
                'element_id' => $operation_element->id,
                'proc_id' => $procedure->id
            ]);
        }

        return [
            'event' =>  SeededEventResource::from($event)->toArray(),
        ];
    }
}
