<?php

class m211118_001218_split_setting_subspecialty extends OEMigration
{
	public function safeUp()
	{
		$this->createOETable(
			'setting_institution_subspecialty',
			array(
				'id' => 'pk',
				'institution_id' => 'int(10) unsigned NOT NULL',
				'subspecialty_id' => 'int(10) unsigned NOT NULL',
				'element_type_id' => 'int(10) unsigned',
				'key' => 'varchar(64)',
				'value' => 'text',
			),
			true);

		$this->addForeignKey(
			'setting_institution_subspecialty_institution_fk',
			'setting_institution_subspecialty',
			'institution_id',
			'institution',
			'id'
		);

		$this->addForeignKey(
			'setting_institution_subspecialty_subspecialty_fk',
			'setting_institution_subspecialty',
			'subspecialty_id',
			'subspecialty',
			'id'
		);

		$this->addForeignKey(
			'setting_institution_subspecialty_element_type_fk',
			'setting_institution_subspecialty',
			'element_type_id',
			'element_type',
			'id'
		);
	}

	public function safeDown()
	{
		$this->dropForeignKey(
			'setting_institution_subspecialty_element_type_fk',
			'setting_institution_subspecialty'
		);

		$this->dropForeignKey(
			'setting_institution_subspecialty_subspecialty_fk',
			'setting_institution_subspecialty'
		);

		$this->dropForeignKey(
			'setting_institution_subspecialty_institution_fk',
			'setting_institution_subspecialty'
		);

		$this->dropOETable('setting_institution_subspecialty', true);
	}
}
