<?php

class m230503_062323_add_auto_save_enable_system_setting extends OEMigration
{
	private const SETTING_NAME = "auto_save_enabled";

	public function up()
	{
		$field_type_id = $this->getDbConnection()->createCommand('select id from setting_field_type where name ="Radio buttons"')->queryScalar();
		$setting_group_id = $this->getDbConnection()->createCommand('select id from setting_group where name ="System"')->queryScalar();
		$this->insert('setting_metadata', array(
			'element_type_id' => null,
			'field_type_id' => $field_type_id,
			'group_id' => $setting_group_id,
			'description' => 'Enables auto-save functionality in events',
			'key' => self::SETTING_NAME,
			'name' => 'Enable event auto-save',
            'data' => 'a:2:{s:2:"on";s:2:"On";s:3:"off";s:3:"Off";}',
            'default_value' => 'on'
        ));
    }

	public function down()
	{
		$this->delete('setting_metadata', '`key` = "' . self::SETTING_NAME . '"');
	}
}
