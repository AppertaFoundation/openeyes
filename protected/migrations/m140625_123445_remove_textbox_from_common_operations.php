<?php

class m140625_123445_remove_textbox_from_common_operations extends OEMigration
{
	public function up()
	{
		$this->delete('common_previous_operation', "name='Text box'");
	}

	public function down()
	{
		$this->insert('common_previous_operation',array('name'=>'Text box'));
	}
}