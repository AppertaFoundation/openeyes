<?php

class m140718_131304_genetics_extra_contact_fields extends CDbMigration
{
	public function up()
	{
		$this->addColumn('contact','maiden_name','varchar(100) COLLATE utf8_bin NOT NULL');
		$this->addColumn('patient','yob','int(2) unsigned NULL');
	}

	public function down()
	{
		$this->dropColumn('patient','yob');
		$this->dropColumn('contact','maiden_name');
	}
}