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
	}

	public function down()
	{
		$this->delete('setting_metadata', array('key' => 'disable_theatre_diary'));
	}
}