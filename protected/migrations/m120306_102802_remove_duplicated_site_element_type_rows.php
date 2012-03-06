<?php

class m120306_102802_remove_duplicated_site_element_type_rows extends CDbMigration
{
	public function up()
	{
		$this->delete('site_element_type','id in (17,49,81,113,145,177,208,238,268,290,320,386,434)');
	}

	public function down()
	{
		$this->insert('site_element_type',array('id'=>17,'possible_element_type_id'=>1,'subspecialty_id'=>1,'view_number'=>1,'required'=>0));
		$this->insert('site_element_type',array('id'=>49,'possible_element_type_id'=>2,'subspecialty_id'=>1,'view_number'=>1,'required'=>0));
		$this->insert('site_element_type',array('id'=>81,'possible_element_type_id'=>3,'subspecialty_id'=>1,'view_number'=>1,'required'=>0));
		$this->insert('site_element_type',array('id'=>113,'possible_element_type_id'=>4,'subspecialty_id'=>1,'view_number'=>1,'required'=>0));
		$this->insert('site_element_type',array('id'=>145,'possible_element_type_id'=>5,'subspecialty_id'=>1,'view_number'=>1,'required'=>0));
		$this->insert('site_element_type',array('id'=>177,'possible_element_type_id'=>6,'subspecialty_id'=>1,'view_number'=>1,'required'=>0));
		$this->insert('site_element_type',array('id'=>208,'possible_element_type_id'=>7,'subspecialty_id'=>1,'view_number'=>1,'required'=>1));
		$this->insert('site_element_type',array('id'=>238,'possible_element_type_id'=>8,'subspecialty_id'=>1,'view_number'=>1,'required'=>1));
		$this->insert('site_element_type',array('id'=>268,'possible_element_type_id'=>9,'subspecialty_id'=>1,'view_number'=>1,'required'=>1));
		$this->insert('site_element_type',array('id'=>290,'possible_element_type_id'=>13,'subspecialty_id'=>1,'view_number'=>1,'required'=>1));
		$this->insert('site_element_type',array('id'=>320,'possible_element_type_id'=>14,'subspecialty_id'=>1,'view_number'=>1,'required'=>1));
		$this->insert('site_element_type',array('id'=>386,'possible_element_type_id'=>18,'subspecialty_id'=>1,'view_number'=>1,'required'=>1));
		$this->insert('site_element_type',array('id'=>434,'possible_element_type_id'=>20,'subspecialty_id'=>1,'view_number'=>1,'required'=>1));
	}
}
