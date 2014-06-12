<?php

class m140610_143459_medication_adherence_and_comments extends OEMigration
{
	public function up()
	{

		$this->createOETable(
			'medication_adherence_level',
			array(
				'id' => 'pk',
				'name' => 'varchar(128) COLLATE utf8_bin NOT NULL',
				'display_order' => 'int(10) unsigned NOT NULL DEFAULT 1',
			),
			true
		);

		$this->createOETable(
			'medication_adherence',
			array(
				'id' => 'pk',
				'patient_id' => 'int(11) unsigned unique not null',
				'medication_adherence_level_id' => 'int(11) not null',
				'comments' => 'text',
				'constraint medication_adherence_patient_id_fk foreign key (patient_id) references patient (id)',
				'constraint medication_adherence_level_fk foreign key (medication_adherence_level_id) references medication_adherence_level (id)',
			),
			true
		);

		$this->insert('medication_adherence_level',array('name'=>'Never misses','display_order'=>1));
		$this->insert('medication_adherence_level',array('name'=>'Occasionally misses','display_order'=>2));
		$this->insert('medication_adherence_level',array('name'=>'Frequently misses (weekly)','display_order'=>3));
		$this->insert('medication_adherence_level',array('name'=>'Usually misses (daily)','display_order'=>4));
		$this->insert('medication_adherence_level',array('name'=>'Not using as per record','display_order'=>5));

	}

	public function down()
	{

		$this->dropTable('medication_adherence');
		$this->dropTable('medication_adherence_level');
		$this->dropTable('medication_adherence_version');
		$this->dropTable('medication_adherence_level_version');

	}
}