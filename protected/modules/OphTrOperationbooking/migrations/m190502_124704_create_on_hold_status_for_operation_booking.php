<?php

class m190502_124704_create_on_hold_status_for_operation_booking extends CDbMigration
{
	public function up()
	{
	    $this->insert('ophtroperationbooking_operation_status', ['name' => 'On-Hold']);
	}

	public function down()
	{
		$this->delete('ophtroperationbooking_operation_status', 'name = "On-Hold"');
	}
}