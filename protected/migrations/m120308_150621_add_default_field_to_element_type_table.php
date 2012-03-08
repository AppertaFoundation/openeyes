<?php

class m120308_150621_add_default_field_to_element_type_table extends CDbMigration
{
	public function up()
	{
		$this->addColumn('element_type','default','tinyint(1) unsigned NOT NULL DEFAULT 1');
	}

	public function down()
	{
		$this->dropColumn('element_type','default');
	}
}
