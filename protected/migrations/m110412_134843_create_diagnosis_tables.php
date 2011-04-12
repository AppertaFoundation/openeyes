<?php

class m110412_134843_create_diagnosis_tables extends CDbMigration
{
	public function up()
	{
		$this->alterColumn('disorder', 'id', 'int(10) unsigned NOT NULL AUTO_INCREMENT');
		$this->alterColumn('disorder', 'fully_specified_name', 'varchar(255) CHARACTER SET latin1 NOT NULL');
		$this->alterColumn('disorder', 'term', 'varchar(255) CHARACTER SET latin1 NOT NULL');

		$this->insert('disorder', array(
			'fully_specified_name' => 'Myopia (disorder)',
			'term' => 'Myopia',
			'systemic' => 0
		));
		$this->insert('disorder', array(
			'fully_specified_name' => 'Retinal lattice degeneration (disorder)',
			'term' => 'Retinal lattice degeneration',
			'systemic' => 0
		));
		$this->insert('disorder', array(
			'fully_specified_name' => 'Posterior vitreous detachment (disorder)',
			'term' => 'Posterior vitreous detachment',
			'systemic' => 0
		));
		$this->insert('disorder', array(
			'fully_specified_name' => 'Vitreous hemorrhage (disorder)',
			'term' => 'Vitreous haemorrhage',
			'systemic' => 0
		));
		$this->insert('disorder', array(
			'fully_specified_name' => 'Essential hypertension (disorder)',
			'term' => 'Essential hypertension',
			'systemic' => 1
		));
		$this->insert('disorder', array(
			'fully_specified_name' => 'Diabetes mellitus type 1 (disorder)',
			'term' => 'Diabetes mellitus type 1',
			'systemic' => 1
		));
		$this->insert('disorder', array(
			'fully_specified_name' => 'Diabetes mellitus type 2 (disorder)',
			'term' => 'Diabetes mellitus type 2',
			'systemic' => 1
		));
		$this->insert('disorder', array(
			'fully_specified_name' => 'Myocardial infarction (disorder)',
			'term' => 'Myocardial infarction',
			'systemic' => 1
		));
		
		$this->renameColumn('diagnosis', 'datetime', 'created_on');
		$this->renameColumn('diagnosis', 'site', 'location');
		
		$this->insert('diagnosis', array(
			'patient_id' => 1,
			'user_id' => 1,
			'disorder_id' => 1,
			'created_on' => '0000-00-00 00:00:00',
			'location' => 0
		));
		$this->insert('diagnosis', array(
			'patient_id' => 1,
			'user_id' => 1,
			'disorder_id' => 2,
			'created_on' => '0000-00-00 00:00:00',
			'location' => 1
		));
		$this->insert('diagnosis', array(
			'patient_id' => 1,
			'user_id' => 1,
			'disorder_id' => 3,
			'created_on' => '0000-00-00 00:00:00',
			'location' => 2
		));
		
		$this->createTable('common_ophthalmic_disorder', array(
			'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
			'disorder_id' => 'int(10) unsigned NOT NULL',
			'specialty_id' => 'int(10) unsigned NOT NULL',
			'PRIMARY KEY (`id`)',
			'KEY `disorder_id` (`disorder_id`)',
			'KEY `specialty_id` (`specialty_id`)'
		), 'ENGINE=InnoDB  DEFAULT CHARSET=utf8');

		$this->insert('common_ophthalmic_disorder', array(
			'disorder_id' => 1, 'specialty_id' => 1
		));
		$this->insert('common_ophthalmic_disorder', array(
			'disorder_id' => 2, 'specialty_id' => 1
		));
		$this->insert('common_ophthalmic_disorder', array(
			'disorder_id' => 3, 'specialty_id' => 1
		));
		
		$this->addForeignKey(
			'common_ophthalmic_disorder_ibfk_1','common_ophthalmic_disorder','disorder_id','disorder','id');
		$this->addForeignKey(
			'common_ophthalmic_disorder_ibfk_2','common_ophthalmic_disorder','specialty_id','specialty','id');
		
		$this->createTable('common_systemic_disorder', array(
			'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
			'disorder_id' => 'int(10) unsigned NOT NULL',
			'PRIMARY KEY (`id`)',
			'KEY `disorder_id` (`disorder_id`)',
		), 'ENGINE=InnoDB  DEFAULT CHARSET=utf8');

		$this->insert('common_systemic_disorder', array('disorder_id' => 5));
		$this->insert('common_systemic_disorder', array('disorder_id' => 6));
		$this->insert('common_systemic_disorder', array('disorder_id' => 7));
		
		$this->addForeignKey(
			'common_systemic_disorder_ibfk_1','common_systemic_disorder','disorder_id','disorder','id');
	}

	public function down()
	{
		$command = $this->dbConnection->createCommand('SET foreign_key_checks = 0;');
		$command->execute();

		$this->truncateTable('common_systemic_disorder');
		$this->dropTable('common_systemic_disorder');
		
		$this->truncateTable('common_ophthalmic_disorder');
		$this->dropTable('common_ophthalmic_disorder');
		
		$this->truncateTable('diagnosis');
		
		$this->renameColumn('diagnosis', 'created_on', 'datetime');
		$this->renameColumn('diagnosis', 'location', 'site');		
		
		$this->truncateTable('disorder');
		
		$this->alterColumn('disorder', 'fully_specified_name', 'char(255) CHARACTER SET latin1 NOT NULL');
		$this->alterColumn('disorder', 'term', 'char(255) CHARACTER SET latin1 NOT NULL');

		$command = $this->dbConnection->createCommand('SET foreign_key_checks = 1;');
		$command->execute();
	}
}