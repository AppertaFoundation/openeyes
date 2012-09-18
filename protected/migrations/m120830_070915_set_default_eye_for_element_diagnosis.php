<?php

class m120830_070915_set_default_eye_for_element_diagnosis extends CDbMigration
{
	public function up()
	{
		$this->alterColumn('element_diagnosis','eye_id','int(10) unsigned NOT NULL DEFAULT 1');
	}

	public function down()
	{
		$this->alterColumn('element_diagnosis','eye_id','int(10) unsigned NOT NULL');
	}
}
