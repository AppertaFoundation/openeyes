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

namespace OE\seeders\seeders;

use OE\seeders\BaseSeeder;

use \SettingMetadata;

/**
* CreateMessageSeeder is a seeder for creating system settings for testing in the admin system settings screen and child screens.
* Each setting created corresponds to a different SettingFieldType (e.g. check boxes, dropdown lists, radio buttons)
*/
class CreateAdminSettingsSeeder extends BaseSeeder
{
    /**
    * Returns the data required to access the created settings and check their properties
    * Return data is:
    * - checkbox - array with elements key, name, started_checked
    * @return array
    */
    public function __invoke(): array
    {
        $checkbox_checked = $this->getApp()->dataGenerator->faker()->boolean();

        $checkbox_setting = SettingMetadata::factory()
                          ->forCheckbox($checkbox_checked)
                          ->create();

        return [
            'checkbox' => [
                'key' => $checkbox_setting->key,
                'name' => $checkbox_setting->name,
                'startedChecked' => $checkbox_checked,
                'startedValue' => $checkbox_checked ? 'On' : 'Off',
                'toggledValue' => $checkbox_checked ? 'Off' : 'On',
            ]
        ];
    }
}
