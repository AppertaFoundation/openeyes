<?php

class m200414_154048_observations_version_remove_default_values extends OEMigration
{
    public function safeUp()
    {
        $this->alterColumn('et_ophciexamination_observations_version', 'blood_pressure_systolic', 'INT(3) UNSIGNED DEFAULT NULL');
        $this->alterColumn('et_ophciexamination_observations_version', 'blood_pressure_diastolic', 'INT(3) UNSIGNED DEFAULT NULL');
        $this->alterColumn('et_ophciexamination_observations_version', 'o2_sat', 'INT(3) UNSIGNED DEFAULT NULL');
        $this->alterColumn('et_ophciexamination_observations_version', 'blood_glucose', 'VARCHAR(4) DEFAULT NULL');
        $this->alterColumn('et_ophciexamination_observations_version', 'hba1c', 'INT(4) UNSIGNED DEFAULT NULL');
        $this->alterColumn('et_ophciexamination_observations_version', 'height', 'VARCHAR(5) DEFAULT NULL');
        $this->alterColumn('et_ophciexamination_observations_version', 'weight', 'VARCHAR(5) DEFAULT NULL');
        $this->alterColumn('et_ophciexamination_observations_version', 'pulse', 'INT(3) UNSIGNED DEFAULT NULL');
    }

    public function safeDown()
    {
        $this->alterColumn('et_ophciexamination_observations_version', 'blood_pressure_systolic', 'INT(3) UNSIGNED NOT NULL DEFAULT 0');
        $this->alterColumn('et_ophciexamination_observations_version', 'blood_pressure_diastolic', 'INT(3) UNSIGNED NOT NULL DEFAULT 0');
        $this->alterColumn('et_ophciexamination_observations_version', 'o2_sat', 'INT(3) UNSIGNED NOT NULL DEFAULT 0');
        $this->alterColumn('et_ophciexamination_observations_version', 'blood_glucose', 'VARCHAR(4) NOT NULL DEFAULT 0.0');
        $this->alterColumn('et_ophciexamination_observations_version', 'hba1c', 'INT(4) UNSIGNED NOT NULL DEFAULT 0');
        $this->alterColumn('et_ophciexamination_observations_version', 'height', 'VARCHAR(5) NOT NULL DEFAULT 0.0');
        $this->alterColumn('et_ophciexamination_observations_version', 'weight', 'VARCHAR(5) NOT NULL DEFAULT 0.0');
        $this->alterColumn('et_ophciexamination_observations_version', 'pulse', 'INT(3) UNSIGNED NOT NULL DEFAULT 0');
    }
}
