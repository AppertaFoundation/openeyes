<?php

class m120918_140159_change_instances_of_char_datatype_to_varchar extends CDbMigration
{
	public function up()
	{
		$this->alterColumn('address','type','varchar(1) COLLATE utf8_bin NOT NULL');
		$this->alterColumn('consultant','gender','varchar(1) COLLATE utf8_bin DEFAULT NULL');
		$this->alterColumn('country','code','varchar(2) COLLATE utf8_bin DEFAULT NULL');
		$this->alterColumn('disorder','fully_specified_name','varchar(255) COLLATE utf8_bin NOT NULL');
		$this->alterColumn('disorder','term','varchar(255) COLLATE utf8_bin NOT NULL');
		$this->alterColumn('firm','pas_code','varchar(4) COLLATE utf8_bin DEFAULT NULL');
		$this->alterColumn('nsc_grade','name','varchar(3) COLLATE utf8_bin NOT NULL');
		$this->alterColumn('patient','gender','varchar(1) COLLATE utf8_bin DEFAULT NULL');
		$this->alterColumn('site','code','varchar(2) COLLATE utf8_bin NOT NULL');
		$this->alterColumn('specialist','gender','varchar(1) COLLATE utf8_bin DEFAULT NULL');
		$this->alterColumn('subspecialty','ref_spec','varchar(3) COLLATE utf8_bin NOT NULL');
		$this->alterColumn('user_session','id','varchar(32) COLLATE utf8_bin NOT NULL');
	}

	public function down()
	{
		$this->alterColumn('address','type','char(1) COLLATE utf8_bin NOT NULL');
		$this->alterColumn('consultant','gender','char(1) CHARACTER SET utf8 DEFAULT NULL');
		$this->alterColumn('country','code','char(2) COLLATE utf8_bin DEFAULT NULL');
		$this->alterColumn('disorder','fully_specified_name','char(255) CHARACTER SET utf8 NOT NULL');
		$this->alterColumn('disorder','term','char(255) CHARACTER SET utf8 NOT NULL');
		$this->alterColumn('firm','pas_code','char(4) COLLATE utf8_bin DEFAULT NULL');
		$this->alterColumn('nsc_grade','name','char(3) COLLATE utf8_bin NOT NULL');
		$this->alterColumn('patient','gender','char(1) CHARACTER SET utf8 DEFAULT NULL');
		$this->alterColumn('site','code','char(2) COLLATE utf8_bin NOT NULL');
		$this->alterColumn('specialist','gender','char(1) CHARACTER SET utf8 DEFAULT NULL');
		$this->alterColumn('subspecialty','ref_spec','char(3) COLLATE utf8_bin NOT NULL');
		$this->alterColumn('user_session','id','char(32) NOT NULL');
	}
}
