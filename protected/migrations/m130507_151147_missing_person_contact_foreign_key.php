<?php

class m130507_151147_missing_person_contact_foreign_key extends CDbMigration
{
	public function up()
	{
		$this->createIndex('person_contact_id_fk','person','contact_id');
		$this->addForeignKey('person_contact_id_fk','person','contact_id','contact','id');
	}

	public function down()
	{
		$this->dropForeignKey('person_contact_id_fk','person');
		$this->dropIndex('person_contact_id_fk','person');
	}
}
