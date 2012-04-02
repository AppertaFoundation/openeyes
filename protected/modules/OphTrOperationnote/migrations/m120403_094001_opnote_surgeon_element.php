<?php

class m120403_094001_opnote_surgeon_element extends CDbMigration
{
	public function up()
	{
		$this->createTable('et_ophtroperationnote_surgeon',array(
				'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
				'event_id' => 'int(10) unsigned NOT NULL',
				'surgeon_id' => 'int(10) unsigned NOT NULL',
				'assistant_id' => 'int(10) unsigned DEFAULT NULL',
				'supervising_surgeon_id' => 'int(10) unsigned DEFAULT NULL',
				'display_order' => 'tinyint(3) unsigned NOT NULL',
				'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'last_modified_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'created_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'PRIMARY KEY (`id`)',
				'KEY `et_ophtroperationnote_sur_type_last_modified_user_id_fk` (`last_modified_user_id`)',
				'KEY `et_ophtroperationnote_sur_type_created_user_id_fk` (`created_user_id`)',
				'KEY `et_ophtroperationnote_sur_surgeon_id_fk` (`surgeon_id`)',
				'CONSTRAINT `et_ophtroperationnote_sur_type_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `et_ophtroperationnote_sur_type_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `et_ophtroperationnote_sur_surgeon_id_fk` FOREIGN KEY (`surgeon_id`) REFERENCES `consultant` (`id`)',
			),
			'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin'
		);

		$this->dropForeignKey('et_ophtroperationnote_procedurelist_surgeon_id_fk','et_ophtroperationnote_procedurelist');
		$this->dropIndex('et_ophtroperationnote_procedurelist_surgeon_id_fk','et_ophtroperationnote_procedurelist');
		$this->dropColumn('et_ophtroperationnote_procedurelist','surgeon_id');
		$this->dropColumn('et_ophtroperationnote_procedurelist','assistant_id');
		$this->dropColumn('et_ophtroperationnote_procedurelist','supervising_surgeon_id');

		$event_type = $this->dbConnection->createCommand()->select('id')->from('event_type')->where('name=:name', array(':name'=>'Operation note'))->queryRow();
		$this->insert('element_type', array('name' => 'Surgeon', 'class_name' => 'ElementSurgeon', 'event_type_id' => $event_type['id'], 'display_order' => 8, 'default' => 1));
	}

	public function down()
	{
		$event_type = $this->dbConnection->createCommand()->select('id')->from('event_type')->where('name=:name', array(':name'=>'Operation note'))->queryRow();
		$element_type = $this->dbConnection->createCommand()->select('id')->from('element_type')->where('event_type_id = :event_type_id and class_name=:class_name',array(':event_type_id' => $event_type['id'], ':class_name'=>'ElementSurgeon'))->queryRow();
		$this->delete('element_type','id='.$element_type['id']);

		$this->addColumn('et_ophtroperationnote_procedurelist','assistant_id','int(10) unsigned DEFAULT NULL');
		$this->addColumn('et_ophtroperationnote_procedurelist','supervising_surgeon_id','int(10) unsigned DEFAULT NULL');
		$this->addColumn('et_ophtroperationnote_procedurelist','surgeon_id','int(10) unsigned NOT NULL');
		$this->createIndex('et_ophtroperationnote_procedurelist_surgeon_id_fk','et_ophtroperationnote_procedurelist','surgeon_id');
		$this->addForeignKey('et_ophtroperationnote_procedurelist_surgeon_id_fk','et_ophtroperationnote_procedurelist','surgeon_id','consultant','id');

		$this->dropTable('et_ophtroperationnote_surgeon');
	}
}
