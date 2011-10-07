<?php

class m110929_104213_modify_procedures extends CDbMigration
{
	public function up()
	{
		$this->dropForeignKey('service_subsection_fk','procedure');
		$this->dropForeignKey('service_fk','service_subsection');

		$this->renameTable('procedure', 'proc');
		$this->renameTable('procedure_opcs_assignment', 'proc_opcs_assignment');
		$this->renameTable('service_subsection', 'specialty_subsection');

		$this->dropForeignKey('operation_procedure_assignment_ibfk_1','operation_procedure_assignment');
		$this->renameColumn('operation_procedure_assignment', 'procedure_id', 'proc_id');
		$this->addForeignKey(
			'operation_procedure_assignment_ibfk_1','operation_procedure_assignment','proc_id','proc','id');

		$this->dropForeignKey('proc_opcs_assignment_ibfk_1','proc_opcs_assignment');
		$this->renameColumn('proc_opcs_assignment', 'procedure_id', 'proc_id');
		$this->addForeignKey(
			'proc_opcs_assignment_ibfk_1','proc_opcs_assignment','proc_id','proc','id');

		$this->renameColumn('specialty_subsection', 'service_id', 'specialty_id');
		$this->addForeignKey(
			'specialty_fk','specialty_subsection','specialty_id','specialty','id');

		$this->createTable('proc_specialty_subsection_assignment', array(
			'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
			'proc_id' => 'int(10) unsigned NOT NULL',
			'specialty_subsection_id' => 'int(10) unsigned NOT NULL',
			'PRIMARY KEY (`id`)',
			'KEY `proc_id` (`proc_id`)',
			'KEY `specialty_subsection_id` (`specialty_subsection_id`)',
		), 'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin');

		$this->addForeignKey(
			'proc_specialty_subsection_assignment_ibfk_1','proc_specialty_subsection_assignment','proc_id','proc','id');
		$this->addForeignKey(
			'proc_specialty_subsection_assignment_ibfk_2','proc_specialty_subsection_assignment','specialty_subsection_id','specialty_subsection','id');

		$this->createTable('proc_specialty_assignment', array(
			'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
			'proc_id' => 'int(10) unsigned NOT NULL',
			'specialty_id' => 'int(10) unsigned NOT NULL',
			'PRIMARY KEY (`id`)',
			'KEY `proc_id` (`proc_id`)',
			'KEY `specialty_id` (`specialty_id`)',
		), 'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin');

		$this->addForeignKey(
			'proc_specialty_assignment_ibfk_1','proc_specialty_assignment','proc_id','proc','id');
		$this->addForeignKey(
			'proc_specialty_assignment_ibfk_2','proc_specialty_assignment','specialty_id','specialty','id');

		// make sure each service has a specialty
		$this->insert('service_specialty_assignment', array(
			'service_id' => 3, // anaesthetic
			'specialty_id' => 3));
		$this->insert('service_specialty_assignment', array(
			'service_id' => 6, // corneal
			'specialty_id' => 5));
		$this->insert('service_specialty_assignment', array(
			'service_id' => 8, // pediatric
			'specialty_id' => 11));
		$this->insert('service_specialty_assignment', array(
			'service_id' => 9, // refractive
			'specialty_id' => 13));
		$this->insert('service_specialty_assignment', array(
			'service_id' => 10, // strabismus
			'specialty_id' => 14));
		$this->insert('service_specialty_assignment', array(
			'service_id' => 11, // vitroretinal
			'specialty_id' => 16));

		// update the data
		$data = $this->dbConnection->createCommand()
			->select('p.id, p.service_subsection_id, ss.specialty_id AS service_id, ss.name AS subsection_name')
			->from('proc p')
			->join('specialty_subsection ss', 'p.service_subsection_id = ss.id')
			->queryAll();

		foreach ($data as $row) {
			// update specialty_id in specialty_subsection to be service_id
			$specialty = $this->dbConnection->createCommand()
				->select('id')
				->from('service_specialty_assignment')
				->where('service_id = ?', array($row['service_id']))
				->queryRow();

			$this->update('specialty_subsection', array('specialty_id' => $specialty['id']),
				'specialty_id = :specialty_id', array(':specialty_id' => $row['service_id']));

			// add to specialty many-to-many
			$this->insert('proc_specialty_assignment', array(
				'proc_id' => $row['id'],
				'specialty_id' => $specialty['id']
			));

			// if we have a non-default subsection, add it there too
			if ($row['subsection_name'] != 'All') {
				$this->insert('proc_specialty_subsection_assignment', array(
					'proc_id' => $row['id'],
					'specialty_subsection_id' => $row['service_subsection_id']
				));
			}
		}

		$this->dropColumn('proc', 'service_subsection_id');
		$this->delete('specialty_subsection', 'name = ?', array('All'));
	}

	public function down()
	{
		$command = $this->dbConnection->createCommand('SET foreign_key_checks = 0;');
		$command->execute();

		$this->addColumn('proc', 'service_subsection_id', 'int(10) unsigned NOT NULL');

		$this->dropForeignKey('proc_specialty_assignment_ibfk_1','proc_specialty_assignment');
		$this->dropForeignKey('proc_specialty_assignment_ibfk_2','proc_specialty_assignment');
		$this->dropTable('proc_specialty_assignment');

		$this->dropForeignKey('proc_specialty_subsection_assignment_ibfk_1','proc_specialty_subsection_assignment');
		$this->dropForeignKey('proc_specialty_subsection_assignment_ibfk_2','proc_specialty_subsection_assignment');
		$this->dropTable('proc_specialty_subsection_assignment');

		$this->dropForeignKey('specialty_fk','specialty_subsection');

		$this->renameColumn('specialty_subsection', 'specialty_id', 'service_id');

		$this->dropForeignKey('operation_procedure_assignment_ibfk_1','operation_procedure_assignment');
		$this->renameColumn('operation_procedure_assignment', 'proc_id', 'procedure_id');
		$this->addForeignKey(
			'operation_procedure_assignment_ibfk_1','operation_procedure_assignment','proc_id','proc','id');

		$this->dropForeignKey('proc_opcs_assignment_ibfk_1','proc_opcs_assignment');
		$this->renameColumn('proc_opcs_assignment', 'proc_id', 'procedure_id');
		$this->addForeignKey(
			'proc_opcs_assignment_ibfk_1','proc_opcs_assignment','procedure_id','proc','id');

		$this->renameTable('proc_opcs_assignment', 'procedure_opcs_assignment');
		$this->renameTable('proc', 'procedure');
		$this->renameTable('specialty_subsection', 'service_subsection');

		$this->addForeignKey(
			'service_subsection_fk','procedure','service_subsection_id','service_subsection','id');

		$this->addForeignKey(
			'service_fk','service_subsection','service_id','service','id');

		$command = $this->dbConnection->createCommand('SET foreign_key_checks = 1;');
		$command->execute();
	}
}