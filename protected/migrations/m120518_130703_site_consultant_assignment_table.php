<?php

class m120518_130703_site_consultant_assignment_table extends CDbMigration
{
	public function up()
	{
		$this->createTable('site_consultant_assignment',array(
				'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
				'site_id' => 'int(10) unsigned NOT NULL',
				'consultant_id' => 'int(10) unsigned NOT NULL',
				'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'last_modified_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'created_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'PRIMARY KEY (`id`)',
				'KEY `site_consultant_assignment_site_id_fk` (`site_id`)',
				'KEY `site_consultant_assignment_consultant_id_fk` (`consultant_id`)',
				'KEY `site_consultant_assignment_last_modified_user_id_fk` (`last_modified_user_id`)',
				'KEY `site_consultant_assignment_created_user_id_fk` (`created_user_id`)',
				'CONSTRAINT `site_consultant_assignment_site_id_fk` FOREIGN KEY (`site_id`) REFERENCES `site` (`id`)',
				'CONSTRAINT `site_consultant_assignment_consultant_id_fk` FOREIGN KEY (`consultant_id`) REFERENCES `consultant` (`id`)',
				'CONSTRAINT `site_consultant_assignment_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `site_consultant_assignment_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)'
			),
			'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin'
		);
	}

	public function down()
	{
		$this->dropTable('site_consultant_assignment');
	}
}
