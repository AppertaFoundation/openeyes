<?php

class m120523_102017_new_audit_table extends CDbMigration
{
	public function up()
	{
		$this->createTable('audit',array(
			'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
			'action' => "varchar(20) NOT NULL DEFAULT ''",
			'target_type' => "varchar(20) NOT NULL DEFAULT ''",
			'patient_id' => "int(10) unsigned DEFAULT NULL",
			'episode_id' => "int(10) unsigned DEFAULT NULL",
			'event_id' => "int(10) unsigned DEFAULT NULL",
			'user_id' => "int(10) unsigned DEFAULT NULL",
			'data' => "text",
			'remote_addr' => "varchar(255) DEFAULT ''",
			'http_user_agent' => "varchar(255) DEFAULT ''",
			'server_name' => "varchar(255) DEFAULT ''",
			'request_uri' => "varchar(255) DEFAULT ''",
			'site_id' => "int(10) unsigned DEFAULT NULL",
			'firm_id' => "int(10) unsigned DEFAULT NULL",
			'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
			'last_modified_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
			'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
			'created_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
			'PRIMARY KEY (`id`)',
			'CONSTRAINT `audit_patient_id_fk` FOREIGN KEY (`patient_id`) REFERENCES `patient` (`id`)',
			'CONSTRAINT `audit_episode_id_fk` FOREIGN KEY (`episode_id`) REFERENCES `episode` (`id`)',
			'CONSTRAINT `audit_event_id_fk` FOREIGN KEY (`event_id`) REFERENCES `event` (`id`)',
			'CONSTRAINT `audit_user_id_fk` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`)',
			'CONSTRAINT `audit_site_id_fk` FOREIGN KEY (`site_id`) REFERENCES `site` (`id`)',
			'CONSTRAINT `audit_firm_id_fk` FOREIGN KEY (`firm_id`) REFERENCES `firm` (`id`)',
		), 'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin');
	}

	public function down()
	{
		$this->dropForeignKey('audit_patient_id_fk', 'patient_id');
		$this->dropForeignKey('audit_episode_id_fk', 'episode_id');
		$this->dropForeignKey('audit_event_id_fk', 'event_id');
		$this->dropForeignKey('audit_user_id_fk', 'user_id');
		$this->dropForeignKey('audit_site_id_fk', 'site_id');
		$this->dropForeignKey('audit_firm_id_fk', 'firm_id');
		$this->dropTable('audit');
		return true;
	}

	/*
	// Use safeUp/safeDown to do migration with transaction
	public function safeUp()
	{
	}

	public function safeDown()
	{
	}
	*/
}
