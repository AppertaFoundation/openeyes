<?php

class m110413_084906_create_operation_tables extends CDbMigration
{
	public function up()
	{
		$this->insert('event_type', array(
			'name' => 'operation',
			'first_in_episode_possible' => 1
		));
		
		$this->createTable('element_appointment', array(
			'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
			'event_id' => 'int(10) unsigned NOT NULL',
			'PRIMARY KEY (`id`)',
			'KEY `event_id` (`event_id`)',
		), 'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin');
		
		$this->createTable('element_operation', array(
			'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
			'event_id' => 'int(10) unsigned NOT NULL',
			'eye' => "tinyint(1) unsigned DEFAULT '0'",
			'comments' => 'text',
			'total_duration' => 'smallint(5) unsigned NOT NULL',
			'consultant_required' => "tinyint(1) unsigned DEFAULT '0'",
			'anaesthetist_required' => "tinyint(1) unsigned DEFAULT '0'",
			'anaesthetic_type' => "tinyint(1) unsigned DEFAULT '0'",
			'overnight_stay' => "tinyint(1) unsigned DEFAULT '0'",
			'schedule_timeframe' => "tinyint(1) unsigned DEFAULT '0'",
			'PRIMARY KEY (`id`)',
			'KEY `event_id` (`event_id`)',
		), 'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin');
		
		$this->createTable('operation_procedure_assignment', array(
			'operation_id' => 'int(10) unsigned NOT NULL',
			'procedure_id' => 'int(10) unsigned NOT NULL',
			'display_order' => "tinyint(3) unsigned DEFAULT '0'",
			'duration' => 'smallint(5) unsigned NOT NULL',
			'PRIMARY KEY (`operation_id`, `procedure_id`)',
			'KEY `operation_id` (`operation_id`)',
			'KEY `procedure_id` (`procedure_id`)',
		), 'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin');
		
		$this->createTable('procedure', array(
			'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
			'term' => 'varchar(255) CHARACTER SET latin1 NOT NULL',
			'short_format' => 'varchar(255) CHARACTER SET latin1 NOT NULL',
			'default_duration' => 'smallint(5) unsigned NOT NULL',
			'service_subsection_id' => 'int(10) unsigned NOT NULL',
			'PRIMARY KEY (`id`)',
			'KEY `service_subsection_id` (`service_subsection_id`)',
		), 'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin');
		
		$this->createTable('procedure_opcs_assignment', array(
			'procedure_id' => 'int(10) unsigned NOT NULL',
			'opcs_code_id' => 'int(10) unsigned NOT NULL',
			'PRIMARY KEY (`procedure_id`, `opcs_code_id`)',
			'KEY `opcs_code_id` (`opcs_code_id`)',
			'KEY `procedure_id` (`procedure_id`)',
		), 'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin');
		
		$this->createTable('service_subsection', array(
			'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
			'service_id' => 'int(10) unsigned NOT NULL',
			'name' => 'varchar(255) CHARACTER SET latin1 NOT NULL',
			'PRIMARY KEY (`id`)',
			'KEY `service_id` (`service_id`)',
		), 'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin');
		
		$this->createTable('opcs_code', array(
			'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
			'name' => 'varchar(255) CHARACTER SET latin1 NOT NULL',
			'description' => 'varchar(255) CHARACTER SET latin1 NOT NULL',
			'PRIMARY KEY (`id`)',
		), 'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin');
		
		$this->addForeignKey(
			'element_appointment_ibfk_1','element_appointment','event_id','event','id');
		
		$this->addForeignKey(
			'element_operation_ibfk_1','element_operation','event_id','event','id');

		$this->addForeignKey(
			'operation_fk','operation_procedure_assignment','operation_id','element_operation','id');
		$this->addForeignKey(
			'operation_procedure_assignment_ibfk_1','operation_procedure_assignment','procedure_id','procedure','id');

		$this->addForeignKey(
			'service_subsection_fk','procedure','service_subsection_id','service_subsection','id');
		
		$this->addForeignKey(
			'opcs_code_fk','procedure_opcs_assignment','opcs_code_id','opcs_code','id');
		$this->addForeignKey(
			'procedure_opcs_assignment_ibfk_1','procedure_opcs_assignment','procedure_id','procedure','id');

		$this->addForeignKey(
			'service_fk','service_subsection','service_id','service','id');
		
		$this->insert('element_type', array(
			'name' => 'Appointment',
			'class_name' => 'ElementAppointment'
		));
		$this->insert('element_type', array(
			'name' => 'Operation',
			'class_name' => 'ElementOperation'
		));
		
		$eventType = $this->dbConnection->createCommand()
			->select('id')
			->from('event_type')
			->where('name=:name', array(':name'=>'operation'))
			->queryRow();
		$specialties = $this->dbConnection->createCommand()
			->select('id')
			->from('specialty')
			->queryAll();

		// extract element type
		$elementTypes = $this->dbConnection->createCommand()
			->select('id')
			->from('element_type')
			->where('name=:name1 OR name=:name2',
				array(':name1'=>'Appointment',':name2'=>'Operation'))
			->queryAll();

		$order = 1;
		foreach ($elementTypes as $elementType) {
			// create possible element type
			$this->insert('possible_element_type',
				array(
					'event_type_id' => $eventType['id'],
					'element_type_id' => $elementType['id'],
					'num_views' => 1,
					'order' => $order
				)
			);

			// extract possible element type
			$possibleElementType = $this->dbConnection->createCommand()
				->select('id')
				->from('possible_element_type')
				->where('event_type_id=:event_type_id and element_type_id=:element_type_id',
					array(':event_type_id'=>$eventType['id'],':element_type_id'=>$elementType['id']))
				->queryRow();

			// create site element type entries
			foreach ($specialties as $specialty) {
				$this->insert('site_element_type', array(
					'possible_element_type_id' => $possibleElementType['id'],
					'specialty_id' => $specialty['id'],
					'view_number' => 1,
					'required' => 1,
					'first_in_episode' => 0
				));
				$this->insert('site_element_type', array(
					'possible_element_type_id' => $possibleElementType['id'],
					'specialty_id' => $specialty['id'],
					'view_number' => 1,
					'required' => 1,
					'first_in_episode' => 1
				));
			}
			$order++;
		}
	}

