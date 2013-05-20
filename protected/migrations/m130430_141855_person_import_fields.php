<?php

class m130430_141855_person_import_fields extends CDbMigration
{
	public function up()
	{
		$this->addColumn('person','source_id','int(10) unsigned NULL');
		$this->createIndex('person_source_id_fk','person','source_id');
		$this->addForeignKey('person_source_id_fk','person','source_id','import_source','id');
		$this->addColumn('person','remote_id','varchar(10) COLLATE utf8_bin NOT NULL');

		foreach (Yii::app()->db->createCommand()
			->select("person.id, contact_metadata.value")
			->from("person")
			->join("contact","person.contact_id = contact.id")
			->join("contact_metadata","contact_metadata.contact_id = contact.id")
			->where("`key` = :key",array(':key'=>'gmc_number'))
			->queryAll() as $person) {

			$this->update('person',array('source_id'=>1,'remote_id'=>$person['value']),"id={$person['id']}");
		}
	}

	public function down()
	{
		$this->dropForeignKey('person_source_id_fk','person');
		$this->dropIndex('person_source_id_fk','person');
		$this->dropColumn('person','source_id');
	}
}
