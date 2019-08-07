<?php

class m180427_060950_op_booking_complexity extends CDbMigration
{
    public function up()
    {
        $this->addColumn('et_ophtroperationbooking_operation', 'complexity', 'TINYINT NULL DEFAULT NULL');
        $this->addColumn('et_ophtroperationbooking_operation_version', 'complexity', 'TINYINT NULL DEFAULT NULL');
    }

    public function down()
    {
        $this->dropColumn('et_ophtroperationbooking_operation', 'complexity');
        $this->dropColumn('et_ophtroperationbooking_operation_version', 'complexity');
    }
}