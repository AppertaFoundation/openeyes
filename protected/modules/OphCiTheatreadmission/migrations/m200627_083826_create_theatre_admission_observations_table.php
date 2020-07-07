<?php

class m200627_083826_create_theatre_admission_observations_table extends OEMigration
{
    public function up()
    {
        $this->createOETable('ophcitheatreadmission_observations', array(
            'id' => 'pk',
            'checklist_result_id' => 'int(11)',
            'blood_pressure_systolic' => 'INT(3) UNSIGNED NOT NULL DEFAULT 0',
            'blood_pressure_diastolic' => 'INT(3) UNSIGNED NOT NULL DEFAULT 0',
            'pulse' => 'INT(3) unsigned NOT NULL DEFAULT 0',
            'temperature' => 'INT(3) UNSIGNED NOT NULL DEFAULT 0',
            'respiration' => 'INT(3) UNSIGNED NOT NULL DEFAULT 0',
            'o2_sat' => 'INT(3) UNSIGNED NOT NULL DEFAULT 0',
            'ews' => 'INT(3) UNSIGNED NOT NULL DEFAULT 0',
            'blood_glucose' => 'INT(3) NOT NULL DEFAULT 0.0',
            'hba1c' => 'INT(4) unsigned NOT NULL DEFAULT 0',
            'inr' => 'INT(2) NOT NULL DEFAULT 0',
        ), true);

        // Add Foreign Key
        $this->addForeignKey(
            'ophcitheatreadmission_observations_crid_fk',
            'ophcitheatreadmission_observations',
            'checklist_result_id',
            'ophcitheatreadmission_admission_checklist_results',
            'id'
        );
    }

    public function down()
    {
        $this->dropOETable('ophcitheatreadmission_observations', true);
    }
}
