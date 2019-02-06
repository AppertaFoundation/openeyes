<?php

class m190129_101959_add_prescription_print_permissions_to_print_group extends CDbMigration
{
	public function up()
	{
		$this->insert('authitemchild', array('parent' => 'TaskPrint', 'child' => 'OprnPrintPrescription', 'last_modified_user_id' => 1,'last_modified_date' => time(), 'created_user_id' => 1,'created_date' => time()));
	}

	public function down()
	{
		$this->delete('authitemchild', 'parent = ? and child = ?', array('TaskPrint', 'OprnPrintPrescription'));
	}
}