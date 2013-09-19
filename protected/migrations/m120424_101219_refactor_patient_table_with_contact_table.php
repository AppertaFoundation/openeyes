<?php

class m120424_101219_refactor_patient_table_with_contact_table extends CDbMigration
{
	public function up()
	{
		$this->dropTable('patient_contact_assignment');

		$this->addColumn('contact','parent_class','varchar(40) COLLATE utf8_bin NOT NULL');
		$this->addColumn('contact','parent_id',"int(10) unsigned NOT NULL DEFAULT '1'");
		$this->alterColumn('contact','title','varchar(20) COLLATE utf8_bin NULL');

		foreach ($this->dbConnection->createCommand()->select('patient.*')->from('patient')->queryAll() as $patient) {
			$this->insert('contact',array(
					'parent_class' => 'Patient',
					'parent_id' => $patient['id'],
					'primary_phone' => $patient['primary_phone'],
					'title' => $patient['title'],
					'first_name' => $patient['first_name'],
					'last_name' => $patient['last_name'],
					'last_modified_user_id' => 1,
					'last_modified_date' => '2000-01-01 00:00:00',
					'created_user_id' => 1,
					'created_date'=>'2000-01-01 00:00:00'
			));
		}

		foreach ($this->dbConnection->createCommand()->select('id, contact_id')->from('gp')->queryAll() as $gp) {
			$this->update('contact',array('parent_class'=>'Gp','parent_id'=>$gp['id']),'id='.$gp['contact_id']);
		}

		$this->dropForeignKey('gp_contact_id_fk_1','gp');
		$this->dropIndex('gp_contact_id_fk_1','gp');
		$this->dropColumn('gp','contact_id');

		foreach ($this->dbConnection->createCommand()->select('id, contact_id')->from('consultant')->queryAll() as $consultant) {
			$this->update('contact',array('parent_class'=>'Consultant','parent_id'=>$consultant['id']),'id='.$consultant['contact_id']);
		}

		$this->dropForeignKey('consultant_contact_id_fk_1','consultant');
		$this->dropIndex('consultant_contact_id_fk_1','consultant');
		$this->dropColumn('consultant','contact_id');

		$this->dropColumn('patient','primary_phone');
		$this->dropColumn('patient','title');
		$this->dropColumn('patient','first_name');
		$this->dropColumn('patient','last_name');
	}

	public function down()
	{
		$this->addColumn('patient','primary_phone','varchar(20) COLLATE utf8_bin DEFAULT NULL');
		$this->addColumn('patient','title','varchar(8) COLLATE utf8_bin DEFAULT NULL');
		$this->addColumn('patient','first_name','varchar(40) CHARACTER SET utf8 NOT NULL');
		$this->addColumn('patient','last_name','varchar(40) CHARACTER SET utf8 NOT NULL');

		$this->addColumn('consultant','contact_id','int(10) unsigned NOT NULL DEFAULT 1');
		$this->createIndex('consultant_contact_id_fk_1','consultant','contact_id');
		$this->addForeignKey('consultant_contact_id_fk_1','consultant','contact_id','contact','id');

		$this->addColumn('gp','contact_id','int(10) unsigned NOT NULL DEFAULT 1');
		$this->createIndex('gp_contact_id_fk_1','gp','contact_id');
		$this->addForeignKey('gp_contact_id_fk_1','gp','contact_id','contact','id');

		foreach ($this->dbConnection->createCommand()->select('contact.*')->from('contact')->queryAll() as $contact) {
			if ($contact['parent_class'] == 'Patient') {
				$this->update('patient',array('primary_phone'=>$contact['primary_phone'],'title'=>$contact['title'],'first_name'=>$contact['first_name'],'last_name'=>$contact['last_name']),'id='.$contact['parent_id']);
			} elseif ($contact['parent_class'] == 'Gp') {
				$this->update('gp',array('contact_id'=>$contact['id']),'id='.$contact['parent_id']);
			} elseif ($contact['parent_class'] == 'Consultant') {
				$this->update('consultant',array('contact_id'=>$contact['id']),'id='.$contact['parent_id']);
			}
		}

		$this->dropColumn('contact','parent_id');
		$this->dropColumn('contact','parent_class');
		$this->alterColumn('contact','title','varchar(20) COLLATE utf8_bin NOT NULL');

		$this->createTable('patient_contact_assignment', array(
				'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
				'patient_id' => 'int(10) unsigned NOT NULL',
				'contact_id' => 'int(10) unsigned NOT NULL',
				'last_modified_user_id' => "int(10) unsigned NOT NULL DEFAULT '1'",
				'last_modified_date' => "datetime NOT NULL DEFAULT '1900-01-01 00:00:00'",
				'created_user_id' => "int(10) unsigned NOT NULL DEFAULT '1'",
				'created_date' => "datetime NOT NULL DEFAULT '1900-01-01 00:00:00'",
				'PRIMARY KEY (`id`)',
				'KEY `patient_id` (`patient_id`)',
				'KEY `contact_id` (`contact_id`)',
				'KEY `patient_contact_assignment_last_modified_user_id_fk` (`last_modified_user_id`)',
				'KEY `patient_contact_assignment_created_user_id_fk` (`created_user_id`)',
				'CONSTRAINT `patient_contact_assignment_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `patient_contact_assignment_fk_1` FOREIGN KEY (`patient_id`) REFERENCES `patient` (`id`)',
				'CONSTRAINT `patient_contact_assignment_fk_2` FOREIGN KEY (`contact_id`) REFERENCES `contact` (`id`)',
				'CONSTRAINT `patient_contact_assignment_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)',
			),
			'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin'
		);
	}
}
