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

namespace OE\seeders\seeders;

use Institution;
use OE\seeders\BaseSeeder;

use Site;
use User;

class UserProfileSeeder extends BaseSeeder
{
    public function __invoke(): array
    {
        $user = $this->getNewUser();
        $inactive_sites = Site::factory()->count(2)->create(['active' => 0, 'institution_id' => $user['institution_id']]);
        $active_sites = Site::model()->findAllByAttributes(['institution_id' => $user['institution_id'], 'active' => 1]);

        return [
            'user' => $this->getNewUser(),
            'inactive_sites' => $inactive_sites,
            'active_sites' => $active_sites
        ];
    }

    private function getNewUser()
    {
        $authitems = ["User", "View clinical"];
        $institution_id = Institution::model()->findByAttributes(['remote_id' => 'NEW'])->id ?? 1;
        $password = 'password';

        $user = User::factory()
            ->withAuthItems($authitems)
            ->withLocalAuthForInstitution(Institution::model()->findByPk($institution_id), $password)
            ->create();

        return [
            'user_id' => $user->id,
            'username' => $user->authentications[0]->username,
            'password' => $password,
            'institution_id' => $institution_id
        ];
    }
}
