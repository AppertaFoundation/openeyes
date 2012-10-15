<?php

class m121004_083121_add_display_order_to_eye_table extends CDbMigration
{
	public function up()
	{
		$this->addColumn('eye','display_order','int(10) unsigned NOT NULL DEFAULT 1');
		$this->update('eye',array('display_order'=>2),'id=3');
		$this->update('eye',array('display_order'=>3),'id=1');
	}

	public function down()
	{
		$this->dropColumn('eye','display_order');
	}
}
