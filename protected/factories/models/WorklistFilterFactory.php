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

namespace OE\factories\models;
use OE\factories\ModelFactory;

class WorklistFilterFactory extends ModelFactory
{
    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->words(3, true)
        ];
    }

    public function forSite($site): self
    {
        return $this->state(function ($attributes) use ($site) {
            $attributes['filter'] = $this->addToFilter(
                $attributes['filter'] ?? '',
                [
                    'site' => $site instanceof \Site ? $site->id : $site
                ]
            );

            return $attributes;
        });
    }

    private function addToFilter(string $filter, array $data): string
    {
        if (empty($filter)) {
            return json_encode($data);
        }

        $filter = json_decode($filter);
        $filter = array_merge($filter, $data);

        return json_encode($filter);
    }
}
