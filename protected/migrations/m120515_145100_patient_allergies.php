<?php

class m120515_145100_patient_allergies extends CDbMigration
{
	public function up()
	{
		// Create drug set tables
		$this->createTable('patient_allergy_assignment',array(
				'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
				'patient_id' => 'int(10) unsigned NOT NULL',
				'allergy_id' => 'int(10) unsigned NOT NULL',
				'PRIMARY KEY (`id`)',
				'CONSTRAINT `patient_allergy_assignment_patient_id_fk` FOREIGN KEY (`patient_id`) REFERENCES `patient` (`id`)',
				'CONSTRAINT `patient_allergy_assignment_allergy_id_fk` FOREIGN KEY (`allergy_id`) REFERENCES `allergy` (`id`)',
		), 'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin');

	}

	public function down()
	{
		$this->dropForeignKey('patient_allergy_assignment_patient_id_fk', 'patient_allergy_assignment');
		$this->dropForeignKey('patient_allergy_assignment_allergy_id_fk', 'patient_allergy_assignment');
		$this->dropTable('patient_allergy_assignment');
	}

	public function safeUp()
	{
		$this->up();
	}

	public function safeDown()
	{
		$this->down();
	}

}
