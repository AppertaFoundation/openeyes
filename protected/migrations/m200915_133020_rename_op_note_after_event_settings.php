<?php
/**
 * (C) OpenEyes Foundation, 2020
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2020, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

class m200915_133020_rename_op_note_after_event_settings extends CDbMigration
{
    private $event_type_name = 'ophtroperationnote';

    private $keys_to_rename = [
        'auto_generate_gp_letter_after_surgery' => 'auto_generate_gp_letter_after_',
        'auto_generate_optom_post_op_letter_after_surgery' => 'auto_generate_optom_letter_after_',
        'auto_generate_prescription_after_surgery' => 'auto_generate_prescription_after_',

        'default_optom_post_op_letter' => 'default_optom_letter_',
        'default_post_op_drug_set' => 'default_drug_set_',
        'default_post_op_letter' => 'default_letter_',
    ];

    private $tables = [
        'setting_metadata',
        'setting_user',
        'setting_firm',
        'setting_subspecialty',
        'setting_specialty',
        'setting_site',
        'setting_institution',
        'setting_installation',
    ];

    public function safeUp()
    {
        foreach ($this->keys_to_rename as $old_key => $_key) {
            $new_key = $_key . $this->event_type_name;

            foreach ($this->tables as $table) {
                $this->update($table, ['key' => $new_key], '`key` = :s_key', [':s_key' => $old_key]);
            }
        }
    }

    public function safeDown()
    {
        foreach ($this->keys_to_rename as $old_key => $_key) {
            $new_key = $_key . $this->event_type_name;

            foreach ($this->tables as $table) {
                $this->update($table, ['key' => $old_key], '`key` = :s_key', [':s_key' => $new_key]);
            }
        }
    }
}
