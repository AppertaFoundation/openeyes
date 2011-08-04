<?php

class m110804_070139_create_ward_data extends CDbMigration
{
	public function up()
	{
		$this->insert('ward', array(
			'site_id' => 1,
			'name' => 'Male Childrens Ward',
			'restriction' => Ward::RESTRICTION_MALE + Ward::RESTRICTION_UNDER_16
		));

		$this->insert('ward', array(
			'site_id' => 1,
			'name' => 'Female Childrens Ward',
			'restriction' => Ward::RESTRICTION_FEMALE + Ward::RESTRICTION_UNDER_16
		));

		$this->insert('ward', array(
			'site_id' => 1,
			'name' => 'Male Adult Ward',
			'restriction' => Ward::RESTRICTION_MALE + Ward::RESTRICTION_ATLEAST_16
		));

		$this->insert('ward', array(
			'site_id' => 1,
			'name' => 'Female Adult Ward',
			'restriction' => Ward::RESTRICTION_FEMALE + Ward::RESTRICTION_ATLEAST_16
		));
	}

	public function down()
	{
		$this->delete('ward', 'site_id = 1');
	}
}