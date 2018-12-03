<?php

class m181128_061013_user_add_diagnosis extends CDbMigration
{
	public function safeUp()
	{
        $this->insert('setting_metadata', array(
            'element_type_id' => null,
            'display_order' => 0,
            'field_type_id' => 3, // Radio Buttons
            'key' => 'user_add_diagnosis',
            'name' => 'Allow all Users to Add/Edit Diagnosis',
            'data' => serialize(array('on' => 'On', 'off' => 'Off')),
            'default_value' => 'off',
        ));
	}

	public function safeDown()
	{
        $this->delete('setting_metadata', '`key` = \'user_add_diagnosis\'');
	}

	/*
	// Use safeUp/safeDown to do migration with transaction
	public function safeUp()
	{
	}

	public function safeDown()
	{
	}
	*/
}