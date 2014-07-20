<?php

class m140610_152528_medication_reasons_for_stopping extends CDbMigration
{
	public function up()
	{
		foreach (array('Shortness of breath','Wheezing','Palpitations','Collapse','Lethargy','Alopecia','Exercise intolerance','Other','Conjunctival injection','Allergic conjunctivitis','Contact dermatitis','Uveitis','Pseudophembhygoid') as $field) {
			$this->insert('medication_stop_reason',array('name'=>$field));
		}
	}

	public function down()
	{
		foreach (array('Shortness of breath','Wheezing','Palpitations','Collapse','Lethargy','Alopecia','Exercise intolerance','Other','Conjunctival injection','Allergic conjunctivitis','Contact dermatitis','Uveitis','Pseudophembhygoid') as $field) {
			$this->delete('medication_stop_reason',"name = '$field'");
		}
	}
}
