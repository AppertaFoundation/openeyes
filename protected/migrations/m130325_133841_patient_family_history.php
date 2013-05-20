<?php

class m130325_133841_patient_family_history extends CDbMigration
{
	public function up()
	{
		$this->createTable('family_history_relative',array(
				'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
				'name' => 'varchar(64) COLLATE utf8_bin NOT NULL',
				'display_order' => 'tinyint(1) unsigned NOT NULL',
				'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'last_modified_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'created_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'PRIMARY KEY (`id`)',
				'KEY `family_history_relative_last_modified_user_id_fk` (`last_modified_user_id`)',
				'KEY `family_history_relative_created_user_id_fk` (`created_user_id`)',
				'CONSTRAINT `family_history_relative_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `family_history_relative_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)',
			),
			'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin'
		);

		$this->insert('family_history_relative',array('name'=>'Mother','display_order'=>1));
		$this->insert('family_history_relative',array('name'=>'Father','display_order'=>2));
		$this->insert('family_history_relative',array('name'=>'Brother','display_order'=>3));
		$this->insert('family_history_relative',array('name'=>'Sister','display_order'=>4));
		$this->insert('family_history_relative',array('name'=>'Uncle','display_order'=>5));
		$this->insert('family_history_relative',array('name'=>'Aunt','display_order'=>6));
		$this->insert('family_history_relative',array('name'=>'Cousin','display_order'=>7));
		$this->insert('family_history_relative',array('name'=>'Grandmother','display_order'=>8));
		$this->insert('family_history_relative',array('name'=>'Grandfather','display_order'=>9));
		$this->insert('family_history_relative',array('name'=>'Other','display_order'=>10));

		$this->createTable('family_history_side',array(
				'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
				'name' => 'varchar(64) COLLATE utf8_bin NOT NULL',
				'display_order' => 'tinyint(1) unsigned NOT NULL',
				'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'last_modified_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'created_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'PRIMARY KEY (`id`)',
				'KEY `family_history_side_last_modified_user_id_fk` (`last_modified_user_id`)',
				'KEY `family_history_side_created_user_id_fk` (`created_user_id`)',
				'CONSTRAINT `family_history_side_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `family_history_side_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)',
			),
			'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin'
		);

		$this->insert('family_history_side',array('name'=>'Maternal','display_order'=>1));
		$this->insert('family_history_side',array('name'=>'Paternal','display_order'=>2));
		$this->insert('family_history_side',array('name'=>'Unknown','display_order'=>3));

		$this->createTable('family_history_condition',array(
				'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
				'name' => 'varchar(64) COLLATE utf8_bin NOT NULL',
				'display_order' => 'tinyint(1) unsigned NOT NULL',
				'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'last_modified_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'created_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'PRIMARY KEY (`id`)',
				'KEY `family_history_condition_last_modified_user_id_fk` (`last_modified_user_id`)',
				'KEY `family_history_condition_created_user_id_fk` (`created_user_id`)',
				'CONSTRAINT `family_history_condition_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `family_history_condition_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)',
			),
			'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin'
		);

		$this->insert('family_history_condition',array('name'=>'Cataract','display_order'=>1));
		$this->insert('family_history_condition',array('name'=>'Diabetes','display_order'=>2));
		$this->insert('family_history_condition',array('name'=>'Glaucoma','display_order'=>3));
		$this->insert('family_history_condition',array('name'=>'Maculopathy','display_order'=>4));
		$this->insert('family_history_condition',array('name'=>'Retinal detachment','display_order'=>5));
		$this->insert('family_history_condition',array('name'=>'Retinitis Pigmentosa','display_order'=>6));
		$this->insert('family_history_condition',array('name'=>'Other','display_order'=>7));

		$this->createTable('family_history',array(
				'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
				'patient_id' => 'int(10) unsigned NOT NULL',
				'relative_id' => 'int(10) unsigned NOT NULL',
				'side_id' => 'int(10) unsigned NOT NULL',
				'condition_id' => 'int(10) unsigned NOT NULL',
				'comments' => 'varchar(1024) COLLATE utf8_bin NOT NULL',
				'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'last_modified_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'created_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'PRIMARY KEY (`id`)',
				'KEY `family_history_last_modified_user_id_fk` (`last_modified_user_id`)',
				'KEY `family_history_created_user_id_fk` (`created_user_id`)',
				'KEY `family_history_patient_id_fk` (`patient_id`)',
				'KEY `family_history_relative_id_fk` (`relative_id`)',
				'KEY `family_history_side_id_fk` (`side_id`)',
				'KEY `family_history_condition_id_fk` (`condition_id`)',
				'CONSTRAINT `family_history_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `family_history_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `family_history_patient_id_fk` FOREIGN KEY (`patient_id`) REFERENCES `patient` (`id`)',
				'CONSTRAINT `family_history_relative_id_fk` FOREIGN KEY (`relative_id`) REFERENCES `family_history_relative` (`id`)',
				'CONSTRAINT `family_history_side_id_fk` FOREIGN KEY (`side_id`) REFERENCES `family_history_side` (`id`)',
				'CONSTRAINT `family_history_condition_id_fk` FOREIGN KEY (`condition_id`) REFERENCES `family_history_condition` (`id`)',
			),
			'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin'
		);
	}

	public function down()
	{
		$this->dropTable('family_history');
		$this->dropTable('family_history_condition');
		$this->dropTable('family_history_side');
		$this->dropTable('family_history_relative');
	}
}
