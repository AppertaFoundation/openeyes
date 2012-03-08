<?php

class m120307_172115_remove_site_element_type_and_possible_element_type_tables extends CDbMigration
{
	public function up()
	{
		$this->dropTable('site_element_type');
		$this->dropTable('possible_element_type');
	}

	public function down()
	{
	}
}
