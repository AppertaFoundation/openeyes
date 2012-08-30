<?php

class m120830_071031_set_default_eye_for_element_operation extends CDbMigration
{
	public function up()
	{
		$this->alterColumn('element_operation','eye_id','int(10) unsigned NOT NULL DEFAULT 1');
	}

	public function down()
	{
		$this->alterColumn('element_operation','eye_id','int(10) unsigned NOT NULL');
	}
}
