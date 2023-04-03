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

namespace OE\factories\models;

use OE\factories\ModelFactory;

/**
 * The definition for this has not been expanded, as it's assumed it will always be used with the "forExisting"
 * behaviour to retrieve an Team from the database.
 */
class TeamFactory extends ModelFactory
{
    /**
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->words(3, true),
            'email' => $this->faker->email(),
            'active' => 1,
            'institution_id' => 1
        ];
    }

    public function withUsers($users): self
    {
        return $this->afterMaking(function (\Team $team) use ($users) {
            $ids = array_map(
                static function ($user) { return $user->id; },
                $users
            );

            $team->setAndCacheAssignedUsers($ids);
        });
    }

    public function withTasks($tasks): self
    {
        return $this->afterCreating(function (\Team $team) use ($tasks) {
            $team->setUserTasks($tasks);
        });
    }

    public function withTeams($teams): self
    {
        return $this->afterMaking(function (\Team $team) use ($teams) {
            $ids = array_map(
                static function ($team) { return $team->id; },
                $teams
            );

            $team->setAndCacheAssignedTeams($ids);
        });
    }
}
