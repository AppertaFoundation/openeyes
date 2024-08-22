<?php

/**
 * (C) OpenEyes Foundation, 2023
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2023, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

/**
 * @group sample-data
 * @group feature
 * @group settings
 */
class AdminSettingsTest extends OEDbTestCase
{
    use MakesApplicationRequests;
    use WithTransactions;
    use WithFaker;

    protected $installation_admin_user;
    protected $institution_admin_user;
    protected $setting_institution;

    public function setUp(): void
    {
        parent::setUp();

        $this->installation_admin_user = User::factory()
                                       ->withUniquePostfix(microtime())
                                       ->withDBUniqueAttribute('first_name')
                                       ->withAuthItems(['User', 'admin'])
                                       ->create();

        $this->institution_admin_user = User::factory()
                                      ->withUniquePostfix(microtime())
                                      ->withDBUniqueAttribute('first_name')
                                      ->withAuthItems(['User', 'Institution Admin'])
                                      ->create();

        $this->setting_institution = Institution::factory()
                                   ->withUserAsMember($this->installation_admin_user)
                                   ->withUserAsMember($this->institution_admin_user)
                                   ->create();
    }

    /** @test */
    public function system_settings_can_utilise_checkboxes()
    {
        $setting_key = $this->faker->word();
        $setting_checked = $this->faker->boolean();

        $setting = SettingMetadata::factory()
                 ->forKey($setting_key)
                 ->forCheckbox($setting_checked)
                 ->create();

        $response = $this->getInstallationSettingPage($setting_key);

        $checkbox_element = $response->filter('[data-test="setting-checkbox"]');

        // An existance check
        $this->assertEquals($checkbox_element->count(), 1);

        $checkbox_name = $checkbox_element->extract(['name'])[0];
        $checkbox_checked = $checkbox_element->extract(['checked'])[0];

        $this->assertEquals($checkbox_name, $setting_key);
        $this->assertEquals($checkbox_checked, $setting_checked ? 'checked' : '');
    }

    /** @test */
    public function installation_admin_can_search_users()
    {
        $user_to_find = User::factory()->withLocalAuthForInstitution($this->setting_institution)
                                       ->withUniquePostfix(microtime())
                                       ->withDBUniqueAttribute('first_name')
                                       ->create();

        $other_user = User::factory()->withUniquePostfix(microtime())
                                     ->withDBUniqueAttribute('first_name')
                                     ->create();

        $other_institution = Institution::factory()
                           ->withUserAsMember($this->installation_admin_user)
                           ->withUserAsMember($other_user)
                           ->create();

        $response = $this->getUsersPage($this->installation_admin_user, $user_to_find->first_name);

        $this->assertEquals(
            $user_to_find->id,
            $response->filter('[data-test="user-id"]')->text(),
            'The user in the same institution as the institution admin should show up in the search results'
        );

        $response = $this->getUsersPage($this->installation_admin_user, $this->institution_admin_user->first_name);

        $this->assertEquals(
            $this->institution_admin_user->id,
            $response->filter('[data-test="user-id"]')->text(),
            'The institution admin user should show up in the search results for their own institution'
        );

        $response = $this->getUsersPage($this->installation_admin_user, $this->installation_admin_user->first_name);

        $this->assertEquals(
            $this->installation_admin_user->id,
            $response->filter('[data-test="user-id"]')->text(),
            'Installation level admins should show up in the search results'
        );

        $response = $this->getUsersPage($this->installation_admin_user, $other_user->first_name);

        $this->assertEquals(
            $other_user->id,
            $response->filter('[data-test="user-id"]')->text(),
            'Users from other institutions should show up in the search results'
        );
    }

    /** @test */
    public function institution_admin_can_search_users()
    {
        $user_to_find = User::factory()->withLocalAuthForInstitution($this->setting_institution)->create();

        $other_user = User::factory()->create();

        $other_institution = Institution::factory()
                           ->withUserAsMember($this->installation_admin_user)
                           ->withUserAsMember($other_user)
                           ->create();

        $response = $this->getUsersPage($this->institution_admin_user, $user_to_find->first_name);

        $this->assertEquals(
            $user_to_find->id,
            $response->filter('[data-test="user-id"]')->text(),
            'The user in the same institution as the institution admin should show up in the search results'
        );

        $response = $this->getUsersPage($this->institution_admin_user, $this->institution_admin_user->first_name);

        $this->assertEquals(
            $this->institution_admin_user->id,
            $response->filter('[data-test="user-id"]')->text(),
            'The institution admin user should show up in the search results for their own institution'
        );

        $response = $this->getUsersPage($this->institution_admin_user, $this->installation_admin_user->first_name);

        $this->assertEquals(
            0,
            $response->filter('[data-test="user-id"]')->count(),
            'Installation level admins should not show up in the search results'
        );

        $response = $this->getUsersPage($this->institution_admin_user, $other_user->first_name);

        $this->assertEquals(
            0,
            $response->filter('[data-test="user-id"]')->count(),
            'Users from other institutions should not show up in the search results'
        );
    }

    protected function getInstallationSettingPage(string $setting_key)
    {
        $url = '/admin/editSystemSetting?' . http_build_query(['key' => $setting_key, 'class' => 'SettingInstallation']);

        return $this->actingAs($this->installation_admin_user, $this->setting_institution)
                    ->get($url)
                    ->assertSuccessful()
                    ->crawl();
    }

    protected function getUsersPage($as, ?string $search_parameter = null)
    {
        $url = '/admin/users';

        if (is_string($search_parameter)) {
            $url .= '?' . http_build_query(['search' => $search_parameter]);
        }

        return $this->actingAs($as, $this->setting_institution)->get($url)->crawl();
    }
}
