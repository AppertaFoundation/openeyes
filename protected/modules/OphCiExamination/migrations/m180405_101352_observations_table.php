<?php

class m180405_101352_observations_table extends OEMigration
{
    public function up()
    {
        $this->createOETable('et_ophciexamination_observations', array(
            'id' => 'pk',
            'event_id' => 'INT(10) UNSIGNED NOT NULL DEFAULT 0',
            'blood_pressure_systolic' => 'INT(3) UNSIGNED NOT NULL DEFAULT 0',
            'blood_pressure_diastolic' => 'INT(3) UNSIGNED NOT NULL DEFAULT 0',
            'o2_sat' => 'INT(3) UNSIGNED NOT NULL DEFAULT 0',
            'blood_glucose' => 'VARCHAR(4) NOT NULL DEFAULT 0.0',
            'hba1c' => 'INT(4) unsigned NOT NULL DEFAULT 0',
            'height' => 'VARCHAR(5) NOT NULL DEFAULT 0',
            'weight' => 'VARCHAR(5) NOT NULL DEFAULT 0',
            'pulse' => 'INT(3) unsigned NOT NULL DEFAULT 0',
        ), true);
        $this->addForeignKey('et_ophciexamination_obs_ev_fk', 'et_ophciexamination_observations', 'event_id', 'event', 'id');

        $this->createElementType('OphCiExamination', 'Observations', array(
           'class_name' => 'OEModule\OphCiExamination\models\Element_OphCiExamination_Observations',
           'display_order' => 12
        ));
    }

    public function down()
    {
        $this->dropOETable('et_ophciexamination_observations', true);
    }
}
