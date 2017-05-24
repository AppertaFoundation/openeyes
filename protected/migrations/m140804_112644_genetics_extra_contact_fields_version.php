<?php

class m140804_112644_genetics_extra_contact_fields_version extends CDbMigration
{
	public function up()
	{
		$this->addColumn('contact_version','maiden_name','varchar(100) COLLATE utf8_bin');
	}

	public function down()
	{
		$this->dropColumn('contact_version','maiden_name');
	}
}