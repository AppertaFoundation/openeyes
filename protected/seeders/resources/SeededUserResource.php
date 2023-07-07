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

namespace OE\seeders\resources;

use \User;

class SeededUserResource extends SeededResource
{
    private string $_password = 'password';

    public static function from(User $user): self
    {
        return new SeededUserResource($user);
    }

    public function setPassword(string $password)
    {
        $this->_password = $password;
    }

    public function toArray(): array
    {
        $auth = $this->instance->authentications[0];

        return [
            'id' => $this->instance->id,
            'username' => $auth->username,
            'password' => $this->_password,
        ];
    }
}
