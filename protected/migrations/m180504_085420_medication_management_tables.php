<?php

class m180504_085420_medication_management_tables extends OEMigration
{
    public function up()
    {

        $this->execute("RENAME TABLE medication TO medication_old");

        $this->createOETable('medication', array(
            'id'                => 'pk',
            'source_type'       => 'VARCHAR(10) NOT NULL',
            'source_subtype'    => 'VARCHAR(10) NULL',
            'preferred_term'    => 'VARCHAR(255) NOT NULL',
            'preferred_code'    => 'VARCHAR(255) NOT NULL',
            'vtm_term'          => 'VARCHAR(255) NULL',
            'vtm_code'          => 'VARCHAR(255) NULL',
            'vmp_term'          => 'VARCHAR(255) NULL',
            'vmp_code'          => 'VARCHAR(255) NULL',
            'amp_term'          => 'VARCHAR(255) NULL',
            'amp_code'          => 'VARCHAR(255) NULL',
            'deleted_date'      => 'DATE NULL',
            'source_old_id'     => 'INT NULL',
            'default_route_id'      => 'INT NULL',
            'default_dose_unit_term'    => 'VARCHAR(255) NULL',
            'default_form_id'           => 'INT NULL',
            'is_prescribable'           => 'TINYINT(1) UNSIGNED NULL',
        ), true, "new_medication");

        $this->createIndex("idx_med_code", "medication", "preferred_code");
        $this->createIndex("idx_source_type", "medication", "source_type");
        $this->createIndex("idx_source_sub_type", "medication", "source_subtype");

        $this->createOETable('medication_search_index', array(
            'id'                => 'pk',
            'medication_id' => 'INT NULL',
            'alternative_term'    => 'TEXT NOT NULL',
        ), true);

        $this->createIndex('fk_ref_medication_idx', 'medication_search_index', 'medication_id');
        $this->addForeignKey('fk_ref_medication', 'medication_search_index', 'medication_id', 'medication', 'id', 'NO ACTION', 'NO ACTION');

        $this->createOETable('medication_set', array(
            'id'                    => 'pk',
            'name'                  => 'VARCHAR(255) NOT NULL',
            'antecedent_medication_set_id' => 'INT NULL',
            'deleted_date'          => 'DATE NULL',
            'display_order'         => 'INT NULL',
        ), true);

        $this->createIndex('fk_ref_set_idx', 'medication_set', 'antecedent_medication_set_id');
        $this->addForeignKey('fk_ref_set', 'medication_set', 'antecedent_medication_set_id', 'medication_set', 'id', 'NO ACTION', 'NO ACTION');

        $this->createOETable('medication_route', array(
            'id'                => 'pk',
            'term'              => 'VARCHAR(255) NULL',
            'code'              => 'VARCHAR(45) NULL',
            'source_type'       => 'VARCHAR(45) NULL',
            'source_subtype'    => 'VARCHAR(45) NULL',
            'deleted_date'      => 'DATE NULL',
        ), true);

        $this->createOETable('medication_form', array(
            'id'                        => 'pk',
            'term'                      => 'VARCHAR(255) NULL',
            'code'                      => 'VARCHAR(45) NULL',
            'unit_term'                 => 'VARCHAR(255) NULL',
            'default_dose_unit_term'    => 'VARCHAR(255) NULL',
            'source_type'               => 'VARCHAR(45) NULL',
            'source_subtype'            => 'VARCHAR(45) NULL',
            'deleted_date'              => 'DATE NULL',
        ), true);

        $this->addForeignKey('fkm_default_route', 'medication', 'default_route_id', 'medication_route', 'id', 'NO ACTION', 'NO ACTION');
        $this->addForeignKey('fkm_default_form', 'medication', 'default_form_id', 'medication_form', 'id', 'NO ACTION', 'NO ACTION');


        $this->createOETable('medication_frequency', array(
            'id'                => 'pk',
            'term'              => 'VARCHAR(255) NULL',
            'code'              => 'VARCHAR(45) NULL',
            'deleted_date'      => 'DATE NULL',
        ), true);

        $this->createOETable('medication_set_item', array(
            'id'                            => 'pk',
            'medication_id'                 => 'INT NOT NULL',
            'medication_set_id'             => 'INT NOT NULL',
            'default_form_id'               => 'INT NULL',
            'default_dose'                  => 'FLOAT NULL',
            'default_route_id'              => 'INT NULL',
            'default_dispense_location_id'  => 'INT NULL',
            'default_dispense_condition_id' => 'INT NULL',
            'default_frequency_id'          => 'INT NULL',
            'default_dose_unit_term'        => 'VARCHAR(255) NULL',
            'deleted_date'                  => 'DATE NULL',
            'default_duration_id'           => 'INT NULL'
        ), true);

        $this->createIndex('fk_ref_medications_idx', 'medication_set_item', 'medication_id');
        $this->createIndex('fk_ref_set_item_idx', 'medication_set_item', 'medication_set_id');
        $this->createIndex('fk_default_route_idx', 'medication_set_item', 'default_route_id');
        $this->createIndex('fk_default_form_idx', 'medication_set_item', 'default_form_id');
        $this->createIndex('fk_default_frequency_idx', 'medication_set_item', 'default_frequency_id');
        $this->createIndex('fk_default_duration_idx', 'medication_set_item', 'default_duration_id');

        $this->addForeignKey('fk_ref_medications', 'medication_set_item', 'medication_id', 'medication', 'id', 'NO ACTION', 'NO ACTION');

        $this->addForeignKey('fk_ref_set_2', 'medication_set_item', 'medication_set_id', 'medication_set', 'id', 'NO ACTION', 'NO ACTION');
        $this->addForeignKey('fk_default_route', 'medication_set_item', 'default_route_id', 'medication_route', 'id', 'NO ACTION', 'NO ACTION');
        $this->addForeignKey('fk_default_form', 'medication_set_item', 'default_form_id', 'medication_form', 'id', 'NO ACTION', 'NO ACTION');
        $this->addForeignKey('fk_default_frequency', 'medication_set_item', 'default_frequency_id', 'medication_frequency', 'id', 'NO ACTION', 'NO ACTION');
        $this->addForeignKey('fk_default_dispense_location', 'medication_set_item', 'default_dispense_location_id', 'ophdrprescription_dispense_location', 'id', 'NO ACTION', 'NO ACTION');
        $this->addForeignKey('fk_default_dispense_condition', 'medication_set_item', 'default_dispense_condition_id', 'ophdrprescription_dispense_condition', 'id', 'NO ACTION', 'NO ACTION');
        $this->addForeignKey('fk_duration', 'medication_set_item', 'default_duration_id', 'medication_duration', 'id');

        $this->createOETable('medication_dose', array(
            'id'    => 'pk',
            'name'  => 'VARCHAR(255) NULL',
        ), true);


        $this->createOETable('event_medication_use', array(
            'id'                            => 'pk',
            'event_id'                      => 'INT unsigned NOT NULL',
            'copied_from_med_use_id'        => 'INT unsigned NULL',
            'first_prescribed_med_use_id'   => 'INT NULL',
            'usage_type'                    => 'VARCHAR(45) NOT NULL',
            'usage_subtype'                 => 'VARCHAR(45) NULL',
            'medication_id'                 => 'INT NOT NULL',
            'form_id'                       => 'INT NULL',
            'laterality'                    => 'INT NULL',
            'dose'                          => 'FLOAT NULL',
            'dose_unit_term'                => 'VARCHAR(255) NULL',
            'route_id'                      => 'INT NULL',
            'frequency_id'                  => 'INT NULL',
            'duration'                      => 'INT(10) unsigned NULL',
            'dispense_location_id'          => 'INT NULL',
            'dispense_condition_id'         => 'INT NULL',
            'start_date'                    => 'date NOT NULL',
            'end_date'                      => 'date NULL',
        ), true);

        $this->createIndex('fk_ref_medication_idx', 'event_medication_use', 'medication_id');
        $this->createIndex('fk_form_id_idx', 'event_medication_use', 'form_id');
        $this->createIndex('fk_route_idx', 'event_medication_use', 'route_id');
        $this->createIndex('fk_frequency_idx', 'event_medication_use', 'frequency_id');
        $this->createIndex('fk_event_1_idx', 'event_medication_use', 'event_id');
        $this->createIndex('fk_event_2_idx', 'event_medication_use', 'copied_from_med_use_id');

        $this->addForeignKey('fk_ref_medication_2', 'event_medication_use', 'medication_id', 'medication', 'id', 'NO ACTION', 'NO ACTION');
        $this->addForeignKey('fk_form', 'event_medication_use', 'form_id', 'medication_form', 'id', 'NO ACTION', 'NO ACTION');
        $this->addForeignKey('fk_route', 'event_medication_use', 'route_id', 'medication_route', 'id', 'NO ACTION', 'NO ACTION');
        $this->addForeignKey('fk_frequency', 'event_medication_use', 'frequency_id', 'medication_frequency', 'id', 'NO ACTION', 'NO ACTION');
        $this->addForeignKey('fk_dispense_location', 'event_medication_use', 'dispense_location_id', 'ophdrprescription_dispense_location', 'id', 'NO ACTION', 'NO ACTION');
        $this->addForeignKey('fk_dispense_condition', 'event_medication_use', 'dispense_condition_id', 'ophdrprescription_dispense_condition', 'id', 'NO ACTION', 'NO ACTION');


        $this->addForeignKey('fk_event_1', 'event_medication_use', 'event_id', 'event', 'id', 'NO ACTION', 'NO ACTION');
        $this->addForeignKey('fk_event_2', 'event_medication_use', 'copied_from_med_use_id', 'event', 'id', 'NO ACTION', 'NO ACTION');
        $this->createOETable('medication_set_rule', [
            'id'                => 'pk',
            'medication_set_id'        => 'INT NOT NULL',
            'subspecialty_id'   => 'INT NULL',
            'site_id'           => 'INT NULL',
            'usage_code'        => 'VARCHAR(255) NULL',
            'usage_code_id'     => 'INT(11) DEFAULT NULL',
            'deleted_date'      => 'DATE NULL',
        ], true);

        $this->createIndex('fk_ref_set_rule_idx', 'medication_set_rule', 'medication_set_id');
        $this->addForeignKey('fk_ref_set_3', 'medication_set_rule', 'medication_set_id', 'medication_set', 'id', 'NO ACTION', 'NO ACTION');

        $this->createOETable('medication_usage_code', [
            'id' => 'pk',
            'usage_code' => 'VARCHAR(30)',
            'name' => 'VARCHAR(50)',
            'active' => 'TINYINT(1) NOT NULL DEFAULT 1',
            'hidden' => 'TINYINT(1) UNSIGNED DEFAULT NULL',
            'display_order' => 'TINYINT UNSIGNED DEFAULT NULL',
        ]);

        $this->addForeignKey('medication_set_rule_ibfk_1', 'medication_set_rule', 'usage_code_id', 'medication_usage_code', 'id');

        $this->insertMultiple('medication_usage_code', [
            ['usage_code' => 'COMMON_OPH', 'name' => 'Common Ophthalmic Drug Sets'],
            ['usage_code' => 'COMMON_SYSTEMIC', 'name' => 'Common Systemic Drug  Sets'],
            ['usage_code' => 'PRESCRIPTION_SET', 'name' => 'Prescription Drug Sets'],
            ['usage_code' => 'Drug', 'name' => 'Drug'],
            ['usage_code' => 'DrugTag', 'name' => 'Drug Tags'],
            ['usage_code' => 'Formulary', 'name' => 'Formulary Drugs'],
            ['usage_code' => 'MedicationDrug', 'name' => 'Medication Drug'],
            ['usage_code' => 'Management', 'name' => 'Management'],
        ]);
    }

