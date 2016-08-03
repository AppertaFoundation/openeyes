<?php

class m160802_112433_add_commissioning_body_for_local_authorities extends CDbMigration
{
	public function up()
	{
		$this->insert('commissioning_body_type', array('name'=>'Local Authorities', 'shortname'=>'LA'));
		$this->insert('commissioning_body_service_type', array('name'=>'Social Services Department', 'shortname'=>'SSD', 'correspondence_name'=>'Social Services Department'));
		// we need a blank contact for the commissioning_body table constraint
		$this->insert('contact', array('nick_name'=>'Local Authorities','first_name'=>'','last_name'=>''));
		$cBodyType = $this->dbConnection->createCommand()->select('id')->from('commissioning_body_type')->where('shortname=:shortname', array(':shortname'=>'LA'))->queryRow();
		$contactData = $this->dbConnection->createCommand()->select('id')->from('contact')->where('nick_name=:nick_name', array(':nick_name'=>'Local Authorities'))->queryRow();
		$this->insert('commissioning_body', array('name'=>'eCVI Local Authorities', 'code'=>'eCVILA', 'commissioning_body_type_id' => $cBodyType['id'], 'contact_id'=>$contactData["id"]));

	}

	public function down()
	{
		$this->delete('commissioning_body', "code='eCVILA'");
		$this->delete('contact', "nick_name='Local Authorities'");
		$this->delete('commissioning_body_type', "shortname = 'LA'");
		$this->delete('commissioning_body_service_type', "shortname = 'SSD'");
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