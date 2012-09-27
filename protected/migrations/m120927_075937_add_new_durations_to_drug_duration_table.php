<?php

class m120927_075937_add_new_durations_to_drug_duration_table extends CDbMigration
{
	public function up()
	{
		$this->addColumn('drug_duration','display_order','int(10) unsigned NOT NULL DEFAULT 1');

		$this->update('drug_duration',array('display_order'=>6),'id=1');
		$this->update('drug_duration',array('display_order'=>7),'id=2');
		$this->update('drug_duration',array('display_order'=>8),'id=3');
		$this->update('drug_duration',array('display_order'=>9),'id=4');
		$this->update('drug_duration',array('display_order'=>10),'id=5');

		$this->insert('drug_duration',array('id'=>6,'name'=>'24 hours','display_order'=>1));
		$this->insert('drug_duration',array('id'=>7,'name'=>'48 hours','display_order'=>2));
		$this->insert('drug_duration',array('id'=>8,'name'=>'1 day','display_order'=>3));
		$this->insert('drug_duration',array('id'=>9,'name'=>'3 days','display_order'=>4));
		$this->insert('drug_duration',array('id'=>10,'name'=>'4 days','display_order'=>5));
		$this->insert('drug_duration',array('id'=>11,'name'=>'6 weeks','display_order'=>11));
		$this->insert('drug_duration',array('id'=>12,'name'=>'Other','display_order'=>12));
	}

	public function down()
	{
		$this->dropColumn('drug_duration','display_order');
		$this->delete('drug_duration','id > 5');
	}
}
