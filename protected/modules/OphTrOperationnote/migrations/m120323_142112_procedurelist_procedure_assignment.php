<?php

class m120323_142112_procedurelist_procedure_assignment extends CDbMigration
{
	public function up()
	{
		$this->createTable('et_ophtroperationnote_procedurelist_procedure_assignment',
			array('procedurelist_id' => 'int(10) unsigned NOT NULL',
				'proc_id' => 'int(10) unsigned NOT NULL',
				'display_order' => 'tinyint(3) unsigned DEFAULT \'0\'',
				'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'last_modified_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'created_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'PRIMARY KEY (`procedurelist_id`,`proc_id`)',
				'KEY `procedurelist_id` (`procedurelist_id`)',
				'KEY `procedure_id` (`proc_id`)',
				'KEY `et_ophtroperationnote_plpa_last_modified_user_id_fk` (`last_modified_user_id`)',
				'KEY `et_ophtroperationnote_plpa_created_user_id_fk` (`created_user_id`)',
				'CONSTRAINT `et_ophtroperationnote_plpa_operation_fk` FOREIGN KEY (`procedurelist_id`) REFERENCES `element_operation` (`id`)',
				'CONSTRAINT `et_ophtroperationnote_plpa_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `et_ophtroperationnote_plpa_ibfk_1` FOREIGN KEY (`proc_id`) REFERENCES `proc` (`id`)',
				'CONSTRAINT `et_ophtroperationnote_plpa_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)'
			), 'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin'
		);
	}

	public function down()
	{
		$this->dropTable('et_ophtroperationnote_procedurelist_procedure_assignment');
	}
}
