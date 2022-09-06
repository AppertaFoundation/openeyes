<?php
/**
 * (C) Copyright Apperta Foundation 2022
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

use Institution;
use InstitutionAuthentication;
use OE\factories\ModelFactory;
use UserAuthentication;
use UserAuthenticationMethod;

class InstitutionFactory extends ModelFactory
{
    /**
     * @return array
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->company(),
            'remote_id' => $this->faker->regexify('\w\w\w\d'),
            'short_name' => $this->faker->word()
        ];
    }

    public function isTenanted($user_authentication_method = null, $use_existing = true)
    {
        return $this->afterCreating(function (Institution $institution) use ($user_authentication_method, $use_existing) {
            if (!$user_authentication_method) {
                $factory = ModelFactory::factoryFor(UserAuthenticationMethod::class);
                if ($use_existing) {
                    $user_authentication_method = $factory->useExisting();
                }
                $user_authentication_method = $factory->create();
            }

            if (!is_array($user_authentication_method)) {
                $user_authentication_method = [$user_authentication_method];
            }

            $institution->authenticationMethods = array_map(
                function ($user_authentication_method) use ($institution) {
                    return ModelFactory::factoryFor(InstitutionAuthentication::class)->create([
                        'institution_id' => $institution->id,
                        'user_authentication_method' => $user_authentication_method->code
                    ]);
                },
                $user_authentication_method
            );
        });
    }

    public function withUserAsMember($user)
    {
        return $this->isTenanted()
            ->afterCreating(function (Institution $institution) use ($user) {
                ModelFactory::factoryFor(UserAuthentication::class)->forUser($user)
                    ->create([
                        'institution_authentication_id' => $institution->authenticationMethods[0]->id
                    ]);
            });
    }
}
