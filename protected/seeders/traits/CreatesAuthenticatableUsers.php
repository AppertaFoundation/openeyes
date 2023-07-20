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

namespace OE\seeders\traits;

use Institution;
use User;

trait CreatesAuthenticatableUsers
{
    /**
     * @param Institution $institution
     * @param string $password
     * @param array|string $auth_items
     * @return array[User $user, string $username, string $password]
     */
    public function createAuthenticatableUser(
        Institution $institution,
        string $password = 'password',
        $auth_items = ['Edit', 'User', 'View clinical']
    ): array
    {
        $user = User::factory()
            ->withLocalAuthForInstitution($institution, $password)
            ->withAuthItems((array) $auth_items)
            ->create();

        return [$user, $user->authentications[0]->username, $password];
    }
}