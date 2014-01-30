<?php

class m131128_111820_primary_key_for_proc_opcs_assignment extends CDbMigration
{
	public function up()
	{
		$this->addColumn('proc_opcs_assignment','id','int(10) unsigned NOT NULL');

		$this->dbConnection->createCommand("alter table proc_opcs_assignment drop primary key;")->query();

		foreach ($this->dbConnection->createCommand()->select("*")->from("proc_opcs_assignment")->queryAll() as $i => $poa) {
			$this->update('proc_opcs_assignment',array('id' => $i+1),"proc_id = {$poa['proc_id']} and opcs_code_id = {$poa['opcs_code_id']}");
		}

		$this->addPrimaryKey('idfk','proc_opcs_assignment','id');

		$this->alterColumn('proc_opcs_assignment','id','int(10) unsigned NOT NULL AUTO_INCREMENT');
	}

	public function down()
	{
		$this->alterColumn('proc_opcs_assignment','id','int(10) unsigned NOT NULL');

		$this->dbConnection->createCommand("alter table proc_opcs_assignment drop primary key;")->query();

		$this->addPrimaryKey("idfk", "proc_opcs_assignment", "proc_id,opcs_code_id");

		$this->dropColumn('proc_opcs_assignment','id');
	}
}
