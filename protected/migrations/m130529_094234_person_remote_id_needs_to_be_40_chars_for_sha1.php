<?php

class m130529_094234_person_remote_id_needs_to_be_40_chars_for_sha1 extends CDbMigration
{
	public function up()
	{
		$this->alterColumn('person','remote_id','varchar(40) COLLATE utf8_bin NOT NULL');
	}

	public function down()
	{
		$this->alterColumn('person','remote_id','varchar(10) COLLATE utf8_bin NOT NULL');
	}
}
