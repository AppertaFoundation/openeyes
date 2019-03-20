<?php

class m190320_104336_add_max_complex_procedures_to_ophtroperation_booking_session extends CDbMigration
{
	public function up()
	{
    $this->addColumn('ophtroperationbooking_operation_session', 'max_complex_procedures', 'integer DEFAULT NULL');
    $this->addColumn('ophtroperationbooking_operation_session_version', 'max_complex_procedures', 'integer DEFAULT NULL');
	}

	public function down()
	{
    $this->dropColumn('ophtroperationbooking_operation_session', 'max_complex_procedures');
    $this->dropColumn('ophtroperationbooking_operation_session_version', 'max_complex_procedures');
	}
}