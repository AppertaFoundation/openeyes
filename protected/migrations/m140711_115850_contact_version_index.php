
<?php

class m140711_115850_contact_version_index extends OEMigration
{
	public function safeUp()
	{
		$this->createIndex('contact_version_id_key', 'contact_version', 'id');
	}

	public function safeDown()
	{
		$this->dropIndex('contact_version_id_key', 'contact_version');
	}
}
