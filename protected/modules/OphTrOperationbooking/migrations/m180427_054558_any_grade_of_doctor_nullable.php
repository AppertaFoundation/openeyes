<?php

class m180427_054558_any_grade_of_doctor_nullable extends CDbMigration
{
    public function up()
    {
        $this->alterColumn('et_ophtroperationbooking_operation', 'any_grade_of_doctor', 'TINYINT NULL DEFAULT NULL');
        $this->alterColumn('et_ophtroperationbooking_operation_version', 'any_grade_of_doctor', 'TINYINT NULL DEFAULT NULL');
    }

    public function down()
    {
        $this->alterColumn('et_ophtroperationbooking_operation', 'any_grade_of_doctor', 'TINYINT NOT NULL DEFAULT 0');
        $this->alterColumn('et_ophtroperationbooking_operation_version', 'any_grade_of_doctor', 'TINYINT NOT NULL DEFAULT 0');
    }
}