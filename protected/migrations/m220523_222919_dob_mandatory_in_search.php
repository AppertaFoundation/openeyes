<?php

class m220523_222919_dob_mandatory_in_search extends OEMigration
{
	private const KEY = 'dob_mandatory_in_search';

	public function safeUp()
	{
        $field_type_id = $this->dbConnection
					   ->createCommand()
					   ->select('id')
					   ->from('setting_field_type')
					   ->where(
						   'name = :name',
						   array(':name' => 'Radio buttons'),
					   )->queryScalar();

		$this->insert('setting_metadata', [
			'key' => self::KEY,
			'name' => 'DOB mandatory in search',
			'data' => serialize(['off' => 'Off', 'on' => 'On']),
			'field_type_id' => $field_type_id,
			'lowest_setting_level' => 'INSTITUTION',
			'default_value' => 'off',
		]);

		$this->insert('setting_installation', [
			'key' => self::KEY,
			'value' => 'off',
		]);
	}

	public function safeDown()
	{
		$this->delete('setting_installation', '`key` = "' . self::KEY . '"');
		$this->delete('setting_metadata', '`key` = "' . self::KEY . '"');
	}
}
