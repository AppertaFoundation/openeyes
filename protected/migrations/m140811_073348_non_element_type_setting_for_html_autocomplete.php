<?php

class m140811_073348_non_element_type_setting_for_html_autocomplete extends OEMigration
{
	public function up()
	{
		$this->alterColumn('setting_metadata','element_type_id','int(10) unsigned null');
		$this->alterColumn('setting_metadata_version','element_type_id','int(10) unsigned null');

		$this->insert('setting_field_type',array('id'=>3,'name'=>'Radio buttons'));

		$this->insert('setting_metadata',array(
			'element_type_id' => null,
			'display_order' => 10,
			'field_type_id' => 3,
			'key' => 'html_autocomplete',
			'name' => 'Auto-complete text inputs',
			'data' => 'a:2:{s:2:"on";s:2:"On";s:3:"off";s:3:"Off";}',
			'default_value' => 'off',
		));

		foreach (array('setting_firm','setting_installation','setting_institution','setting_site','setting_specialty','setting_subspecialty','setting_user') as $table) {
			$this->alterColumn($table,'element_type_id','int(10) unsigned null');
			$this->alterColumn($table.'_version','element_type_id','int(10) unsigned null');
		}
	}

	public function down()
	{
		foreach (array('setting_firm','setting_installation','setting_institution','setting_site','setting_specialty','setting_subspecialty','setting_user') as $table) {
			$this->alterColumn($table,'element_type_id','int(10) unsigned not null');
			$this->alterColumn($table.'_version','element_type_id','int(10) unsigned not null');
		}

		$this->delete('setting_metadata',"element_type_id is null and `key` = 'html_autocomplete'");
		$this->delete('setting_field_type',"id=3");

		$this->alterColumn('setting_metadata','element_type_id','int(10) unsigned not null');
		$this->alterColumn('setting_metadata_version','element_type_id','int(10) unsigned not null');
	}
}
