<?php

class m120411_173646_anaesthetic_complication_table_changes extends CDbMigration
{
	public function up()
	{
		$this->dropForeignKey('et_ophtroperationnote_pac_anaesthetic_complication_id_fk','et_ophtroperationnote_anaesthetic_anaesthetic_complication');
		$this->dropIndex('et_ophtroperationnote_pac_anaesthetic_complication_id_fk','et_ophtroperationnote_anaesthetic_anaesthetic_complication');
		$this->dropColumn('et_ophtroperationnote_anaesthetic_anaesthetic_complication','anaesthetic_complication_id');

		$this->dropForeignKey('et_ophtroperationnote_pac_procedurelist_id_fk','et_ophtroperationnote_anaesthetic_anaesthetic_complication');
		$this->dropIndex('et_ophtroperationnote_pac_procedurelist_id_fk','et_ophtroperationnote_anaesthetic_anaesthetic_complication');
		$this->dropColumn('et_ophtroperationnote_anaesthetic_anaesthetic_complication','procedurelist_id');

		$this->addColumn('et_ophtroperationnote_anaesthetic_anaesthetic_complication','et_ophtroperationnote_anaesthetic_id','int(10) unsigned NOT NULL');
		$this->createIndex('et_ophtroperationnote_anaesthetic_ac_anaesthetic_id_fk','et_ophtroperationnote_anaesthetic_anaesthetic_complication','et_ophtroperationnote_anaesthetic_id');
		$this->addForeignKey('et_ophtroperationnote_anaesthetic_ac_anaesthetic_id_fk','et_ophtroperationnote_anaesthetic_anaesthetic_complication','et_ophtroperationnote_anaesthetic_id','et_ophtroperationnote_anaesthetic','id');

		$this->createTable('et_ophtroperationnote_anaesthetic_anaesthetic_complications',array(
				'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
				'name' => 'varchar(64) COLLATE utf8_bin NOT NULL',
				'display_order' => 'tinyint(3) unsigned NOT NULL',
				'last_modified_user_id' => "int(10) unsigned NOT NULL DEFAULT '1'",
				'last_modified_date' => "datetime NOT NULL DEFAULT '1900-01-01 00:00:00'",
				'created_user_id' => "int(10) unsigned NOT NULL DEFAULT '1'",
				'created_date' => "datetime NOT NULL DEFAULT '1900-01-01 00:00:00'",
				'PRIMARY KEY (`id`)',
				'KEY `et_ophtroperationnote_anaesthetic_ac_last_modified_user_id_fk` (`last_modified_user_id`)',
				'KEY `et_ophtroperationnote_anaesthetic_ac_created_user_id_fk` (`created_user_id`)',
				'CONSTRAINT `et_ophtroperationnote_anaesthetic_ac_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `et_ophtroperationnote_anaesthetic_ac_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
			),
			'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin'
		);

		$this->insert('et_ophtroperationnote_anaesthetic_anaesthetic_complications',array('name'=>'Eyelid haemorrage/bruising','display_order'=>1));
		$this->insert('et_ophtroperationnote_anaesthetic_anaesthetic_complications',array('name'=>'Conjunctivital chemosis','display_order'=>2));
		$this->insert('et_ophtroperationnote_anaesthetic_anaesthetic_complications',array('name'=>'Retro bulbar / peribulbar haemorrage','display_order'=>3));
		$this->insert('et_ophtroperationnote_anaesthetic_anaesthetic_complications',array('name'=>'Globe/optic nerve penetration','display_order'=>4));
		$this->insert('et_ophtroperationnote_anaesthetic_anaesthetic_complications',array('name'=>'Inadequate akinesia','display_order'=>5));
		$this->insert('et_ophtroperationnote_anaesthetic_anaesthetic_complications',array('name'=>'Patient pain - Mild','display_order'=>6));
		$this->insert('et_ophtroperationnote_anaesthetic_anaesthetic_complications',array('name'=>'Patient pain - Moderate','display_order'=>7));
		$this->insert('et_ophtroperationnote_anaesthetic_anaesthetic_complications',array('name'=>'Patient pain - Severe','display_order'=>8));
		$this->insert('et_ophtroperationnote_anaesthetic_anaesthetic_complications',array('name'=>'Systemic problems','display_order'=>9));
		$this->insert('et_ophtroperationnote_anaesthetic_anaesthetic_complications',array('name'=>'Operation cancelled due to complication','display_order'=>10));

		$this->addColumn('et_ophtroperationnote_anaesthetic_anaesthetic_complication','anaesthetic_complication_id','int(10) unsigned NOT NULL');
		$this->createIndex('et_ophtroperationnote_anaesthetic_aca_complication_id_fk','et_ophtroperationnote_anaesthetic_anaesthetic_complication','anaesthetic_complication_id');
		$this->addForeignKey('et_ophtroperationnote_anaesthetic_aca_complication_id_fk','et_ophtroperationnote_anaesthetic_anaesthetic_complication','anaesthetic_complication_id','et_ophtroperationnote_anaesthetic_anaesthetic_complications','id');
	}

	public function down()
	{
		$this->dropForeignKey('et_ophtroperationnote_anaesthetic_aca_complication_id_fk','et_ophtroperationnote_anaesthetic_anaesthetic_complication');
		$this->dropIndex('et_ophtroperationnote_anaesthetic_aca_complication_id_fk','et_ophtroperationnote_anaesthetic_anaesthetic_complication');
		$this->dropColumn('et_ophtroperationnote_anaesthetic_anaesthetic_complication','anaesthetic_complication_id');

		$this->dropTable('et_ophtroperationnote_anaesthetic_anaesthetic_complications');

		$this->dropForeignKey('et_ophtroperationnote_anaesthetic_ac_anaesthetic_id_fk','et_ophtroperationnote_anaesthetic_anaesthetic_complication');
		$this->dropIndex('et_ophtroperationnote_anaesthetic_ac_anaesthetic_id_fk','et_ophtroperationnote_anaesthetic_anaesthetic_complication');
		$this->dropColumn('et_ophtroperationnote_anaesthetic_anaesthetic_complication','et_ophtroperationnote_anaesthetic_id');

		$this->addColumn('et_ophtroperationnote_anaesthetic_anaesthetic_complication','procedurelist_id','int(10) unsigned NOT NULL');
		$this->createIndex('et_ophtroperationnote_pac_procedurelist_id_fk','et_ophtroperationnote_anaesthetic_anaesthetic_complication','procedurelist_id');
		$this->addForeignKey('et_ophtroperationnote_pac_procedurelist_id_fk','et_ophtroperationnote_anaesthetic_anaesthetic_complication','procedurelist_id','et_ophtroperationnote_procedurelist','id');

		$this->addColumn('et_ophtroperationnote_anaesthetic_anaesthetic_complication','anaesthetic_complication_id','int(10) unsigned NOT NULL');
		$this->createIndex('et_ophtroperationnote_pac_anaesthetic_complication_id_fk','et_ophtroperationnote_anaesthetic_anaesthetic_complication','anaesthetic_complication_id');
		$this->addForeignKey('et_ophtroperationnote_pac_anaesthetic_complication_id_fk','et_ophtroperationnote_anaesthetic_anaesthetic_complication','anaesthetic_complication_id','anaesthetic_complication','id');
	}
}
