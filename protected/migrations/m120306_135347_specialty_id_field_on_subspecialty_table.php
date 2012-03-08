<?php

class m120306_135347_specialty_id_field_on_subspecialty_table extends CDbMigration
{
	public function up()
	{
		$this->addColumn('subspecialty','specialty_id','int(10) unsigned NOT NULL DEFAULT 109');
		$this->alterColumn('subspecialty','specialty_id','int(10) unsigned NOT NULL');
		$this->createIndex('subspecialty_specialty_id_fk','subspecialty','specialty_id');
		$this->addForeignKey('subspecialty_specialty_id_fk','subspecialty','specialty_id','specialty','id');
	}

	public function down()
	{
		$this->dropForeignKey('subspecialty_specialty_id_fk','subspecialty');
		$this->dropIndex('subspecialty_specialty_id_fk','subspecialty');
		$this->dropColumn('subspecialty','specialty_id');
	}
}
