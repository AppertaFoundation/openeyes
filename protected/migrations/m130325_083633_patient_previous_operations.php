<?php

class m130325_083633_patient_previous_operations extends CDbMigration
{
	public function up()
	{
		$this->createTable('previous_operation',array(
				'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
				'patient_id' => 'int(10) unsigned NOT NULL',
				'side_id' => 'int(10) unsigned NULL',
				'operation' => 'varchar(1024) COLLATE utf8_bin NOT NULL',
				'date' => 'varchar(10) COLLATE utf8_bin NOT NULL',
				'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'last_modified_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'created_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'PRIMARY KEY (`id`)',
				'KEY `previous_operation_last_modified_user_id_fk` (`last_modified_user_id`)',
				'KEY `previous_operation_created_user_id_fk` (`created_user_id`)',
				'KEY `previous_operation_patient_id_fk` (`patient_id`)',
				'KEY `previous_operation_side_id_fk` (`side_id`)',
				'CONSTRAINT `previous_operation_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `previous_operation_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `previous_operation_patient_id_fk` FOREIGN KEY (`patient_id`) REFERENCES `patient` (`id`)',
				'CONSTRAINT `previous_operation_side_id_fk` FOREIGN KEY (`side_id`) REFERENCES `eye` (`id`)',
			),
			'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin'
		);

		$this->createTable('common_previous_operation',array(
				'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
				'name' => 'varchar(1024) COLLATE utf8_bin NOT NULL',
				'display_order' => 'tinyint(1) unsigned NOT NULL',
				'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'last_modified_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'created_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'PRIMARY KEY (`id`)',
				'KEY `common_previous_operation_last_modified_user_id_fk` (`last_modified_user_id`)',
				'KEY `common_previous_operation_created_user_id_fk` (`created_user_id`)',
				'CONSTRAINT `common_previous_operation_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `common_previous_operation_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)',
			),
			'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin'
		);

		$this->insert('common_previous_operation',array('name'=>'Cataract surgery','display_order'=>1));
		$this->insert('common_previous_operation',array('name'=>'Corneal surgery','display_order'=>2));
		$this->insert('common_previous_operation',array('name'=>'Glaucoma surgery','display_order'=>3));
		$this->insert('common_previous_operation',array('name'=>'Retinal laser','display_order'=>4));
		$this->insert('common_previous_operation',array('name'=>'Retinal surgery','display_order'=>5));
		$this->insert('common_previous_operation',array('name'=>'Squint surgery','display_order'=>6));
	}

	public function down()
	{
		$this->dropTable('common_previous_operation');
		$this->dropTable('previous_operation');
	}
}
