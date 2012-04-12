<?php

class m120412_143801_general_operation_comments_and_postop_instructions extends CDbMigration
{
	public function up()
	{
		$event_type = $this->dbConnection->createCommand()->select('id')->from('event_type')->where('name=:name', array(':name'=>'Operation note'))->queryRow();

		$element_type = $this->dbConnection->createCommand()->select('id')->from('element_type')->where('event_type_id = :event_type_id and class_name=:class_name',array(':event_type_id' => $event_type['id'], ':class_name'=>'ElementSurgeon'))->queryRow();
		$this->update('element_type',array('display_order'=>4),'id='.$element_type['id']);

		$element_type = $this->dbConnection->createCommand()->select('id')->from('element_type')->where('event_type_id = :event_type_id and class_name=:class_name',array(':event_type_id' => $event_type['id'], ':class_name'=>'ElementDrugs'))->queryRow();
		$this->update('element_type',array('display_order'=>5),'id='.$element_type['id']);

		$this->createTable('et_ophtroperationnote_comments',array(
				'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
				'event_id' => 'int(10) unsigned NOT NULL',
				'comments' => 'varchar(4096) COLLATE utf8_bin NOT NULL',
				'postop_instructions' => 'varchar(4096) COLLATE utf8_bin NOT NULL',
				'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'last_modified_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'created_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'PRIMARY KEY (`id`)',
				'KEY `et_ophtroperationnote_comments_last_modified_user_id_fk` (`last_modified_user_id`)',
				'KEY `et_ophtroperationnote_comments_created_user_id_fk` (`created_user_id`)',
				'CONSTRAINT `et_ophtroperationnote_comments_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `et_ophtroperationnote_comments_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)',
			),
			'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin'
		);

		$this->insert('element_type', array('name' => 'Comments', 'class_name' => 'ElementComments', 'event_type_id' => $event_type['id'], 'display_order' => 6, 'default' => 1));
	}

	public function down()
	{
		$this->dropTable('et_ophtroperationnote_comments');

		$event_type = $this->dbConnection->createCommand()->select('id')->from('event_type')->where('name=:name', array(':name'=>'Operation note'))->queryRow();
		$element_type = $this->dbConnection->createCommand()->select('id')->from('element_type')->where('event_type_id = :event_type_id and class_name=:class_name',array(':event_type_id' => $event_type['id'], ':class_name'=>'ElementComments'))->queryRow();
		
		$this->delete('element_type','id='.$element_type['id']);

		$element_type = $this->dbConnection->createCommand()->select('id')->from('element_type')->where('event_type_id = :event_type_id and class_name=:class_name',array(':event_type_id' => $event_type['id'], ':class_name'=>'ElementSurgeon'))->queryRow();
		$this->update('element_type',array('display_order'=>3),'id='.$element_type['id']);

		$element_type = $this->dbConnection->createCommand()->select('id')->from('element_type')->where('event_type_id = :event_type_id and class_name=:class_name',array(':event_type_id' => $event_type['id'], ':class_name'=>'ElementDrugs'))->queryRow();
		$this->update('element_type',array('display_order'=>8),'id='.$element_type['id']);
	}
}
