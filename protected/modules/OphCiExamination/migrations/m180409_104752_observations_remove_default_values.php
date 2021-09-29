<?php

class m180409_104752_observations_remove_default_values extends OEMigration
{
    public function up()
    {
        $this->alterColumn('et_ophciexamination_observations', 'blood_pressure_systolic', 'INT(3) UNSIGNED DEFAULT NULL');
        $this->alterColumn('et_ophciexamination_observations', 'blood_pressure_diastolic', 'INT(3) UNSIGNED DEFAULT NULL');
        $this->alterColumn('et_ophciexamination_observations', 'o2_sat', 'INT(3) UNSIGNED DEFAULT NULL');
        $this->alterColumn('et_ophciexamination_observations', 'blood_glucose', 'VARCHAR(4) DEFAULT NULL');
        $this->alterColumn('et_ophciexamination_observations', 'hba1c', 'INT(4) UNSIGNED DEFAULT NULL');
        $this->alterColumn('et_ophciexamination_observations', 'height', 'VARCHAR(5) DEFAULT NULL');
        $this->alterColumn('et_ophciexamination_observations', 'weight', 'VARCHAR(5) DEFAULT NULL');
        $this->alterColumn('et_ophciexamination_observations', 'pulse', 'INT(3) UNSIGNED DEFAULT NULL');

        $this->update('element_type', array('display_order' => 11), "name = 'Observations' AND display_order = 12");
    }

    public function down()
    {
        $this->alterColumn('et_ophciexamination_observations', 'blood_pressure_systolic', 'INT(3) UNSIGNED NOT NULL DEFAULT 0');
        $this->alterColumn('et_ophciexamination_observations', 'blood_pressure_diastolic', 'INT(3) UNSIGNED NOT NULL DEFAULT 0');
        $this->alterColumn('et_ophciexamination_observations', 'o2_sat', 'INT(3) UNSIGNED NOT NULL DEFAULT 0');
        $this->alterColumn('et_ophciexamination_observations', 'blood_glucose', 'VARCHAR(4) NOT NULL DEFAULT 0.0');
        $this->alterColumn('et_ophciexamination_observations', 'hba1c', 'INT(4) UNSIGNED NOT NULL DEFAULT 0');
        $this->alterColumn('et_ophciexamination_observations', 'height', 'VARCHAR(5) NOT NULL DEFAULT 0.0');
        $this->alterColumn('et_ophciexamination_observations', 'weight', 'VARCHAR(5) NOT NULL DEFAULT 0.0');
        $this->alterColumn('et_ophciexamination_observations', 'pulse', 'INT(3) UNSIGNED NOT NULL DEFAULT 0');

        $this->update('element_type', array('display_order' => 12), 'name = Observations AND display_order = 11');
    }
}
