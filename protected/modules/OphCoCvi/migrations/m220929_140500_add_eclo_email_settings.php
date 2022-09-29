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

class m220929_140500_add_eclo_email_settings extends OEMigration
{
    private $settings = array(
        array(
            'key' => 'cvi_eclo_sender_email',
            'name' => 'CVI sender of ECLO email',
            'value' => 'noreply@openeyes.org.uk',
        ),
        array(
            'key' => 'cvi_eclo_sender_email',
            'name' => 'CVI sender of ECLO email',
            'value' => '',
        ),
    );

    public function up()
    {
        foreach ($this->settings as $setting) {
            $this->insert('setting_metadata', array(
                'key' => $setting['key'],
                'name' => $setting['name'],
                'element_type_id' => null,
                'default_value' => '',
                'field_type_id' => $this->getSettingFieldIdByName('Text Field'),
            ));
            $this->insert('setting_installation', array(
                'key' => $setting['key'],
                'value' => $setting['value'],
            ));
        };
    }

    public function down()
    {
        foreach ($this->settings as $setting) {
            $this->delete('setting_installation', '`key` = ?', array($setting['key']));    
            $this->delete('setting_metadata', '`key` = ?', array($setting['key']));    
        }
    }
}
