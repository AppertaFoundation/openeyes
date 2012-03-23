<?php

class m120323_204644_oil_procedure_element_assignment extends CDbMigration
{
	public function up()
	{
		$event_type = $this->dbConnection->createCommand()->select('id')->from('event_type')->where('name=:name', array(':name'=>'Operation note'))->queryRow();
		$element_type = $this->dbConnection->createCommand()->select('id')->from('element_type')->where('event_type_id = :event_type_id and class_name=:class_name',array(':event_type_id' => $event_type['id'], ':class_name'=>'ElementTamponade'))->queryRow();
		
		$proc = $this->dbConnection->createCommand()->select('id')->from('proc')->where('snomed_term=:snomed',array(':snomed'=>'Injection of silicone oil into vitreous'))->queryRow();
		$this->insert('et_ophtroperationnote_procedure_element',array('procedure_id'=>$proc['id'],'element_type_id'=>$element_type['id']));
	}

	public function down()
	{
		$event_type = $this->dbConnection->createCommand()->select('id')->from('event_type')->where('name=:name', array(':name'=>'Operation note'))->queryRow();
		$element_type = $this->dbConnection->createCommand()->select('id')->from('element_type')->where('event_type_id = :event_type_id and class_name=:class_name',array(':event_type_id' => $event_type['id'], ':class_name'=>'ElementTamponade'))->queryRow();
		$proc = $this->dbConnection->createCommand()->select('id')->from('proc')->where('snomed_term=:snomed',array(':snomed'=>'Injection of silicone oil into vitreous'))->queryRow();

		$this->delete('et_ophtroperationnote_procedure_element','element_type_id='.$element_type['id'].' and procedure_id='.$proc['id']);
	}
}
