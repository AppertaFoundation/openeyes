<?php

class m140926_135604_any_grade_of_doctor extends OEMigration
{
    public function up()
    {
        $this->addColumn('et_ophtroperationbooking_operation', 'any_grade_of_doctor', 'tinyint(1) unsigned not null AFTER consultant_required');
        $this->addColumn('et_ophtroperationbooking_operation_version', 'any_grade_of_doctor', 'tinyint(1) unsigned not null AFTER consultant_required');
        $this->update('et_ophtroperationbooking_operation_version', array('any_grade_of_doctor' => 0));
    }

    public function down()
    {
        $this->dropColumn('et_ophtroperationbooking_operation', 'any_grade_of_doctor');
        $this->dropColumn('et_ophtroperationbooking_operation_version', 'any_grade_of_doctor');
    }
}
