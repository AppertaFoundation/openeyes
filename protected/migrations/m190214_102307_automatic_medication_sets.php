<?php

class m190214_102307_automatic_medication_sets extends OEMigration
{
    public function up()
    {
        $this->addColumn('medication_set', 'automatic', 'BOOLEAN NOT NULL DEFAULT 0');
        $this->addColumn('medication_set_version', 'automatic', 'BOOLEAN NOT NULL DEFAULT 0');

        $this->createOETable('medication_set_auto_rule_attribute', array(
            'id' => 'pk',
            'medication_set_id' => 'INT NOT NULL',
            'medication_attribute_option_id' => 'INT NOT NULL'
        ), true);

        $this->addForeignKey('fk_msara_msid', 'medication_set_auto_rule_attribute', 'medication_set_id', 'medication_set', 'id');
        $this->addForeignKey('fk_msara_mattroptid', 'medication_set_auto_rule_attribute', 'medication_attribute_option_id', 'medication_attribute_option', 'id');

        $this->createOETable('medication_set_auto_rule_set_membership', array(
            'id' => 'pk',
            'target_medication_set_id' => 'INT NOT NULL',
            'source_medication_set_id' => 'INT NOT NULL'
        ), true);

        $this->addForeignKey('fk_msarsm_tmsid', 'medication_set_auto_rule_set_membership', 'target_medication_set_id', 'medication_set', 'id');
        $this->addForeignKey('fk_msarsm_smsid', 'medication_set_auto_rule_set_membership', 'source_medication_set_id', 'medication_set', 'id');

        $this->createOETable('medication_set_auto_rule_medication', array(
            'id' => 'pk',
            'medication_set_id' => 'INT NOT NULL',
            'medication_id' => 'INT NOT NULL',
            'include_parent' => 'TINYINT DEFAULT 0 NOT NULL',
            'include_children' => 'TINYINT DEFAULT 0 NOT NULL',

            'default_form_id'               => 'INT NULL',
            'default_dose'                  => 'FLOAT NULL',
            'default_route_id'              => 'INT NULL',
            'default_dispense_location_id'  => 'INT NULL',
            'default_dispense_condition_id' => 'INT NULL',
            'default_frequency_id'          => 'INT NULL',
            'default_dose_unit_term'        => 'VARCHAR(255) NULL',
            'default_duration_id'           => 'INT NULL'
            
        ), true);

        $this->addForeignKey('fk_msarm_msid', 'medication_set_auto_rule_medication', 'medication_set_id', 'medication_set', 'id');
        $this->addForeignKey('fk_msarm_mid', 'medication_set_auto_rule_medication', 'medication_id', 'medication', 'id');

        $this->addForeignKey('fk_msarm_default_route', 'medication_set_auto_rule_medication', 'default_route_id', 'medication_route', 'id', 'NO ACTION', 'NO ACTION');
        $this->addForeignKey('fk_msarm_default_form', 'medication_set_auto_rule_medication', 'default_form_id', 'medication_form', 'id', 'NO ACTION', 'NO ACTION');
        $this->addForeignKey('fk_msarm_default_frequency', 'medication_set_auto_rule_medication', 'default_frequency_id', 'medication_frequency', 'id', 'NO ACTION', 'NO ACTION');
        $this->addForeignKey('fk_msarm_default_dispense_location', 'medication_set_auto_rule_medication', 'default_dispense_location_id', 'ophdrprescription_dispense_location', 'id', 'NO ACTION', 'NO ACTION');
        $this->addForeignKey('fk_msarm_default_dispense_condition', 'medication_set_auto_rule_medication', 'default_dispense_condition_id', 'ophdrprescription_dispense_condition', 'id', 'NO ACTION', 'NO ACTION');
        $this->addForeignKey('fk_msarm_duration', 'medication_set_auto_rule_medication', 'default_duration_id', 'medication_duration', 'id');

        $this->createOETable('medication_set_auto_rule_medication_taper', array(
            'id' => 'pk',
            'medication_set_auto_rule_id' => 'int NOT NULL',
            'dose' => 'FLOAT',
            'frequency_id' => 'INT NOT NULL',
            'duration_id' => 'INT NOT NULL'
                ), true);

        $this->addForeignKey('fk_msarm_med_id', 'medication_set_auto_rule_medication_taper', 'medication_set_auto_rule_id', 'medication_set_auto_rule_medication', 'id');
        $this->addForeignKey('fk_msarm_freq_id', 'medication_set_auto_rule_medication_taper', 'frequency_id', 'medication_frequency', 'id');
        $this->addForeignKey('fk_msarm_duration_id', 'medication_set_auto_rule_medication_taper', 'duration_id', 'medication_duration', 'id');
    }

    public function down()
    {
        $this->dropOETable('medication_set_auto_rule_medication', true);
        $this->dropOETable('medication_set_auto_rule_set_membership', true);
        $this->dropOETable('medication_set_auto_rule_attribute', true);
    }
}
