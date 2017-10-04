<?php

class m170831_100900_add_system_setting_disable_correspondence_notes_copy extends CDbMigration
{
    public function up()
    {
        $this->insert('setting_metadata', array(
            'display_order' => 0,
            'field_type_id' => 3,
            'key' => 'disable_correspondence_notes_copy',
            'name' => 'Disable copy for notes in correspondence',
            'data' => serialize(array('on'=>'On', 'off'=>'Off')),
            'default_value' => 'on'
        ));

        $this->insert('setting_installation', array(
            'key' => 'disable_correspondence_notes_copy',
            'value' => 'on'
        ));
    }

    public function down()
    {
        $this->delete('setting_installation', '`key`="disable_correspondence_notes_copy"');
        $this->delete('setting_metadata', '`key`="disable_correspondence_notes_copy"');
    }
}
