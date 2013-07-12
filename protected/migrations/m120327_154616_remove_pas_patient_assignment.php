<?php

class m120327_154616_remove_pas_patient_assignment extends CDbMigration
{
	public function up()
	{
		$this->dropForeignKey('pas_patient_assignment_fk_1', 'pas_patient_assignment');
		$this->dropTable('pas_patient_assignment');
	}

	public function down()
	{
		echo "m120327_154616_remove_pas_patient_assignment does not support migration down.\n";
		return false;
	}

	public function safeUp()
	{
		$this->up();
	}

	public function safeDown()
	{
		$this->down();
	}

}
