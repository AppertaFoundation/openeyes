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
use UserAuthentication;
use FirmUserAssignment;

use WorklistRecentFilter;

class UserFactory extends ModelFactory
{
    /**
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'first_name' => $this->faker->firstName(),
            'last_name' => $this->faker->lastName(),
            'email' => $this->faker->email(),
            // Note, current regex doesn't support faker title due to trailing .
            // this approach suffices for the current testing requirements
            'title' => $this->faker->randomElement(['Mr', 'Mrs', 'Ms', 'Prof', 'Dr']),
            'role' => '', // not sure what this is for so blank string works as default
            'has_selected_firms' => 0
        ];
    }

    /**
     * Define the auth items to be assigned to the user. Some notes on this are provided for reference
     * in the CypressHelper readme
     *
     * @param array $authitems
     * @return self
     */
    public function withAuthItems(array $authitems = []): self
    {
        return $this->afterCreating(function (\User $user) use ($authitems) {
            foreach ($authitems as $authitem) {
                $this->app->authManager->assign($authitem, $user->id);
            }
        });
    }

    /**
     * This state allows for creating Users that can actually login through the application
     *
     * @param \Institution $institution
     * @param string $password
     * @return self
     */
    public function withLocalAuthForInstitution(\Institution $institution, string $password = 'password'): self
    {
        return $this->afterCreating(function (\User $user) use ($password, $institution) {
            UserAuthentication::factory()->create([
                'user_id' => $user->id,
                'institution_authentication_id' => InstitutionAuthentication::factory()->useExisting([
                    'institution_id' => $institution->id,
                    'user_authentication_method' => 'LOCAL'
                ]),
                'password' => $password,
                'password_repeat' => $password
            ]);
        });
    }


    public function withContact($attributes = []): self
    {
        return $this->state(function () use ($attributes) {
            return [
                'contact_id' => \Contact::factory()->create($attributes)
            ];
        });
    }

    public function withDefaultWorklistFilter(): self
    {
        return $this->afterCreating(function (\User $user) {
            WorklistRecentFilter::factory()->forUser($user)->create();
        });
    }

    public function forSpecificFirms($firms): self
    {
        return $this->state([
            'global_firm_rights' => false
        ])->afterCreating(static function (\User $user) use ($firms) {
            foreach ($firms as $firm) {
                FirmUserAssignment::factory()
                    ->forUser($user)
                    ->forFirm($firm)
                    ->create();
            }
        });
    }
}
