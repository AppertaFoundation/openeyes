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

namespace OEModule\OphInBiometry\seeders;

use Element_OphInBiometry_Calculation;
use Element_OphInBiometry_Selection;
use OE\factories\models\EventFactory;
use OE\seeders\BaseSeeder;
use OE\seeders\resources\SeededEventResource;
use OphInBiometry_Imported_Events;

class PopulatedTargetRefractionBiometrySeeder extends BaseSeeder
{
    public function __invoke(): array
    {
        $eye_id = $this->getSeederAttribute('eye_id');
        $target_refraction = $this->getSeederAttribute('target_refraction');
        $predicted_refraction = $this->getSeederAttribute('predicted_refraction');

        $patient_id = $this->getSeederAttribute('patient_id');
        $event_attributes = [];

        if (!is_null($patient_id)) {
            $episode = \Episode::factory()
                ->create([
                    'patient_id' => $patient_id,
                    'firm_id' => $this->app_context->getSelectedFirm()->id
                ]);

            $event_attributes = ['episode_id' => $episode->id];
        }

        $event = EventFactory::forModule('OphInBiometry')
            ->withElements([
                \Element_OphInBiometry_Measurement::class,
                OphInBiometry_Imported_Events::class,

            ])
            ->withIolRefValues()
            ->create($event_attributes);

        Element_OphInBiometry_Selection::factory()
            ->forSidedPredictedRefraction($eye_id, $predicted_refraction)
            ->create([
                'event_id' => $event->id, 'eye_id' => $eye_id]);

        Element_OphInBiometry_Calculation::factory()
            ->forEye($eye_id)
            ->forSidedTargetRefraction($eye_id, $target_refraction)
            ->create(['event_id' => $event->id]);


        return [
            'event' => SeededEventResource::from($event)->inSummary()->toArray(),
        ];
    }
}
