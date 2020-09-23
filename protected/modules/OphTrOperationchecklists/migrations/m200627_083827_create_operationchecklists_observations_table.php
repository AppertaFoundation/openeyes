<?php

class m200627_083827_create_operationchecklists_observations_table extends OEMigration
{
    public function up()
    {
        $this->createOETable('ophtroperationchecklists_observations', array(
            'id' => 'pk',
            'checklist_result_id' => 'int(11)',
            'blood_pressure_systolic' => 'INT(3) UNSIGNED NULL',
            'blood_pressure_diastolic' => 'INT(3) UNSIGNED NULL',
            'pulse' => 'INT(3) unsigned NULL',
            'temperature' => 'DECIMAL(3,1) UNSIGNED NULL',
            'respiration' => 'INT(3) UNSIGNED NULL',
            'o2_sat' => 'INT(3) UNSIGNED NULL',
            'ews' => 'INT(3) UNSIGNED NULL',
            'blood_glucose' => 'INT(3) NULL ',
            'hba1c' => 'INT(4) unsigned NULL',
        ), true);

        // Add Foreign Key
        $this->addForeignKey(
            'ophtroperationchecklists_observations_crid_fk',
            'ophtroperationchecklists_observations',
            'checklist_result_id',
            'ophtroperationchecklists_admission_results',
            'id'
        );
    }

    public function down()
    {
        $this->dropOETable('ophtroperationchecklists_observations', true);
    }
}
