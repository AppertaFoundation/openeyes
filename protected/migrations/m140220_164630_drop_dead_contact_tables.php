<?php

class m140220_164630_drop_dead_contact_tables extends CDbMigration
{
	public function up()
	{
		$this->dropTable('institution_consultant_assignment_version');
		$this->dropTable('institution_consultant_assignment');
		$this->dropTable('site_consultant_assignment_version');
		$this->dropTable('site_consultant_assignment');
		$this->dropTable('consultant_version');
		$this->dropTable('consultant');
		$this->dropTable('manual_contact_version');
		$this->dropTable('manual_contact');
		$this->dropTable('contact_type_version');
		$this->dropTable('contact_type');
	}

	public function down()
	{
		echo "m140220_164630_drop_dead_contact_tables does not support migration down.\n";
		return false;
	}
}
