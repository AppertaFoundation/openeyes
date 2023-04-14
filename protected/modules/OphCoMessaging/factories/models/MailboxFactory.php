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

namespace OEModule\OphCoMessaging\factories\models;

use Faker\Generator;
use OE\factories\ModelFactory;
use OEModule\OphCoMessaging\models\Mailbox;

class MailboxFactory extends ModelFactory
{
    /**
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->word(),
            'is_personal' => false,
        ];
    }

    public function personalFor($user): self
    {
        return $this->state([
            'is_personal' => true,
            'users' => [$user]
        ]);
    }

    public function isPersonal(): self
    {
        return $this->state([
            'is_personal' => true
        ]);
    }

    /**
     * Take an array of User or OEWebUser objects to store as the associated users for the mailbox.
     * The Mailbox model has auto_update_relations so create will save them as related MailboxUser models.
     *
     * @param array $users
     * @return MailboxFactory
     */
    public function withUsers($users): self
    {
        return $this->state([
            'users' => $users
        ]);
    }

    /**
     * Take an array of Team objects to store as the associated teams for the mailbox.
     * The Mailbox model has auto_update_relations so create will save them as related MailboxUser models.
     *
     * @param array $teams
     * @return MailboxFactory
     */
    public function withTeams($teams): self
    {
        return $this->state([
            'teams' => $teams
        ]);
    }

    /**
     * Ensure that the mailbox has a unique name.
     * This is necessary to avoid flakiness in tests that ensure that mailboxes are visible only to certain users,
     * as duplicate names result in a false negative.
     *
     * @param string $postfix
     * @return MailboxFactory
     */
    public function withUniqueMailboxName($prefix = '')
    {
        return $this->withDBUniqueAttribute('name', ['prefix' => $prefix]);
    }

    /**
     * Generates a mailbox name that doesn't already exist in the database.
     *
     * @param Generator $faker
     * @param string $postfix
     * @return string
     */
    public static function getUniqueMailboxName($prefix = '', $is_personal = false)
    {
        return self::generateDBUniqueAttribute('name', '', ['prefix' => $prefix, 'is_personal' => $is_personal]);
    }

    /**
     * Provide specific generator for the mailbox name generator
     *
     * @param string $attribute
     * @param array $params
     */
    public function uniqueAttributeGenerator(string $attribute, array $params = [])
    {
        if ($attribute !== 'name') {
            return parent::uniqueAttributeGenerator($attribute, $params);
        }

        return function () use ($params) {
            $max_length = isset($params['is_personal']) && $params['is_personal'] ? Mailbox::MAX_NAME_PERSONAL : Mailbox::MAX_NAME_NOT_PERSONAL;

            return substr(($params['prefix'] ?? '') . $this->faker->unique()->words(2, true) . ($params['postfix'] ?? ''), 0, $max_length);
        };
    }
}
