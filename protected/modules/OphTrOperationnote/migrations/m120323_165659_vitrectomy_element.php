<?php

class m120323_165659_vitrectomy_element extends CDbMigration
{
	public function up()
	{
		$this->createTable('et_ophtroperationnote_gauge', array(
				'id' => 'int(10) unsigned NOT NULL',
				'value' => 'varchar(5) COLLATE utf8_bin NOT NULL',
				'display_order' => 'tinyint(3) unsigned NOT NULL',
				'PRIMARY KEY (`id`)',
			),
			'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin'
		);

		$this->insert('et_ophtroperationnote_gauge',array('id'=>1,'value'=>'20g','display_order'=>1));
		$this->insert('et_ophtroperationnote_gauge',array('id'=>2,'value'=>'23g','display_order'=>2));
		$this->insert('et_ophtroperationnote_gauge',array('id'=>3,'value'=>'25g','display_order'=>3));

		$this->createTable('et_ophtroperationnote_vitrectomy', array(
				'id' => 'int(10) unsigned NOT NULL',
				'event_id' => 'int(10) unsigned NOT NULL',
				'gauge_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
				'pvd_induced' => 'tinyint(1) unsigned NOT NULL DEFAULT 0',
				'other_dye' => 'varchar(255) COLLATE utf8_bin NOT NULL',
				'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'last_modified_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'created_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'PRIMARY KEY (`id`)',
				'KEY `et_ophtroperationnote_vitrectomy_event_id` (`event_id`)',
				'KEY `et_ophtroperationnote_vitrectomy_gauge_id` (`gauge_id`)',
				'CONSTRAINT `et_ophtroperationnote_vitrectomy_event_fk` FOREIGN KEY (`event_id`) REFERENCES `event` (`id`)',
				'CONSTRAINT `et_ophtroperationnote_vitrectomy_gauge_fk` FOREIGN KEY (`gauge_id`) REFERENCES `et_ophtroperationnote_gauge` (`id`)'
			),
			'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin'
		);

		$event_type = $this->dbConnection->createCommand()->select('id')->from('event_type')->where('name=:name', array(':name'=>'Operation note'))->queryRow();

		$this->insert('element_type', array('name' => 'Vitrectomy', 'class_name' => 'ElementVitrectomy', 'event_type_id' => $event_type['id'], 'display_order' => 2, 'default' => 0));
	}

	public function down()
	{
		$event_type = $this->dbConnection->createCommand()->select('id')->from('event_type')->where('name=:name', array(':name'=>'Operation note'))->queryRow();
		$this->delete('element_type','event_type_id = '.$event_type['id']." and class_name = 'ElementVitrectomy'");

		$this->dropTable('et_ophtroperationnote_vitrectomy');
		$this->dropTable('et_ophtroperationnote_gauge');
	}
}
