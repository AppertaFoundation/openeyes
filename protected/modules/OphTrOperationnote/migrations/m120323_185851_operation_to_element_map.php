<?php

class m120323_185851_operation_to_element_map extends CDbMigration
{
	public function up()
	{
		$this->createTable('et_ophtroperationnote_procedure_element',array(
				'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
				'procedure_id' => 'int(10) unsigned NOT NULL',
				'element_type_id' => 'int(10) unsigned NOT NULL',
				'display_order' => 'tinyint(3) unsigned NOT NULL DEFAULT 1',
				'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'last_modified_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'created_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'PRIMARY KEY (`id`)',
				'KEY `et_ophtroperationnote_pe_procedure_id` (`procedure_id`)',
				'KEY `et_ophtroperationnote_pe_element_type_id` (`element_type_id`)',
				'CONSTRAINT `et_ophtroperationnote_pe_procedure_fk` FOREIGN KEY (`procedure_id`) REFERENCES `proc` (`id`)',
				'CONSTRAINT `et_ophtroperationnote_pe_element_type_fk` FOREIGN KEY (`element_type_id`) REFERENCES `element_type` (`id`)'
			),
			'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin'
		);

		$event_type = $this->dbConnection->createCommand()->select('id')->from('event_type')->where('name=:name', array(':name'=>'Operation note'))->queryRow();
		$element_type = $this->dbConnection->createCommand()->select('id')->from('element_type')->where('event_type_id = :event_type_id and class_name=:class_name',array(':event_type_id' => $event_type['id'], ':class_name'=>'ElementVitrectomy'))->queryRow();

		$proc = $this->dbConnection->createCommand()->select('id')->from('proc')->where('snomed_term=:snomed',array(':snomed'=>'Vitrectomy'))->queryRow();
		$this->insert('et_ophtroperationnote_procedure_element',array('id'=>1,'procedure_id'=>$proc['id'],'element_type_id'=>$element_type['id']));
	}

	public function down()
	{
		$this->dropTable('et_ophtroperationnote_procedure_element');
	}
}
