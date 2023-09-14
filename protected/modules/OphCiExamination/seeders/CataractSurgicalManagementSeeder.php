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

use OE\factories\models\EventFactory;
use OE\seeders\BaseSeeder;
use OE\seeders\resources\SeededEventResource;
use OEModule\OphCiExamination\models\Element_OphCiExamination_CataractSurgicalManagement;


class CataractSurgicalManagementSeeder extends BaseSeeder
{

    public function __invoke(): array
    {
        $event_attributes = [];
        $event_date = $this->getSeederAttribute('event_date');

        if (!is_null($event_date)) {
            $event_attributes['event_date'] = $event_date;
        }

        $patient_id = $this->getSeederAttribute('patient_id');

        if (!is_null($patient_id)) {
            $episode = \Episode::factory()
                ->create([
                    'patient_id' => $this->getSeederAttribute('patient_id'),
                    'firm_id' => $this->app_context->getSelectedFirm()->id
                ]);

            $event_attributes['episode_id'] = $episode->id;
        }

        $event = EventFactory::forModule('OphCiExamination')->create($event_attributes);

        $eye_id = $this->getSeederAttribute('eye_id');
        $target_refraction = $this->getSeederAttribute('target_refraction');
        $element = Element_OphCiExamination_CataractSurgicalManagement::factory()
            ->forEvent($event)
            ->forEye($eye_id)
            ->forSidedTargetRefraction($eye_id, $target_refraction)
            ->create();

        return [
            'event' => SeededEventResource::from($event)->toArray()
        ];
    }
}
