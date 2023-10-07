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

use InstitutionAuthentication;
use OE\factories\ModelFactory;
use \User;

class UserAuthenticationFactory extends ModelFactory
{
    /**
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'institution_authentication_id' => InstitutionAuthentication::factory(),
            'user_id' => User::factory(),
            'username' => $this->faker->userName(),
            // because we validate when saving with this factory, we must provide password
            'password' => 'password',
            'password_repeat' => 'password'
        ];
    }

    public function configure()
    {
        return $this->withDBUniqueAttribute('username');
    }

    public function forUser($user)
    {
        return $this->state(function ($attributes) use ($user) {
            return [
                'user_id' => $user->id,
            ];
        });
    }

    /**
     * The UserAuthentication model manipulates password values during validation
     * to ensure it is stored correctly, as a result, we must call save with
     * validation enabled, which is not the typical behaviour for ModelFactories.
     *
     * We also disable password restrictions to allow simplified password values to
     * be used in model generation.
     *
     * @param [type] $instance
     * @return boolean
     */
    public function persistInstance($instance): bool
    {
        return self::disablePasswordRestrictionsFor(
            function ($instance) {
                return $instance->save(true);
            },
            [$instance],
            $this->app
        );
    }

    /**
     * Wraps the given function to disable the password restrictions
     * that are used for validation in the UserAuthentication model
     *
     * @param callable $callback
     * @param array $args
     * @param [type] $app
     * @return mixed
     */
    public static function disablePasswordRestrictionsFor(callable $callback, $args = [], $app = null)
    {
        if ($app === null) {
            $app = \Yii::app();
        }

        $default_settings = $app->params['pw_restrictions'];

        $app->setParams([
            'pw_restrictions' => [
                'strength_regex' => '%\w*%'
            ]
        ]);

        $result = call_user_func_array($callback, $args);

        $app->setParams([
            'pw_restrictions' => $default_settings
        ]);

        return $result;
    }
}
