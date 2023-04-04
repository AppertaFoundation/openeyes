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

namespace OEModule\Admin\seeders;

use OE\seeders\BaseSeeder;

/**
 * AddTeamSeeder is a seeder for generating data used solely in the Add Team test (admin\teams.cy.js)
 */
class AddTeamSeeder extends BaseSeeder
{
    /**
     * Returns the data required for adding a team.
     * Return data includes:
     * - teamName - the name of the team to be created
     * - email - email address of said team
     * - active - active status of said team
     * - userAssignments - 2 x users (to be subsequently assigned to said team)
     * - teamAssignments - 2 x teams (to be subsequently assigned to said team)
     * @return array
     */
    public function __invoke(): array
    {
        $users = [];
        $team_names = [];

        $admin_user = \User::model()->findByPk(1);
        $users[] = ['fullName' => $admin_user->getFullNameAndTitle(), 'role' => 'Owner'];

        $user = \User::factory()->useExisting()->create();
        $users[] = ['fullName' => $user->getFullNameAndTitle(), 'role' => 'Member'];

        for ($i = 0; $i < 2; $i++) {
            // team must have a user assigned or it will be set to 'inactive' automatically
            $team_user = \User::factory()->useExisting()->create();
            $team_names[] = \Team::factory()->withUsers([$team_user])->create()->name;
        }

        return [
            'teamName' => 'Test Team ' . $this->getApp()->dataGenerator->faker()->word(),
            'email' => $this->getApp()->dataGenerator->faker()->email(),
            'active' => true,
            'userAssignments' => $users,
            'teamAssignments' => $team_names,
        ];
    }
}
