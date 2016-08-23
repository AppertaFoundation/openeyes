<?php

class m160819_094559_optom_comment_alert extends CDbMigration
{
	public function up()
	{
		$this->insert('setting_metadata', array(
			'element_type_id' => null,
			'field_type_id' => SettingFieldType::model()->find('name = "Text Field"')->id,
			'key' => 'optom_comment_alert',
			'name' => 'Address For Optom Comment Alerts',
			'default_value' => '',
		));
	}

	public function down()
	{
		echo "m160819_094559_optom_comment_alert does not support migration down.\n";
		return false;
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