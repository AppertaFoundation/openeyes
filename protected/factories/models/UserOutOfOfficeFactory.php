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
use User;

class UserOutOfOfficeFactory extends ModelFactory
{
    /**
     * @return array
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'enabled' => false
        ];
    }

    /**
     * withUser
     *
     * Supply a specific user instead of having a factory generated one
     *
     * @param $user User|UserFactory|int|string|null
     * @return UserOutOfOfficeFactory
     */
    public function withUser($user): self
    {
        return $this->state([
            'user_id' => $user->id
        ]);
    }

    /**
     * enabled
     *
     * @return UserOutOfOfficeFactory
     */
    public function enabled(): self
    {
        return $this->state([
            'enabled' => true
        ]);
    }

    /**
     * notEnabled
     *
     * @return UserOutOfOfficeFactory
     */
    public function notEnabled(): self
    {
        return $this->state([
            'enabled' => false
        ]);
    }

    /**
     * withDates
     *
     * Set the from and to dates and optionally a supplied alternate user.
     * If no alternate user is supplied, one will be generated as it is required.
     *
     * @param $from
     * @param $to
     * @param $alternate_user User|UserFactory|int|string|null
     * @return UserOutOfOfficeFactory
     */
    public function withDates($from, $to, $alternate_user = null): self
    {
        $alternate_user = $alternate_user ?? User::factory();

        return $this->state([
            'from_date' => $from,
            'to_date' => $to,
            'alternate_user_id' => $alternate_user,
        ]);
    }
}
