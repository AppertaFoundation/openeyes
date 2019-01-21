<?php

class m181116_163210_remove_city_road_and_satellite_options_admin extends CDbMigration
{
	public function up()
	{
		$this->delete('setting_metadata', "element_type_id is null and `key` = 'city_road_satellite_view'");
	}

	public function down()
	{
        $this->insert('setting_metadata', array(
            'element_type_id' => null,
            'field_type_id' => 3,
            'key' => 'city_road_satellite_view',
            'name' => 'City Road and Satellite Options',
            'data' => 'a:2:{s:2:"on";s:2:"On";s:3:"off";s:3:"Off";}',
            'default_value' => 'off',
        ));
	}
}