<?php

class m120320_140929_eye_table extends CDbMigration
{
	public function up()
	{
		$this->createTable('eye',array(
			'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
			'name' => 'varchar(10) COLLATE utf8_bin DEFAULT NULL',
			'PRIMARY KEY (`id`)'
		), 'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin');

		$this->update('element_operation',array('eye'=>3),'eye=2');
		$this->update('element_operation',array('eye'=>2),'eye=1');
		$this->update('element_operation',array('eye'=>1),'eye=0');

		$this->update('element_diagnosis',array('eye'=>3),'eye=2');
		$this->update('element_diagnosis',array('eye'=>2),'eye=1');
		$this->update('element_diagnosis',array('eye'=>1),'eye=0');

		$this->insert('eye',array('name'=>'Left'));
		$this->insert('eye',array('name'=>'Right'));
		$this->insert('eye',array('name'=>'Both'));

		$this->renameColumn('element_operation','eye','eye_id');
		$this->createIndex('element_operation_eye_id_fk','element_operation','eye_id');
		$this->alterColumn('element_operation','eye_id',"int(10) unsigned NOT NULL DEFAULT '1'");
		$this->addForeignKey('element_operation_eye_id_fk','element_operation','eye_id','eye','id');

		$this->renameColumn('element_diagnosis','eye','eye_id');
		$this->createIndex('element_diagnosis_eye_id_fk','element_diagnosis','eye_id');
		$this->alterColumn('element_diagnosis','eye_id',"int(10) unsigned NOT NULL DEFAULT '1'");
		$this->addForeignKey('element_diagnosis_eye_id_fk','element_diagnosis','eye_id','eye','id');

		$this->createTable('element_type_eye',array(
			'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
			'element_type_id' => 'int(10) unsigned NOT NULL',
			'eye_id' => 'int(10) unsigned NOT NULL',
			'display_order' => 'tinyint(1) unsigned NOT NULL DEFAULT 1',
			'PRIMARY KEY (`id`)'
		), 'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin');

		$this->createIndex('element_type_eye_fk1','element_type_eye','element_type_id');
		$this->createIndex('element_type_eye_fk2','element_type_eye','eye_id');
		$this->addForeignKey('element_type_eye_fk1','element_type_eye','element_type_id','element_type','id');
		$this->addForeignKey('element_type_eye_fk2','element_type_eye','eye_id','eye','id');

		$element_type = $this->dbConnection->createCommand()->select('id')->from('element_type')->where('name=:name', array(':name'=>'Operation'))->queryRow();

		$this->insert('element_type_eye',array('element_type_id'=>$element_type['id'],'eye_id'=>1,'display_order'=>3));
		$this->insert('element_type_eye',array('element_type_id'=>$element_type['id'],'eye_id'=>2,'display_order'=>1));
		$this->insert('element_type_eye',array('element_type_id'=>$element_type['id'],'eye_id'=>3,'display_order'=>2));

		$element_type = $this->dbConnection->createCommand()->select('id')->from('element_type')->where('name=:name', array(':name'=>'Diagnosis'))->queryRow();

		$this->insert('element_type_eye',array('element_type_id'=>$element_type['id'],'eye_id'=>1,'display_order'=>3));
		$this->insert('element_type_eye',array('element_type_id'=>$element_type['id'],'eye_id'=>2,'display_order'=>1));
		$this->insert('element_type_eye',array('element_type_id'=>$element_type['id'],'eye_id'=>3,'display_order'=>2));
	}

	public function down()
	{
		$this->dropForeignKey('element_type_eye_fk1','element_type_eye');
		$this->dropForeignKey('element_type_eye_fk2','element_type_eye');
		$this->dropIndex('element_type_eye_fk1','element_type_eye');
		$this->dropIndex('element_type_eye_fk2','element_type_eye');
		$this->dropTable('element_type_eye');

		$this->dropForeignKey('element_operation_eye_id_fk','element_operation');
		$this->dropIndex('element_operation_eye_id_fk','element_operation');
		$this->renameColumn('element_operation','eye_id','eye');
		$this->alterColumn('element_operation','eye',"tinyint(1) unsigned DEFAULT '0'");

		$this->dropForeignKey('element_diagnosis_eye_id_fk','element_diagnosis');
		$this->dropIndex('element_diagnosis_eye_id_fk','element_diagnosis');
		$this->renameColumn('element_diagnosis','eye_id','eye');
		$this->alterColumn('element_diagnosis','eye',"tinyint(1) unsigned DEFAULT '0'");

		$this->update('element_operation',array('eye'=>0),'eye=1');
		$this->update('element_operation',array('eye'=>1),'eye=2');
		$this->update('element_operation',array('eye'=>2),'eye=3');

		$this->update('element_diagnosis',array('eye'=>0),'eye=1');
		$this->update('element_diagnosis',array('eye'=>1),'eye=2');
		$this->update('element_diagnosis',array('eye'=>2),'eye=3');

		$this->dropTable('eye');
	}
}
