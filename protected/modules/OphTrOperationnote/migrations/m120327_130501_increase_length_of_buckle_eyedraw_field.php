<?php

class m120327_130501_increase_length_of_buckle_eyedraw_field extends CDbMigration
{
	public function up()
	{
		$this->alterColumn('et_ophtroperationnote_buckle','eyedraw','varchar(4096) COLLATE utf8_bin NOT NULL');
	}

	public function down()
	{
		$this->alterColumn('et_ophtroperationnote_buckle','eyedraw','varchar(1024) COLLATE utf8_bin NOT NULL');
	}
}
