<?php

class m120330_153339_additional_fields_for_cataract_element extends CDbMigration
{
	public function up()
	{
		$this->addColumn('et_ophtroperationnote_cataract','vision_blue','tinyint(1) unsigned NOT NULL DEFAULT 0');

		$this->createTable('et_ophtroperationnote_cataract_iol_position', array(
				'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
				'name' => 'varchar(32) COLLATE utf8_bin NOT NULL',
				'display_order' => 'tinyint(3) unsigned NOT NULL',
				'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'last_modified_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'created_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'PRIMARY KEY (`id`)',
				'KEY `et_ophtroperationnote_cip_last_modified_user_id_fk` (`last_modified_user_id`)',
				'KEY `et_ophtroperationnote_cip_created_user_id_fk` (`created_user_id`)',
				'CONSTRAINT `et_ophtroperationnote_cip_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `et_ophtroperationnote_cip_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)'
			),
			'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin'
		);

		$this->insert('et_ophtroperationnote_cataract_iol_position',array('id'=>1,'name'=>'In the bag','display_order'=>1));
		$this->insert('et_ophtroperationnote_cataract_iol_position',array('id'=>2,'name'=>'Partly in the bag','display_order'=>2));
		$this->insert('et_ophtroperationnote_cataract_iol_position',array('id'=>3,'name'=>'In the sulcus','display_order'=>3));
		$this->insert('et_ophtroperationnote_cataract_iol_position',array('id'=>4,'name'=>'Anterior chamber','display_order'=>4));
		$this->insert('et_ophtroperationnote_cataract_iol_position',array('id'=>5,'name'=>'Sutured posterior chamber','display_order'=>5));
		$this->insert('et_ophtroperationnote_cataract_iol_position',array('id'=>6,'name'=>'Iris fixated','display_order'=>6));
		$this->insert('et_ophtroperationnote_cataract_iol_position',array('id'=>7,'name'=>'Other','display_order'=>7));

		$this->addColumn('et_ophtroperationnote_cataract','iol_position_id','integer(10) unsigned NOT NULL');
		$this->createIndex('et_ophtroperationnote_cataract_iol_position_fk','et_ophtroperationnote_cataract','iol_position_id');
		$this->addForeignKey('et_ophtroperationnote_cataract_iol_position_fk','et_ophtroperationnote_cataract','iol_position_id','et_ophtroperationnote_cataract_iol_position','id');
	}

	public function down()
	{
		$this->dropForeignKey('et_ophtroperationnote_cataract_iol_position_fk','et_ophtroperationnote_cataract');
		$this->dropIndex('et_ophtroperationnote_cataract_iol_position_fk','et_ophtroperationnote_cataract');
		$this->dropColumn('et_ophtroperationnote_cataract','iol_position_id');
		$this->dropTable('et_ophtroperationnote_cataract_iol_position');
		$this->dropColumn('et_ophtroperationnote_cataract','vision_blue');
	}
}
