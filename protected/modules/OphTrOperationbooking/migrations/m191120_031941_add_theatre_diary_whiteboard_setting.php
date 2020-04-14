<?php

class m191120_031941_add_theatre_diary_whiteboard_setting extends OEMigration
{
    // Use safeUp/safeDown to do migration with transaction
    public function safeUp()
    {
        $id = $this->getDbConnection()->createCommand('select id from setting_field_type where name ="Dropdown list"')->queryScalar();
        $this->insert('setting_metadata', array(
            'field_type_id' => $id,
            'key' => 'theatre_diary_whiteboard_display_mode',
            'name' => 'Theatre diary whiteboard display mode',
            'data' => serialize(array(
                'NEW' => 'Always open in new tab/window',
                'CURRENT' => 'Reuse tab/window if already open',
            )),
            'default_value' => 'NEW',
        ));
    }

    public function safeDown()
    {
        $this->delete('setting_metadata', '`key` = "theatre_diary_whiteboard_display_mode"');
    }
}
