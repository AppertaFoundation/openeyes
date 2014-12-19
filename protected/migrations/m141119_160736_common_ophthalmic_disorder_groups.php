<?php

class m141119_160736_common_ophthalmic_disorder_groups extends OEMigration
{
	public function up()
	{
		$this->createTable('common_ophthalmic_disorder_group', array(
				'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
				'name' => 'varchar(64) not null',
				'display_order' => 'tinyint(1) unsigned not null',
				'deleted' => 'tinyint(1) unsigned not null',
				'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
				'last_modified_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
				'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
				'created_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
				'PRIMARY KEY (`id`)',
				'KEY `common_ophthalmic_disorder_group_lmui_fk` (`last_modified_user_id`)',
				'KEY `common_ophthalmic_disorder_group_cui_fk` (`created_user_id`)',
				'CONSTRAINT `common_ophthalmic_disorder_group_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `common_ophthalmic_disorder_group_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
			), 'ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci');

		$this->versionExistingTable('common_ophthalmic_disorder_group');

		$this->addColumn('common_ophthalmic_disorder','group_id','int(10) unsigned null');
		$this->addColumn('common_ophthalmic_disorder_version','group_id','int(10) unsigned null');
		$this->createIndex('common_ophthalmic_disorder_group_id_fk','common_ophthalmic_disorder','group_id');
		$this->addForeignKey('common_ophthalmic_disorder_group_id_fk','common_ophthalmic_disorder','group_id','common_ophthalmic_disorder_group','id');
	}

	public function down()
	{
		$this->dropForeignKey('common_ophthalmic_disorder_group_id_fk','common_ophthalmic_disorder');
		$this->dropIndex('common_ophthalmic_disorder_group_id_fk','common_ophthalmic_disorder');
		$this->dropColumn('common_ophthalmic_disorder','group_id');
		$this->dropColumn('common_ophthalmic_disorder_version','group_id');

		$this->dropTable('common_ophthalmic_disorder_group_version');
		$this->dropTable('common_ophthalmic_disorder_group');
	}
}
