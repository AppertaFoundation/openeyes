<?php

class m130709_090359_dr_patientshortcodes extends CDbMigration
{
	public function up()
	{
		$this->insert('patient_shortcode',array('event_type_id'=>null,'default_code'=>'dmt','code'=>'dmt','description'=>'Type of diabetes mellitus'));
	}

	public function down()
	{
		$this->delete('patient_shortcode', "default_code = 'dmt'");
	}

	/*
	// Use safeUp/safeDown to do migration with transaction
	public function safeUp()
	{
	}

	public function safeDown()
	{
	}
	*/
}