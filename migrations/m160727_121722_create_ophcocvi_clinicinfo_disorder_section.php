<?php

class m160727_121722_create_ophcocvi_clinicinfo_disorder_section extends CDbMigration
{
	public function up()
	{
            $this->createTable('ophcocvi_clinicinfo_disorder_section', array(
			'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
			'name' => 'varchar(128) NOT NULL',
                        'comments_allowed' => 'tinyint(1) unsigned NOT NULL DEFAULT 1',
                        'comments_label' => 'varchar(128) NOT NULL',
                        'display_order' => 'int(10) unsigned NOT NULL DEFAULT 1',
                        'active' => 'tinyint(1) unsigned NOT NULL DEFAULT 1',
                        'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
			'last_modified_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
			'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
			'created_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
			'deleted' => 'tinyint(1) unsigned not null',
			'PRIMARY KEY (`id`)',
			'KEY `ophcocvi_clinicinfo_disorder_section_lmui_fk` (`last_modified_user_id`)',
			'KEY `ophcocvi_clinicinfo_disorder_section_cui_fk` (`created_user_id`)',
			'CONSTRAINT `ophcocvi_clinicinfo_disorder_section_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)',
			'CONSTRAINT `ophcocvi_clinicinfo_disorder_section_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
		), 'ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci');

		$this->createTable('ophcocvi_clinicinfo_disorder_section_version', array(
			'id' => 'int(10) unsigned NOT NULL',
			'name' => 'varchar(128) NOT NULL',
                        'comments_allowed' => 'tinyint(1) unsigned NOT NULL DEFAULT 1',
                        'comments_label' => 'varchar(128) NOT NULL',
			'display_order' => 'int(10) unsigned NOT NULL DEFAULT 1',
                        'active' => 'tinyint(1) unsigned NOT NULL DEFAULT 1',
                        'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
			'last_modified_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
			'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
			'created_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
			'deleted' => 'tinyint(1) unsigned not null',
			'version_date' => "datetime NOT NULL DEFAULT '1900-01-01 00:00:00'",
			'version_id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
			'PRIMARY KEY (`version_id`)',
			'KEY `acv_ophcocvi_clinicinfo_disorder_section_lmui_fk` (`last_modified_user_id`)',
			'KEY `acv_ophcocvi_clinicinfo_disorder_section_cui_fk` (`created_user_id`)',
			'KEY `ophcocvi_clinicinfo_disorder_section_aid_fk` (`id`)',
			'CONSTRAINT `acv_ophcocvi_clinicinfo_disorder_section_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)',
			'CONSTRAINT `acv_ophcocvi_clinicinfo_disorder_section_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
			'CONSTRAINT `ophcocvi_clinicinfo_disorder_section_aid_fk` FOREIGN KEY (`id`) REFERENCES `ophcocvi_clinicinfo_disorder_section` (`id`)',
		), 'ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci');
                
               
	}

	public function down()
	{
		$this->dropTable('ophcocvi_clinicinfo_disorder_section_version');
		$this->dropTable('ophcocvi_clinicinfo_disorder_section');
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