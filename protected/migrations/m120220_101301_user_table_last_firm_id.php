<?php

class m120220_101301_user_table_last_firm_id extends CDbMigration
{
	public function up()
	{
		$this->addColumn('user', 'last_firm_id', 'integer(11) unsigned NULL');
		$this->addForeignKey('user_last_firm_id_fk','user','last_firm_id','firm','id');
	}

	public function down()
	{
		$this->dropForeignKey('user_last_firm_id_fk','user');
		$this->dropColumn('user','last_firm_id');
	}
}
