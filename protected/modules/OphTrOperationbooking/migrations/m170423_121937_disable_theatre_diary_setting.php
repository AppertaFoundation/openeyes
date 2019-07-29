<?php

class m170423_121937_disable_theatre_diary_setting extends CDbMigration
{
    public function up()
    {
        $this->insert('setting_metadata', array(
                        'field_type_id' => 3,
                        'key' => 'disable_theatre_diary',
                        'name' => 'Disable Theatre Diary',
                        'default_value' => 'off',
                        'data' => serialize(array('on'=>'On', 'off'=>'Off'))
        ));

        $this->insert('setting_installation', array(
                        'key' => 'disable_theatre_diary',
                        'value' => 'off',
        ));
    }

    public function down()
    {
        $this->delete('setting_metadata', array('key' => 'disable_theatre_diary'));
        $this->delete('setting_installation', array('key' => 'disable_theatre_diary'));
    }
}
