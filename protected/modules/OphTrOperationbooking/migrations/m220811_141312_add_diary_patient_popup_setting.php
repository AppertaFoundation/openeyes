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

class m220811_141312_add_diary_patient_popup_setting extends OEMigration
{
    protected $setting_key = 'ophtroperation_booking_theatre_diary_show_patient_popup';
    public function safeUp()
    {
        $radio_type = $this->dbConnection->createCommand("SELECT id FROM setting_field_type WHERE `name` LIKE 'Radio buttons' LIMIT 1;")->queryScalar();
        $this->insert('setting_metadata', [
            'element_type_id' => null,
            'field_type_id' => $radio_type,
            'key' => $this->setting_key,
            'name' => 'Show patient summary popup in theatre diaries',
            'data' => 'a:2:{s:2:"on";s:2:"On";s:3:"off";s:3:"Off";}',
            'default_value' => 'on'
        ]);
    }

    public function safeDown()
    {
        $this->delete('setting_metadata', '`key` = ?', [$this->setting_key]);
        $this->delete('setting_installation', '`key` = ?', [$this->setting_key]);
    }
}
