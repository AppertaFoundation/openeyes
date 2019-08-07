<?php

class m180619_135119_add_is_golden_patient_to_opbooking extends CDbMigration
{
    public function up()
    {
        $this->addColumn('et_ophtroperationbooking_operation', 'is_golden_patient', 'tinyint(1) unsigned DEFAULT NULL');
        $this->addColumn('et_ophtroperationbooking_operation_version', 'is_golden_patient', 'tinyint(1) unsigned DEFAULT NULL');
    }

    public function down()
    {
        $this->dropColumn('et_ophtroperationbooking_operation', 'is_golden_patient');
        $this->dropColumn('et_ophtroperationbooking_operation_version', 'is_golden_patient');
    }
}