<?php

class m120302_092216_pas_patient_assignment extends CDbMigration
{
	public function up()
	{
		// Create new PAS mapping table to hold foreign keys
		$this->createTable('pas_patient_assignment', array(
				'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
				'patient_id' => 'int(10) unsigned NOT NULL',
				'external_id' => 'int(10) unsigned NOT NULL',
				'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
				'created_date' => 'datetime NOT NULL',
				'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
				'last_modified_date' => 'datetime NOT NULL',
				'PRIMARY KEY (`id`)',
				'UNIQUE KEY `patient_id` (`patient_id`)',
				'UNIQUE KEY `external_id` (`external_id`)',
		), 'ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin'
		);
		$this->addForeignKey('pas_patient_assignment_fk_1', 'pas_patient_assignment', 'patient_id', 'patient', 'id');

		// Change patient key to be auto increment and reset counter
		$this->alterColumn('patient', 'id', 'int(10) unsigned NOT NULL AUTO_INCREMENT');

		// Disable audit trail for address migration
		$audit_trail = Yii::app()->params['audit_trail'];
		Yii::app()->params['audit_trail'] = false;

		// Disable foreign key checks (keys being reassigned)
		$this->execute('SET foreign_key_checks = 0');

		// Migrate existing patients
		echo "Migrating patients...\n";
		$patients = Patient::model()->findAll();

		// Set patient counter above highest existing ID to avoid collisions
		$patient_counter = $this->getDbConnection()->createCommand('SELECT MAX(id) FROM `patient`')->queryScalar() + 1;

		foreach ($patients as $patient) {
			$old_patient_id = $patient->id;
			$new_patient_id = $patient_counter;

			// Remap Episodes
			$this->execute('UPDATE `episode` SET `patient_id` = :new_patient_id WHERE `patient_id` = :old_patient_id', array(
				':new_patient_id' => $new_patient_id,
				':old_patient_id' => $old_patient_id,
			));

			// Remap Referrals
			$this->execute('UPDATE `referral` SET `patient_id` = :new_patient_id WHERE `patient_id` = :old_patient_id', array(
				':new_patient_id' => $new_patient_id,
				':old_patient_id' => $old_patient_id,
			));

			// Remap Patient/Contact assignment
			$this->execute('UPDATE `patient_contact_assignment` SET `patient_id` = :new_patient_id WHERE `patient_id` = :old_patient_id', array(
				':new_patient_id' => $new_patient_id,
				':old_patient_id' => $old_patient_id,
			));

			// Remap Addresses
			$this->execute('UPDATE `address` SET `parent_id` = :new_patient_id WHERE `parent_class` = \'Patient\' AND `parent_id` = :old_patient_id', array(
				':new_patient_id' => $new_patient_id,
				':old_patient_id' => $old_patient_id,
			));

			// Update Patient
			$this->execute('UPDATE `patient` SET `id` = :new_patient_id WHERE `id` = :old_patient_id', array(
				':new_patient_id' => $new_patient_id,
				':old_patient_id' => $old_patient_id,
			));

			$patient_counter++;

		}

		// Set auto_increment counter
		$this->execute('ALTER TABLE `patient` AUTO_INCREMENT = '.$patient_counter);

		echo "done.\n";

		Yii::app()->params['audit_trail'] = $audit_trail;
		$this->execute('SET foreign_key_checks = 1');
	}

	public function down()
	{
		echo "m120302_092216_pas_patient_assignment does not support migration down.\n";
		return false;
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
