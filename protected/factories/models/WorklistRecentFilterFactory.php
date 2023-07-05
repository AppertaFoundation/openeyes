<?php
/**
 * OpenEyes.
 *
 * (C) OpenEyes Foundation, 2023
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2011-2012, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

namespace OE\factories\models;

use OE\factories\ModelFactory;

use User;
use WorklistFilterQuery;

class WorklistRecentFilterFactory extends ModelFactory
{
    public function definition(): array
    {
        $default_query = new WorklistFilterQuery();

        return [
            'created_user_id' => User::factory(),
            'filter' => $default_query->getJSONRepresentation()
        ];
    }

    /**
     * @param User|UserFactory|string|int|null $user
     * @return WorklistRecentFilterFactory
     */
    public function forUser($user = null): self
    {
        $user ??= User::factory();

        return $this->state([
            'created_user_id' => $user
        ]);
    }

    /**
     * Override persistInstance to call save with its third parameter, $allow_overriding, set to true.
     * Setting that parameter to true ensures that the value of last_modified_user_id and/or created_user_id
     * that is provided is maintained.
     *
     * @param mixed $instance
     * @return bool
     */
    protected function persistInstance($instance): bool
    {
        return $instance->save(false, null, true);
    }
}
