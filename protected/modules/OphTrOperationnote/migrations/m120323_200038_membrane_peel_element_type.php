<?php

class m120323_200038_membrane_peel_element_type extends CDbMigration
{
	public function up()
	{
		$this->createTable('et_ophtroperationnote_membrane_peel', array(
				'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
				'event_id' => 'int(10) unsigned NOT NULL',
				'membrane_blue' => 'tinyint(1) unsigned NOT NULL DEFAULT 0',
				'brilliant_blue' => 'tinyint(1) unsigned NOT NULL DEFAULT 0',
				'other_dye' => 'varchar(255) COLLATE utf8_bin NOT NULL',
				'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'last_modified_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'created_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'PRIMARY KEY (`id`)',
			),
			'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin'
		);

		$event_type = $this->dbConnection->createCommand()->select('id')->from('event_type')->where('name=:name', array(':name'=>'Operation note'))->queryRow();
		$this->insert('element_type', array('name' => 'Membrane peel', 'class_name' => 'ElementMembranePeel', 'event_type_id' => $event_type['id'], 'display_order' => 3, 'default' => 0));

		$element_type = $this->dbConnection->createCommand()->select('id')->from('element_type')->where('event_type_id = :event_type_id and class_name=:class_name',array(':event_type_id' => $event_type['id'], ':class_name'=>'ElementMembranePeel'))->queryRow();

		$proc = $this->dbConnection->createCommand()->select('id')->from('proc')->where('snomed_term=:snomed',array(':snomed'=>'Epiretinal dissection'))->queryRow();
		$this->insert('et_ophtroperationnote_procedure_element',array('procedure_id'=>$proc['id'],'element_type_id'=>$element_type['id']));
	}

	public function down()
	{
		$event_type = $this->dbConnection->createCommand()->select('id')->from('event_type')->where('name=:name', array(':name'=>'Operation note'))->queryRow();
		$element_type = $this->dbConnection->createCommand()->select('id')->from('element_type')->where('event_type_id = :event_type_id and class_name=:class_name',array(':event_type_id' => $event_type['id'], ':class_name'=>'ElementMembranePeel'))->queryRow();

		$this->delete('et_ophtroperationnote_procedure_element','element_type_id='.$element_type['id']);
		$this->dropTable('et_ophtroperationnote_membrane_peel');

		$this->delete('element_type','id = '.$element_type['id']);
	}
}
