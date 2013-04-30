<?php

class m130429_133030_site_institution_import_tables extends CDbMigration
{
	public function up()
	{
		$this->createTable('import_source', array(
				'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
				'name' => 'varchar(64) CHARACTER SET utf8 NOT NULL',
				'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'created_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'last_modified_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'PRIMARY KEY (`id`)',
				'KEY `import_source_created_user_id_fk` (`created_user_id`)',
				'KEY `import_source_last_modified_user_id_fk` (`last_modified_user_id`)',
				'CONSTRAINT `import_source_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `import_source_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)'
			), 'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin');

		$this->insert('import_source',array('id'=>1,'name'=>'Connecting for Health'));
		$this->insert('import_source',array('id'=>2,'name'=>'Dr Foster Health'));

		$this->addColumn('site','source_id','int(10) unsigned NOT NULL');
		$this->addColumn('institution','source_id','int(10) unsigned NOT NULL');

		$this->update('site',array('source_id'=>1));
		$this->update('institution',array('source_id'=>1));

		$this->createIndex('site_source_id_fk','site','source_id');
		$this->addForeignKey('site_source_id_fk','site','source_id','import_source','id');
		$this->createIndex('institution_source_id_fk','institution','source_id');
		$this->addForeignKey('institution_source_id_fk','institution','source_id','import_source','id');

		$this->renameColumn('site','code','remote_id');
		$this->renameColumn('institution','code','remote_id');

		$this->alterColumn('site','remote_id','varchar(10) COLLATE utf8_bin NOT NULL');
		$this->alterColumn('institution','remote_id','varchar(10) COLLATE utf8_bin NOT NULL');
	}

	public function down()
	{
		$this->alterColumn('institution','remote_id','varchar(5) COLLATE utf8_bin NOT NULL');
		$this->alterColumn('site','remote_id','varchar(2) COLLATE utf8_bin NOT NULL');

		$this->renameColumn('institution','remote_id','code');
		$this->renameColumn('site','remote_id','code');

		$this->dropForeignKey('institution_source_id_fk','institution');
		$this->dropIndex('institution_source_id_fk','institution');
		$this->dropForeignKey('site_source_id_fk','site');
		$this->dropIndex('site_source_id_fk','site');
		$this->dropColumn('institution','source_id');
		$this->dropColumn('site','source_id');

		$this->dropTable('import_source');
	}
}
