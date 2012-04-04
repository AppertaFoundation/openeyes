<?php

class m120403_163428_move_cataract_complications_into_a_table extends CDbMigration
{
	public function up()
	{
		$this->createTable('et_ophtroperationnote_cataract_complications',array(
				'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
				'name' => 'varchar(64) COLLATE utf8_bin NOT NULL',
				'display_order' => 'tinyint(3) unsigned NOT NULL',
				'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'last_modified_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'created_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'PRIMARY KEY (`id`)',
				'KEY `et_ophtroperationnote_cc_last_modified_user_id_fk` (`last_modified_user_id`)',
				'KEY `et_ophtroperationnote_cc_created_user_id_fk` (`created_user_id`)',
				'CONSTRAINT `et_ophtroperationnote_cc_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `et_ophtroperationnote_cc_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)',
			),
			'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin'
		);

		$this->createTable('et_ophtroperationnote_cataract_complication',array(
				'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
				'cataract_id' => 'int(10) unsigned NOT NULL',
				'complication_id' => 'int(10) unsigned NOT NULL',
				'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'last_modified_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'created_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'PRIMARY KEY (`id`)',
				'KEY `et_ophtroperationnote_cc2_cataract_id_fk` (`cataract_id`)',
				'KEY `et_ophtroperationnote_cc2_complication_id_fk` (`complication_id`)',
				'KEY `et_ophtroperationnote_cc2_last_modified_user_id_fk` (`last_modified_user_id`)',
				'KEY `et_ophtroperationnote_cc2_created_user_id_fk` (`created_user_id`)',
				'CONSTRAINT `et_ophtroperationnote_cc2_cataract_id_fk` FOREIGN KEY (`cataract_id`) REFERENCES `et_ophtroperationnote_cataract` (`id`)',
				'CONSTRAINT `et_ophtroperationnote_cc2_complication_id_fk` FOREIGN KEY (`complication_id`) REFERENCES `et_ophtroperationnote_cataract_complications` (`id`)',
				'CONSTRAINT `et_ophtroperationnote_cc2_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `et_ophtroperationnote_cc2_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)',
			),
			'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin'
		);

		$this->insert('et_ophtroperationnote_cataract_complications',array('id'=>1,'name'=>'Choroidal haem','display_order'=>1));
		$this->insert('et_ophtroperationnote_cataract_complications',array('id'=>2,'name'=>'Corneal odema','display_order'=>2));
		$this->insert('et_ophtroperationnote_cataract_complications',array('id'=>3,'name'=>'Decentered IOL','display_order'=>3));
		$this->insert('et_ophtroperationnote_cataract_complications',array('id'=>4,'name'=>'Dropped nucleus','display_order'=>4));
		$this->insert('et_ophtroperationnote_cataract_complications',array('id'=>5,'name'=>'IOL exchange','display_order'=>5));
		$this->insert('et_ophtroperationnote_cataract_complications',array('id'=>6,'name'=>'IOL into vitreous','display_order'=>6));
		$this->insert('et_ophtroperationnote_cataract_complications',array('id'=>7,'name'=>'Iris prolapse','display_order'=>7));
		$this->insert('et_ophtroperationnote_cataract_complications',array('id'=>8,'name'=>'Iris trauma','display_order'=>8));
		$this->insert('et_ophtroperationnote_cataract_complications',array('id'=>9,'name'=>'Op cancelled','display_order'=>9));
		$this->insert('et_ophtroperationnote_cataract_complications',array('id'=>10,'name'=>'Other IOL problem','display_order'=>10));
		$this->insert('et_ophtroperationnote_cataract_complications',array('id'=>11,'name'=>'PC rupture','display_order'=>11));
		$this->insert('et_ophtroperationnote_cataract_complications',array('id'=>12,'name'=>'Vitreous loss','display_order'=>12));
		$this->insert('et_ophtroperationnote_cataract_complications',array('id'=>13,'name'=>'Wound burn','display_order'=>13));
		$this->insert('et_ophtroperationnote_cataract_complications',array('id'=>14,'name'=>'Zonular dialysis','display_order'=>14));
		$this->insert('et_ophtroperationnote_cataract_complications',array('id'=>15,'name'=>'Zonular rupture','display_order'=>15));

		$this->dropColumn('et_ophtroperationnote_cataract','wound_burn');
		$this->dropColumn('et_ophtroperationnote_cataract','iris_trauma');
		$this->dropColumn('et_ophtroperationnote_cataract','zonular_dialysis');
		$this->dropColumn('et_ophtroperationnote_cataract','pc_rupture');
		$this->dropColumn('et_ophtroperationnote_cataract','decentered_iol');
		$this->dropColumn('et_ophtroperationnote_cataract','iol_exchange');
		$this->dropColumn('et_ophtroperationnote_cataract','dropped_nucleus');
		$this->dropColumn('et_ophtroperationnote_cataract','op_cancelled');
		$this->dropColumn('et_ophtroperationnote_cataract','corneal_odema');
		$this->dropColumn('et_ophtroperationnote_cataract','iris_prolapse');
		$this->dropColumn('et_ophtroperationnote_cataract','zonular_rupture');
		$this->dropColumn('et_ophtroperationnote_cataract','vitreous_loss');
		$this->dropColumn('et_ophtroperationnote_cataract','iol_into_vitreous');
		$this->dropColumn('et_ophtroperationnote_cataract','other_iol_problem');
		$this->dropColumn('et_ophtroperationnote_cataract','choroidal_haem');
	}

