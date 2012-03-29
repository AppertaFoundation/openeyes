<?php

class m120329_163400_added_eye_id_field_to_procedurelist_element extends CDbMigration
{
	public function up()
	{
		$this->addColumn('et_ophtroperationnote_procedurelist','eye_id','integer(10) unsigned NOT NULL');

		$this->update('et_ophtroperationnote_procedurelist',array('eye_id'=>1));
		$this->createIndex('et_ophtroperationnote_procedurelist_eye_id_fk','et_ophtroperationnote_procedurelist','eye_id');
		$this->addForeignKey('et_ophtroperationnote_procedurelist_eye_id_fk','et_ophtroperationnote_procedurelist','eye_id','eye','id');

		$event_type = $this->dbConnection->createCommand()->select('id')->from('event_type')->where('name=:name', array(':name'=>'Operation note'))->queryRow();
		$element_type = $this->dbConnection->createCommand()->select('id')->from('element_type')->where('event_type_id = :event_type_id and class_name=:class_name',array(':event_type_id' => $event_type['id'], ':class_name'=>'ElementProcedureList'))->queryRow();

		$this->insert('element_type_eye',array('element_type_id'=>$element_type['id'],'eye_id'=>2,'display_order'=>1));
		$this->insert('element_type_eye',array('element_type_id'=>$element_type['id'],'eye_id'=>1,'display_order'=>2));
	}

	public function down()
	{
		$event_type = $this->dbConnection->createCommand()->select('id')->from('event_type')->where('name=:name', array(':name'=>'Operation note'))->queryRow();
		$element_type = $this->dbConnection->createCommand()->select('id')->from('element_type')->where('event_type_id = :event_type_id and class_name=:class_name',array(':event_type_id' => $event_type['id'], ':class_name'=>'ElementProcedureList'))->queryRow();

		$this->delete('element_type_eye','element_type_id='.$element_type['id']);

		$this->dropForeignKey('et_ophtroperationnote_procedurelist_eye_id_fk','et_ophtroperationnote_procedurelist');
		$this->dropIndex('et_ophtroperationnote_procedurelist_eye_id_fk','et_ophtroperationnote_procedurelist');
		$this->dropColumn('et_ophtroperationnote_procedurelist','eye_id');
	}
}