	public function down()
	{
		$eventType = $this->dbConnection->createCommand()
			->select('id')
			->from('event_type')
			->where('name=:name', array(':name'=>'operation'))
			->queryRow();
		$specialties = $this->dbConnection->createCommand()
			->select('id')
			->from('specialty')
			->queryAll();
		$elementTypes = $this->dbConnection->createCommand()
			->select('id')
			->from('element_type')
			->where('name=:name1 OR name=:name2',
				array(':name1'=>'Appointment',':name2'=>'Operation'))
			->queryRow();
		
		foreach ($elementTypes as $elementType) {
			$possibleElementType = $this->dbConnection->createCommand()
				->select('id')
				->from('possible_element_type')
				->where('event_type_id=:event_type_id and element_type_id=:element_type_id',
					array(':event_type_id'=>$eventType['id'],':element_type_id'=>$elementType['id']))
				->queryRow();

			// remove site_element_type entries
			foreach ($specialties as $specialty) {
				$this->delete('site_element_type', 'possible_element_type_id = :possible_element_type_id and specialty_id = :specialty_id',
					array(':possible_element_type_id' => $possibleElementType['id'], ':specialty_id' => $specialty['id'])
				);
			}

			// remove possible_element_type entries
			$this->delete('possible_element_type', 'id = :id',
				array(':id' => $possibleElementType['id'])
			);
		}
		
		$this->dropForeignKey('service_fk','service_subsection');	
		
		$this->dropForeignKey('opcs_code_fk','procedure_opcs_assignment');
		$this->dropForeignKey('procedure_opcs_assignment_ibfk_1','procedure_opcs_assignment');
		
		$this->dropForeignKey('service_subsection_fk','procedure');
		
		$this->dropForeignKey('operation_fk','operation_procedure_assignment');
		$this->dropForeignKey('operation_procedure_assignment_ibfk_1','operation_procedure_assignment');
		
		$this->dropForeignKey('element_operation_ibfk_1','element_operation');

		if ($this->dbConnection->schema->getTable('{{element_appointment}}')) {
			$this->dropForeignKey('element_appointment_ibfk_1','element_appointment');
			$this->dropTable('element_appointment');
		}
		
		$this->dropTable('opcs_code');
		
		$this->dropTable('service_subsection');
		
		$this->dropTable('procedure_opcs_assignment');
		
		$this->dropTable('procedure');
		
		$this->dropTable('operation_procedure_assignment');
		
		$this->dropTable('element_operation');
		
		$command = $this->dbConnection->createCommand('SET foreign_key_checks = 0;');
		$command->execute();
		
		$this->delete('element_type', 'name=:name AND class_name=:class', array(
			':name' => 'Appointment',':class' => 'ElementAppointment'));
		$this->delete('element_type', 'name=:name AND class_name=:class', array(
			':name' => 'Operation',':class' => 'ElementOperation'));
		
		$this->delete('event_type', 'name=:name AND first_in_episode_possible=:fiep', array(
			':name' => 'operation', ':fiep' => 1));
		
		$command = $this->dbConnection->createCommand('SET foreign_key_checks = 1;');
		$command->execute();
	}
}