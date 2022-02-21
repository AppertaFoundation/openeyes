<?php

class m210621_080815_create_observation_widget_tables extends OEMigration
{
    public function safeUp()
    {
        // Archive the original et_ophciexamination_observations and et_ophciexamination_observations_version
        // tables, which are being split into two tables to allow for multiple observation entries
        $this->dropForeignKey('et_ophciexamination_obs_ev_fk', 'et_ophciexamination_observations');
        $this->dropForeignKey('et_ophciexamination_observations_cui_fk', 'et_ophciexamination_observations');
        $this->dropForeignKey('et_ophciexamination_observations_lmui_fk', 'et_ophciexamination_observations');

        $this->renameTable('et_ophciexamination_observations', 'archive_et_ophciexamination_observations');
        $this->renameTable('et_ophciexamination_observations_version', 'archive_et_ophciexamination_observations_version');

        $this->createOETable('et_ophciexamination_observations', array(
            'id' => 'pk',
            'event_id' => 'INT(10) UNSIGNED NOT NULL DEFAULT 0',
        ), true);

        $this->addForeignKey('et_ophciexamination_obs_ev_fk', 'et_ophciexamination_observations', 'event_id', 'event', 'id');

        $this->dbConnection->createCommand(
            'INSERT INTO et_ophciexamination_observations (id, event_id, last_modified_user_id, last_modified_date, created_user_id, created_date)'.
            ' SELECT id, event_id, last_modified_user_id, last_modified_date, created_user_id, created_date FROM archive_et_ophciexamination_observations'
        )->execute();

        $this->dbConnection->createCommand(
            'INSERT INTO et_ophciexamination_observations_version (id, event_id, last_modified_user_id, last_modified_date, created_user_id,'.
            ' created_date, version_date, version_id)'.
            ' SELECT id, event_id, last_modified_user_id, last_modified_date, created_user_id, created_date, version_date, version_id'.
            ' FROM archive_et_ophciexamination_observations_version'
        )->execute();

        $this->createOETable('ophciexamination_observation_entry', array(
            'id' => 'pk',
            'element_id' => 'INT(11) NOT NULL',
            'blood_pressure_systolic' => 'INT(3) UNSIGNED DEFAULT NULL',
            'blood_pressure_diastolic' => 'INT(3) UNSIGNED DEFAULT NULL',
            'o2_sat' => 'INT(3) UNSIGNED DEFAULT NULL',
            'blood_glucose' => 'VARCHAR(4) DEFAULT NULL',
            'hba1c' => 'INT(4) unsigned DEFAULT NULL',
            'height' => 'VARCHAR(5) DEFAULT NULL',
            'weight' => 'VARCHAR(5) DEFAULT NULL',
            'pulse' => 'INT(3) unsigned DEFAULT NULL',
            'temperature' => 'DECIMAL(5,2) DEFAULT NULL',
            'taken_at' => 'TIME DEFAULT NULL',
        ), true);

        $this->addForeignKey('ophciexamination_obs_ent_el_fk', 'ophciexamination_observation_entry', 'element_id', 'et_ophciexamination_observations', 'id');

        $this->dbConnection->createCommand(
            'INSERT INTO ophciexamination_observation_entry (element_id, blood_pressure_systolic, blood_pressure_diastolic, o2_sat, blood_glucose,'.
            ' hba1c, height, weight, pulse, temperature, taken_at, last_modified_user_id, last_modified_date, created_user_id, created_date)'.
            ' SELECT id, blood_pressure_systolic, blood_pressure_diastolic, o2_sat, blood_glucose, hba1c, height, weight, pulse, temperature, TIME(created_date),'.
            ' last_modified_user_id, last_modified_date, created_user_id, created_date FROM archive_et_ophciexamination_observations'
        )->execute();

        $this->dbConnection->createCommand(
            'INSERT INTO ophciexamination_observation_entry_version (id, element_id, blood_pressure_systolic, blood_pressure_diastolic, o2_sat, blood_glucose,'.
            ' hba1c, height, weight, pulse, temperature, taken_at, last_modified_user_id, last_modified_date, created_user_id, created_date,'.
            ' version_date, version_id)'.
            ' SELECT oe.id, a.id, a.blood_pressure_systolic, a.blood_pressure_diastolic, a.o2_sat, a.blood_glucose, a.hba1c, a.height,'.
            ' a.weight, a.pulse, a.temperature, TIME(a.created_date), a.last_modified_user_id, a.last_modified_date, a.created_user_id,'.
            ' a.created_date, a.version_date, a.version_id'.
            ' FROM archive_et_ophciexamination_observations_version a'.
            ' JOIN ophciexamination_observation_entry oe ON oe.element_id = a.id'
        )->execute();
    }

    public function down()
    {
        echo "m210621_080815_create_observation_widget_tables does not support migration down.\n";

        return false;
    }
}
