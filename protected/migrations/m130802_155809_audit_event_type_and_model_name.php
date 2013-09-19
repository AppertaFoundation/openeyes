<?php

class m130802_155809_audit_event_type_and_model_name extends CDbMigration
{
	public function up()
	{
		$this->createTable('audit_model', array(
				'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
				'name' => 'varchar(64) COLLATE utf8_bin NOT NULL',
				'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
				'last_modified_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
				'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
				'created_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
				'PRIMARY KEY (`id`)',
				'KEY `audit_model_lmui_fk` (`last_modified_user_id`)',
				'KEY `audit_model_cui_fk` (`created_user_id`)',
				'CONSTRAINT `audit_model_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `audit_model_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
			), 'ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin');

		$this->createTable('audit_module', array(
				'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
				'name' => 'varchar(64) COLLATE utf8_bin NOT NULL',
				'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
				'last_modified_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
				'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
				'created_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
				'PRIMARY KEY (`id`)',
				'KEY `audit_module_lmui_fk` (`last_modified_user_id`)',
				'KEY `audit_module_cui_fk` (`created_user_id`)',
				'CONSTRAINT `audit_module_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `audit_module_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
			), 'ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin');
		$this->execute('ALTER TABLE audit
			ADD COLUMN event_type_id int(10) unsigned DEFAULT NULL,
			ADD COLUMN model_id int(10) unsigned DEFAULT NULL,
			ADD COLUMN module_id int(10) unsigned DEFAULT NULL,
			ADD INDEX audit_event_type_id_fk (event_type_id),
			ADD INDEX audit_model_id_fk (model_id),
			ADD INDEX audit_module_id_fk (module_id),
			ADD FOREIGN KEY audit_event_type_id_fk (event_type_id) REFERENCES event_type (id),
			ADD FOREIGN KEY audit_model_id_fk (model_id) REFERENCES audit_model (id),
			ADD FOREIGN KEY audit_module_id_fk (module_id) REFERENCES audit_module (id);');
	}

	public function down()
	{
		$this->execute('ALTER TABLE audit
			DROP COLUMN event_type_id,
			DROP COLUMN model_id,
			DROP COLUMN module_id,
			DROP INDEX audit_event_type_id_fk,
			DROP INDEX audit_model_id_fk,
			DROP INDEX audit_module_id_fk,
			DROP FOREIGN KEY audit_event_type_id_fk,
			DROP FOREIGN KEY audit_model_id_fk,
			DROP FOREIGN KEY audit_module_id_fk;');

		$this->dropTable('audit_model');
		$this->dropTable('audit_module');
	}
}
