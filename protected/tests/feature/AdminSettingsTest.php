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

use OE\factories\ModelFactory;

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

    protected $admin_user;
    protected $setting_institution;

    public function setUp(): void
    {
        parent::setUp();

        list($user, $institution) = $this->createUserWithInstitution();

        $this->admin_user = $user;
        $this->setting_institution = $institution;
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

    protected function getInstallationSettingPage(string $setting_key)
    {
        $url = '/admin/editSystemSetting?' . http_build_query(['key' => $setting_key, 'class' => 'SettingInstallation']);

        return $this->actingAs($this->admin_user, $this->setting_institution)
                    ->get($url);
    }

    protected function createUserWithInstitution()
    {
        $user = User::model()->findByAttributes(['first_name' => 'admin']);

        $institution = ModelFactory::factoryFor(Institution::class)
            ->withUserAsMember($user)
            ->create();

        return [$user, $institution];
    }
}
