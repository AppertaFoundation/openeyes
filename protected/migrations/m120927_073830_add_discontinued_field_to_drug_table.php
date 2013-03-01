<?php

class m120927_073830_add_discontinued_field_to_drug_table extends CDbMigration
{
	public function up()
	{
		$this->addColumn('drug','discontinued','tinyint(1) unsigned NOT NULL DEFAULT 0');
	}

	public function down()
	{
		$this->dropColumn('drug','discontinued');
	}
}
