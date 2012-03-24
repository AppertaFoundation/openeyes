<?php

class m120324_131041_tamponade_percentage_and_volume_values extends CDbMigration
{
	public function up()
	{
		$this->createTable('et_ophtroperationnote_gas_percentage',array(
				'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
				'value' => 'tinyint(1) unsigned NOT NULL DEFAULT 0',
				'display_order' => 'tinyint(3) unsigned NOT NULL',
				'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'last_modified_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'created_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'PRIMARY KEY (`id`)',
				'KEY `et_ophtroperationnote_gas_pc_last_modified_user_id_fk` (`last_modified_user_id`)',
				'KEY `et_ophtroperationnote_gas_pc_created_user_id_fk` (`created_user_id`)',
				'CONSTRAINT `et_ophtroperationnote_gas_pc_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `et_ophtroperationnote_gas_pc_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)'
			),
			'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin'
		);

		$this->insert('et_ophtroperationnote_gas_percentage',array('id'=>1,'value'=>14,'display_order'=>1));
		$this->insert('et_ophtroperationnote_gas_percentage',array('id'=>2,'value'=>16,'display_order'=>2));
		$this->insert('et_ophtroperationnote_gas_percentage',array('id'=>3,'value'=>20,'display_order'=>3));
		$this->insert('et_ophtroperationnote_gas_percentage',array('id'=>4,'value'=>30,'display_order'=>4));
		$this->insert('et_ophtroperationnote_gas_percentage',array('id'=>5,'value'=>100,'display_order'=>5));

		$this->createTable('et_ophtroperationnote_gas_volume',array(
				'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
				'value' => 'varchar(3) COLLATE utf8_bin NOT NULL',
				'display_order' => 'tinyint(3) unsigned NOT NULL',
				'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'last_modified_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'created_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'PRIMARY KEY (`id`)',
				'KEY `et_ophtroperationnote_gas_vol_last_modified_user_id_fk` (`last_modified_user_id`)',
				'KEY `et_ophtroperationnote_gas_vol_created_user_id_fk` (`created_user_id`)',
				'CONSTRAINT `et_ophtroperationnote_gas_vol_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `et_ophtroperationnote_gas_vol_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)'
			),
			'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin'
		);

		$this->insert('et_ophtroperationnote_gas_volume',array('id'=>1,'value'=>'0.1','display_order'=>1));
		$this->insert('et_ophtroperationnote_gas_volume',array('id'=>2,'value'=>'0.2','display_order'=>2));
		$this->insert('et_ophtroperationnote_gas_volume',array('id'=>3,'value'=>'0.3','display_order'=>3));
		$this->insert('et_ophtroperationnote_gas_volume',array('id'=>4,'value'=>'0.4','display_order'=>4));

		$this->renameColumn('et_ophtroperationnote_tamponade','percentage','gas_percentage_id');
		$this->renameColumn('et_ophtroperationnote_tamponade','volume','gas_volume_id');
		$this->alterColumn('et_ophtroperationnote_tamponade','gas_volume_id','int(10) unsigned NOT NULL DEFAULT 1');
		$this->createIndex('et_ophtroperationnote_tamponade_pc_id','et_ophtroperationnote_tamponade','gas_percentage_id');

		$this->addForeignKey('et_ophtroperationnote_tamponade_pc_id','et_ophtroperationnote_tamponade','gas_percentage_id','et_ophtroperationnote_gas_percentage','id');
		$this->createIndex('et_ophtroperationnote_tamponade_gv_id','et_ophtroperationnote_tamponade','gas_volume_id');
		$this->addForeignKey('et_ophtroperationnote_tamponade_gv_id','et_ophtroperationnote_tamponade','gas_volume_id','et_ophtroperationnote_gas_volume','id');
	}

	public function down()
	{
		$this->dropForeignKey('et_ophtroperationnote_tamponade_gv_id','et_ophtroperationnote_tamponade');
		$this->dropIndex('et_ophtroperationnote_tamponade_gv_id','et_ophtroperationnote_tamponade');
		$this->dropForeignKey('et_ophtroperationnote_tamponade_pc_id','et_ophtroperationnote_tamponade');
		$this->dropIndex('et_ophtroperationnote_tamponade_pc_id','et_ophtroperationnote_tamponade');

		$this->alterColumn('et_ophtroperationnote_tamponade','gas_volume_id','int(10) unsigned NOT NULL DEFAULT 0');
		$this->renameColumn('et_ophtroperationnote_tamponade','gas_volume_id','volume');
		$this->renameColumn('et_ophtroperationnote_tamponade','gas_percentage_id','percentage');

		$this->dropTable('et_ophtroperationnote_gas_volume');
		$this->dropTable('et_ophtroperationnote_gas_percentage');
	}
}
