<?php

class m111220_130948_element_operation_user_id extends CDbMigration
{
	public function up()
	{
		$this->addColumn('element_operation','user_id','int(10) unsigned NOT NULL DEFAULT 1');
		$this->addForeignKey('user_fk','element_operation','user_id','user','id');
	}

	public function down()
	{
		$this->dropForeignKey('user_fk','element_operation');
		$this->dropColumn('element_operation','user_id');
	}
}
