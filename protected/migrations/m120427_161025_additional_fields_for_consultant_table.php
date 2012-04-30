<?php

class m120427_161025_additional_fields_for_consultant_table extends CDbMigration
{
	public function up()
	{
		$this->dropColumn('consultant','obj_prof');
		$this->dropColumn('consultant','nat_id');
		$this->dropColumn('consultant','pas_code');

		$this->addColumn('consultant','gmc_number','varchar(7) COLLATE utf8_bin NULL');
		$this->addColumn('consultant','practitioner_code','varchar(8) COLLATE utf8_bin NULL');
		$this->addColumn('consultant','gender','char(1) CHARACTER SET utf8 DEFAULT NULL');
	}

	public function down()
	{
		$this->dropColumn('consultant','gender');
		$this->dropColumn('consultant','practitioner_code');
		$this->dropColumn('consultant','gmc_number');

		$this->addColumn('consultant','obj_prof','varchar(20) COLLATE utf8_bin NOT NULL');
		$this->addColumn('consultant','nat_id','varchar(20) COLLATE utf8_bin NOT NULL');
		$this->addColumn('consultant','pas_code','char(4) COLLATE utf8_bin DEFAULT NULL');
	}
}
