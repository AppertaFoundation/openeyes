<?php

class m190502_151159_add_system_setting_for_include_subspecialty_name_in_unbooked_worklists extends CDbMigration
{
	public function safeUp()
	{
    $this->insert('setting_metadata', [
      'field_type_id' => SettingFieldType::model()->find('name = ?' , ["Radio buttons"])->id,
      'key' => 'include_subspecialty_name_in_unbooked_worklists',
      'name' => 'Include subspecialty name in unbooked worklists',
      'data' => serialize(['1' => 'Yes', '0' => 'No']),
      'default_value' => 1,
    ]);
    $this->insert('setting_installation', [
      'key' => 'include_subspecialty_name_in_unbooked_worklists',
      'value' => 1,
    ]);
	}

	public function safeDown()
	{
    $this->delete('setting_installation', '`key`="include_subspecialty_name_in_unbooked_worklists"');
    $this->delete('setting_metadata', '`key`="include_subspecialty_name_in_unbooked_worklists"');
	}
}