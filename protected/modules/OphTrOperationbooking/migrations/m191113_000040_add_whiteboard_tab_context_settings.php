<?php

class m191113_000040_add_whiteboard_tab_context_settings extends CDbMigration
{
    public function safeUp()
    {
        $id = $this->getDbConnection()->createCommand('select id from setting_field_type where name ="Dropdown list"')->queryRow();
        $this->insert('ophtroperationbooking_whiteboard_settings', array(
            'field_type_id' => $id['id'],
            'key' => 'opbooking_whiteboard_display_mode',
            'name' => 'Operation Booking whiteboard display mode',
            'data' => serialize(array(
                'NEW' => 'Always open in new tab/window',
                'CURRENT' => 'Reuse tab/window if already open',
            )),
            'default_value' => 'CURRENT',
        ));

        $this->insert(
            'ophtroperationbooking_whiteboard_settings',
            array(
            'field_type_id' => $id['id'],
            'key' => 'opnote_whiteboard_display_mode',
            'name' => 'Operation Note whiteboard display mode',
            'data' => serialize(array(
                'NEW' => 'Always open in new tab/window',
                'CURRENT' => 'Reuse tab/window if already open',
            )),
            'default_value' => 'CURRENT',
            )
        );

        $refresh_setting = new OphTrOperationbooking_Whiteboard_Settings_Data();
        $refresh_setting->key = 'opbooking_whiteboard_display_mode';
        $refresh_setting->value = 'NEW';
        $refresh_setting->save();

        $refresh_setting = new OphTrOperationbooking_Whiteboard_Settings_Data();
        $refresh_setting->key = 'opnote_whiteboard_display_mode';
        $refresh_setting->value = 'CURRENT';
        $refresh_setting->save();
    }

    public function safeDown()
    {
        $this->delete('ophtroperationbooking_whiteboard_settings_data', '`key` = "opnote_whiteboard_display_mode"');
        $this->delete('ophtroperationbooking_whiteboard_settings_data', '`key` = "opbooking_whiteboard_display_mode"');
        $this->delete('ophtroperationbooking_whiteboard_settings', '`key` = "opnote_whiteboard_display_mode"');
        $this->delete('ophtroperationbooking_whiteboard_settings', '`key` = "opbooking_whiteboard_display_mode"');
    }
}
