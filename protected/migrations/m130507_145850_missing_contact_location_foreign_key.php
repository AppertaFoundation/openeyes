<?php

class m130507_145850_missing_contact_location_foreign_key extends CDbMigration
{
	public function up()
	{
		$this->createIndex('contact_location_contact_id_fk','contact_location','contact_id');
		$this->addForeignKey('contact_location_contact_id_fk','contact_location','contact_id','contact','id');
	}

	public function down()
	{
		$this->dropForeignKey('contact_location_contact_id_fk','contact_location');
		$this->dropIndex('contact_location_contact_id_fk','contact_location');
	}
}