    public function down()
    {
        $this->dropForeignKey('fk_ref_set_3', 'medication_set_rule');
        $this->dropIndex('fk_ref_set_idx', 'medication_set_rule');
        $this->dropOETable('medication_set_rule', true);

        $this->dropForeignKey('fk_ref_medication_2', 'event_medication_use');
        $this->dropForeignKey('fk_form', 'event_medication_use');
        $this->dropForeignKey('fk_route', 'event_medication_use');
        $this->dropForeignKey('fk_frequency', 'event_medication_use');
        $this->dropForeignKey('fk_event_1', 'event_medication_use');
        $this->dropForeignKey('fk_event_2', 'event_medication_use');
        $this->dropIndex('fk_ref_medication_idx', 'event_medication_use');
        $this->dropIndex('fk_form_id_idx', 'event_medication_use');
        $this->dropIndex('fk_route_idx', 'event_medication_use');
        $this->dropIndex('fk_frequency_idx', 'event_medication_use');
        $this->dropIndex('fk_event_1_idx', 'event_medication_use');
        $this->dropIndex('fk_event_2_idx', 'event_medication_use');
        $this->dropOETable('event_medication_use', true);

        $this->dropOETable('medication_dose', true);

        $this->dropForeignKey('fk_ref_medications', 'medication_set_item');
        $this->dropForeignKey('fk_ref_set_2', 'medication_set_item');
        $this->dropForeignKey('fk_default_route', 'medication_set_item');
        $this->dropForeignKey('fk_default_form', 'medication_set_item');
        $this->dropForeignKey('fk_default_frequency', 'medication_set_item');
        $this->dropIndex('fk_ref_medications_idx', 'medication_set_item');
        $this->dropIndex('fk_ref_set_idx', 'medication_set_item');
        $this->dropIndex('fk_default_route_idx', 'medication_set_item');
        $this->dropIndex('fk_default_form_idx', 'medication_set_item');
        $this->dropIndex('fk_default_frequency_idx', 'medication_set_item');
        $this->dropOETable('medication_set_item', true);

        $this->dropOETable('medication_route', true);
        $this->dropOETable('medication_form', true);
        $this->dropOETable('medication_frequency', true);

        $this->dropForeignKey('fk_ref_set', 'medication_set');
        $this->dropIndex('fk_ref_set_idx', 'medication_set');
        $this->dropOETable('medication_set', true);

        $this->dropForeignKey('fk_ref_medication', 'medication_search_index');
        $this->dropIndex('fk_ref_medication_idx', 'medication_search_index');
        $this->dropOETable('medication_search_index', true);

        $this->dropOETable('medication', true);
    }
}