	public function down()
	{
		$this->dropTable('et_ophtroperationnote_cataract_complication');
		$this->dropTable('et_ophtroperationnote_cataract_complications');

		$this->addColumn('et_ophtroperationnote_cataract','wound_burn','tinyint(1) unsigned NOT NULL DEFAULT 0');
		$this->addColumn('et_ophtroperationnote_cataract','iris_trauma','tinyint(1) unsigned NOT NULL DEFAULT 0');
		$this->addColumn('et_ophtroperationnote_cataract','zonular_dialysis','tinyint(1) unsigned NOT NULL DEFAULT 0');
		$this->addColumn('et_ophtroperationnote_cataract','pc_rupture','tinyint(1) unsigned NOT NULL DEFAULT 0');
		$this->addColumn('et_ophtroperationnote_cataract','decentered_iol','tinyint(1) unsigned NOT NULL DEFAULT 0');
		$this->addColumn('et_ophtroperationnote_cataract','iol_exchange','tinyint(1) unsigned NOT NULL DEFAULT 0');
		$this->addColumn('et_ophtroperationnote_cataract','dropped_nucleus','tinyint(1) unsigned NOT NULL DEFAULT 0');
		$this->addColumn('et_ophtroperationnote_cataract','op_cancelled','tinyint(1) unsigned NOT NULL DEFAULT 0');
		$this->addColumn('et_ophtroperationnote_cataract','corneal_odema','tinyint(1) unsigned NOT NULL DEFAULT 0');
		$this->addColumn('et_ophtroperationnote_cataract','iris_prolapse','tinyint(1) unsigned NOT NULL DEFAULT 0');
		$this->addColumn('et_ophtroperationnote_cataract','zonular_rupture','tinyint(1) unsigned NOT NULL DEFAULT 0');
		$this->addColumn('et_ophtroperationnote_cataract','vitreous_loss','tinyint(1) unsigned NOT NULL DEFAULT 0');
		$this->addColumn('et_ophtroperationnote_cataract','iol_into_vitreous','tinyint(1) unsigned NOT NULL DEFAULT 0');
		$this->addColumn('et_ophtroperationnote_cataract','other_iol_problem','tinyint(1) unsigned NOT NULL DEFAULT 0');
		$this->addColumn('et_ophtroperationnote_cataract','choroidal_haem','tinyint(1) unsigned NOT NULL DEFAULT 0');
	}
}
