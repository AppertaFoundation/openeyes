<?php

class m191114_003526_move_whiteboard_tab_settings_to_system_settings extends CDbMigration
{
    public function safeUp()
    {
        $id = $this->getDbConnection()->createCommand('select id from setting_field_type where name ="Dropdown list"')->queryRow();
        $this->insert('setting_metadata', array(
            'field_type_id' => $id['id'],
            'key' => 'opbooking_whiteboard_display_mode',
            'name' => 'Operation Booking whiteboard display mode',
            'data' => serialize(array(
                'NEW' => 'Always open in new tab/window',
                'CURRENT' => 'Reuse tab/window if already open',
            )),
            'default_value' => 'NEW',
        ));

        $this->insert('setting_metadata', array(
            'field_type_id' => $id['id'],
            'key' => 'opnote_whiteboard_display_mode',
            'name' => 'Operation Note whiteboard display mode',
            'data' => serialize(array(
                'NEW' => 'Always open in new tab/window',
                'CURRENT' => 'Reuse tab/window if already open',
            )),
            'default_value' => 'CURRENT'
        ));

        $this->delete('ophtroperationbooking_whiteboard_settings_data', '`key` = "opnote_whiteboard_display_mode"');
        $this->delete('ophtroperationbooking_whiteboard_settings_data', '`key` = "opbooking_whiteboard_display_mode"');
        $this->delete('ophtroperationbooking_whiteboard_settings', '`key` = "opnote_whiteboard_display_mode"');
        $this->delete('ophtroperationbooking_whiteboard_settings', '`key` = "opbooking_whiteboard_display_mode"');
    }

    public function safeDown()
    {
        $this->delete('setting_metadata', '`key` = "opnote_whiteboard_display_mode"');
        $this->delete('setting_metadata', '`key` = "opbooking_whiteboard_display_mode"');

        $id = $this->getDbConnection()->createCommand('select id from setting_field_type where name ="Dropdown list"')->queryRow();
        $this->insert('ophtroperationbooking_whiteboard_settings', array(
            'field_type_id' => $id['id'],
            'key' => 'opbooking_whiteboard_display_mode',
            'name' => 'Operation Booking whiteboard display mode',
            'data' => serialize(array(
                'NEW' => 'Always open in new tab/window',
                'CURRENT' => 'Reuse tab/window if already open',
            )),
        ));

        $this->insert('ophtroperationbooking_whiteboard_settings', array(
            'field_type_id' => $id['id'],
            'key' => 'opnote_whiteboard_display_mode',
            'name' => 'Operation Note whiteboard display mode',
            'data' => serialize(array(
                'NEW' => 'Always open in new tab/window',
                'CURRENT' => 'Reuse tab/window if already open',
            )),
        ));

        $refresh_setting = new OphTrOperationbooking_Whiteboard_Settings_Data();
        $refresh_setting->key = 'opbooking_whiteboard_display_mode';
        $refresh_setting->value = 'NEW';
        $refresh_setting->save();

        $refresh_setting = new OphTrOperationbooking_Whiteboard_Settings_Data();
        $refresh_setting->key = 'opnote_whiteboard_display_mode';
        $refresh_setting->value = 'CURRENT';
        $refresh_setting->save();
    }
}
