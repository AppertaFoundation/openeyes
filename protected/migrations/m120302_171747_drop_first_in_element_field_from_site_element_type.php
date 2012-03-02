<?php

class m120302_171747_drop_first_in_element_field_from_site_element_type extends CDbMigration
{
	public function up()
	{
		$this->dropColumn('site_element_type','first_in_episode');
	}

	public function down()
	{
		$this->addColumn('site_element_type','first_in_episode',"tinyint(1) unsigned DEFAULT '1'");
	}
}
