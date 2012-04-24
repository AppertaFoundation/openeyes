<?php

class m120424_094328_remove_default_eye_id_for_diagnosis_and_operation extends CDbMigration
{
	public function up()
	{
		$this->alterColumn('element_diagnosis','eye_id','int(10) unsigned NOT NULL');
		$this->alterColumn('element_operation','eye_id','int(10) unsigned NOT NULL');
	}

	public function down()
	{
		$this->alterColumn('element_diagnosis','eye_id',"int(10) unsigned NOT NULL DEFAULT '1'");
		$this->alterColumn('element_operation','eye_id',"int(10) unsigned NOT NULL DEFAULT '1'");
	}
}
