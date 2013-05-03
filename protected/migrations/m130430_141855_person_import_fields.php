<?php

class m130430_141855_person_import_fields extends CDbMigration
{
	public function up()
	{
		$this->addColumn('person','source_id','int(10) unsigned NULL');
		$this->createIndex('person_source_id_fk','person','source_id');
		$this->addForeignKey('person_source_id_fk','person','source_id','import_source','id');

		$person_ids = array();

		foreach (Yii::app()->db->createCommand()
			->select("person.id")
			->from("person")
			->join("contact","person.contact_id = contact.id")
			->join("contact_metadata","contact_metadata.contact_id = contact.id")
			->where("`key` = :key",array(':key'=>'gmc_number'))
			->queryAll() as $person) {
			$person_ids[] = $person['id'];
		}

		if (!empty($person_ids)) {
			$this->update('person',array('source_id'=>1),"id in (".implode(',',$person_ids).")");
		}
	}

	public function down()
	{
		$this->dropForeignKey('person_source_id_fk','person');
		$this->dropIndex('person_source_id_fk','person');
		$this->dropColumn('person','source_id');
	}
}
