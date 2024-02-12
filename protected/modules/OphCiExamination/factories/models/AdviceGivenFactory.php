<?php
/**
 * (C) Apperta Foundation, 2024
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2024, Apperta Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

namespace OEModule\OphCiExamination\factories\models;

use OE\factories\ModelFactory;
use OE\factories\models\EventFactory;
use OE\factories\models\traits\HasEventTypeElementStates;
use OEModule\OphCiExamination\models\AdviceGiven;
use OEModule\OphCiExamination\models\AdviceLeafletEntry;

class AdviceGivenFactory extends ModelFactory
{
    use HasEventTypeElementStates;

    public function definition(): array
    {
        return [
            'event_id' => EventFactory::forModule('OphCiExamination'),
        ];
    }

    public function configure(): self
    {
        return $this->afterCreating(function (AdviceGiven $instance) {
            foreach ($instance->leaflet_entries as $entry) {
                $entry->element_id = $instance->id;
                $entry->save();
            }
        });
    }

    public function withLeaflets(int|array $count = 1)
    {
        return $this->afterMaking(function (AdviceGiven $instance) use ($count) {
            if (is_array($count)) {
                $instance->leaflet_entries = array_map(
                    fn ($leaflet) => AdviceLeafletEntry::factory()->make(['element_id' => null, 'leaflet_id' => $leaflet->id]),
                    $count
                );
                return;
            }

            $instance->leaflet_entries = AdviceLeafletEntry::factory()
                ->count($count)
                ->make(['element_id' => null]);
        });
    }

    public function withComments()
    {
        return $this->state([
            'comments' => $this->faker->sentence()
        ]);
    }

    protected function mapModelToFormData($model): array
    {
        return [
            'comments' => $model->comments,
            'leaflet_entries' => array_map(
                fn ($entry) => $entry->leaflet_id,
                $model->leaflet_entries
            )
        ];
    }
}
