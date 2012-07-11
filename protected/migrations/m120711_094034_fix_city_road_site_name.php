<?php

class m120711_094034_fix_city_road_site_name extends CDbMigration
{
	public function up()
	{
		$this->update('site',array('name'=>'Moorfields at City Road'),'id=1');
	}

	public function down()
	{
		$this->update('site',array('name'=>'Moorfields City Road'),'id=1');
	}
}
