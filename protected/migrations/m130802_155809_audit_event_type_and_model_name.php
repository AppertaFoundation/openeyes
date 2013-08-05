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

		$this->addColumn('audit','event_type_id','int(10) unsigned NULL');
		$this->createIndex('audit_event_type_id_fk','audit','event_type_id');
		$this->addForeignKey('audit_event_type_id_fk','audit','event_type_id','event_type','id');

		$this->addColumn('audit','model_id','int(10) unsigned NULL');
		$this->createIndex('audit_model_id_fk','audit','model_id');
		$this->addForeignKey('audit_model_id_fk','audit','model_id','audit_model','id');

		$this->addColumn('audit','module_id','int(10) unsigned NULL');
		$this->createIndex('audit_module_id_fk','audit','module_id');
		$this->addForeignKey('audit_module_id_fk','audit','module_id','audit_module','id');
	}

	public function down()
	{
		$this->dropForeignKey('audit_event_type_id_fk','audit');
		$this->dropIndex('audit_event_type_id_fk','audit');
		$this->dropColumn('audit','event_type_id');

		$this->dropForeignKey('audit_model_id_fk','audit');
		$this->dropIndex('audit_model_id_fk','audit');
		$this->dropColumn('audit','model_id');

		$this->dropForeignKey('audit_module_id_fk','audit');
		$this->dropIndex('audit_module_id_fk','audit');
		$this->dropColumn('audit','module_id');

		$this->dropTable('audit_model');
		$this->dropTable('audit_module');
	}
}
