<?php

class m120921_080410_erod_table extends CDbMigration
{
	public function up()
	{
		$this->createTable('element_operation_erod',array(
				'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
				'element_operation_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'session_id' => 'int(10) unsigned NOT NULL',
				'session_date' => 'date NOT NULL',
				'session_start_time' => 'time NOT NULL',
				'session_end_time' => 'time NOT NULL',
				'firm_id' => 'int(10) unsigned NOT NULL',
				'consultant' => 'tinyint(1) unsigned NOT NULL',
				'paediatric' => 'tinyint(1) unsigned NOT NULL',
				'anaesthetist' => 'tinyint(1) unsigned NOT NULL',
				'general_anaesthetic' => 'tinyint(1) unsigned NOT NULL',
				'session_duration' => 'int(10) unsigned NOT NULL',
				'total_operations_time' => 'int(10) unsigned NOT NULL',
				'available_time' => 'int(10) unsigned NOT NULL',
				'last_modified_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'created_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'PRIMARY KEY (`id`)',
				'KEY `element_operation_erod_last_modified_user_id_fk` (`last_modified_user_id`)',
				'KEY `element_operation_erod_created_user_id_fk` (`created_user_id`)',
				'KEY `element_operation_erod_element_operation_id_fk` (`element_operation_id`)',
				'KEY `element_operation_erod_session_id_fk` (`session_id`)',
				'KEY `element_operation_erod_firm_id_fk` (`firm_id`)',
				'CONSTRAINT `element_operation_erod_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `element_operation_erod_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `element_operation_erod_element_operation_id_fk` FOREIGN KEY (`element_operation_id`) REFERENCES `element_operation` (`id`)',
				'CONSTRAINT `element_operation_erod_session_id_fk` FOREIGN KEY (`session_id`) REFERENCES `session` (`id`)',
				'CONSTRAINT `element_operation_erod_firm_id_fk` FOREIGN KEY (`firm_id`) REFERENCES `firm` (`id`)'
			), 'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin'
		);
	}

	public function down()
	{
		$this->dropTable('element_operation_erod');
	}
}
