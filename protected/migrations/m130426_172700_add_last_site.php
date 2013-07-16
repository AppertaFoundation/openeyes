<?php

class m130426_172700_add_last_site extends CDbMigration
{
	public function up()
	{
		$this->addColumn('user', 'last_site_id', 'int(10) UNSIGNED');
		$this->addForeignKey('user_last_site_id_fk', 'user', 'last_site_id', 'site', 'id');
	}

	public function down()
	{
		$this->dropForeignKey('user_last_site_id_fk', 'user');
		$this->dropColumn('user', 'last_site_id');
	}
}
