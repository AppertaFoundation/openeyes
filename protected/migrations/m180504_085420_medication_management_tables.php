<?php

class m180504_085420_medication_management_tables extends OEMigration
{
	public function up()
	{
        
        $this->createOETable('ref_medication', array(
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
        ), true);

        $this->createOETable('ref_medications_search_index', array(
            'id'                => 'pk',
            'ref_medication_id' => 'INT NULL',
            'alternative_term'    => 'TEXT NOT NULL',
        ), true);

        $this->createIndex('fk_ref_medication_idx', 'ref_medications_search_index', 'ref_medication_id');
        $this->addForeignKey('fk_ref_medication', 'ref_medications_search_index', 'ref_medication_id', 'ref_medication', 'id', 'NO ACTION' ,'NO ACTION');
        
        $this->createOETable('ref_set', array(
            'id'                    => 'pk',
            'name'                  => 'VARCHAR(255) NOT NULL',
            'antecedent_ref_set_id' => 'INT NULL',
            'deleted_date'          => 'DATE NULL',
            'display_order'         => 'INT NULL',
        ), true);
        
        $this->createIndex('fk_ref_set_idx', 'ref_set', 'antecedent_ref_set_id');
        $this->addForeignKey('fk_ref_set', 'ref_set', 'antecedent_ref_set_id', 'ref_set', 'id', 'NO ACTION' ,'NO ACTION');
        
        $this->createOETable('ref_medication_route', array(
            'id'                => 'pk',
            'term'              => 'VARCHAR(255) NULL',
            'code'              => 'VARCHAR(45) NULL',
            'source_type'       => 'VARCHAR(45) NULL',
            'source_subtype'    => 'VARCHAR(45) NULL',
            'deleted_date'      => 'DATE NULL',
        ), true);
        
        $this->createOETable('ref_medication_form', array(
            'id'                        => 'pk',
            'term'                      => 'VARCHAR(255) NULL',
            'code'                      => 'VARCHAR(45) NULL',
            'unit_term'                 => 'VARCHAR(255) NULL',
            'default_dose_unit_term'    => 'VARCHAR(255) NULL',
            'source_type'               => 'VARCHAR(45) NULL',
            'source_subtype'            => 'VARCHAR(45) NULL',
            'deleted_date'              => 'DATE NULL',
        ), true);
        
        $this->createOETable('ref_medication_frequency', array(
            'id'                => 'pk',
            'term'              => 'VARCHAR(255) NULL',
            'code'              => 'VARCHAR(45) NULL',
            'deleted_date'      => 'DATE NULL',
        ), true);
        
        $this->createOETable('ref_medication_set', array(
            'id'                        => 'pk',
            'ref_medication_id'         => 'INT NOT NULL',
            'ref_set_id'                => 'INT NOT NULL',
            'default_form'              => 'INT NULL',
            'default_dose'              => 'FLOAT NULL',
            'default_route'             => 'INT NULL',
            'default_frequency'         => 'INT NULL',
            'default_dose_unit_term'   => 'VARCHAR(255) NULL',
            'deleted_date'              => 'DATE NULL',
        ), true);
        
        $this->createIndex('fk_ref_medications_idx', 'ref_medication_set', 'ref_medication_id');
        $this->createIndex('fk_ref_set_idx', 'ref_medication_set', 'ref_set_id');
        $this->createIndex('fk_default_route_idx', 'ref_medication_set', 'default_route');
        $this->createIndex('fk_default_form_idx', 'ref_medication_set', 'default_form');
        $this->createIndex('fk_default_frequency_idx', 'ref_medication_set', 'default_frequency');
        
        $this->addForeignKey('fk_ref_medications', 'ref_medication_set', 'ref_medication_id', 'ref_medication', 'id', 'NO ACTION' ,'NO ACTION');
        
        $this->addForeignKey('fk_ref_set_2', 'ref_medication_set', 'ref_set_id', 'ref_set', 'id', 'NO ACTION' ,'NO ACTION');
        $this->addForeignKey('fk_default_route', 'ref_medication_set', 'default_route', 'ref_medication_route', 'id', 'NO ACTION' ,'NO ACTION');
        $this->addForeignKey('fk_default_form', 'ref_medication_set', 'default_form', 'ref_medication_form', 'id', 'NO ACTION' ,'NO ACTION');
        $this->addForeignKey('fk_default_frequency', 'ref_medication_set', 'default_frequency', 'ref_medication_frequency', 'id', 'NO ACTION' ,'NO ACTION');
        
        $this->createOETable('ref_medication_dose', array(
            'id'    => 'pk',
            'name'  => 'VARCHAR(255) NULL',
        ), true);
        
     
        $this->createOETable('event_medication_uses', array(
            'id'                            => 'pk',
            'event_id'                      => 'INT unsigned NOT NULL',
            'copied_from_med_use_id'        => 'INT unsigned NULL',
            'first_prescribed_med_use_id'   => 'INT NULL',
            'usage_type'                    => 'VARCHAR(45) NOT NULL',
            'usage_subtype'                 => 'VARCHAR(45) NULL',
            'ref_medication_id'             => 'INT NOT NULL',
            'form_id'                       => 'INT NULL',
            'laterality'                    => 'INT NULL',
            'dose'                          => 'FLOAT NULL',
            'dose_unit_term'                => 'VARCHAR(255) NULL',
            'route_id'                      => 'INT NULL',
            'frequency_id'                  => 'INT NULL',
            'duration'                      => 'INT(10) NULL',
            'dispense_location_id'          => 'INT NULL',
            'dispense_condition_id'         => 'INT NULL',
            'start_date_string_YYYYMMDD'    =>  'VARCHAR(8) NOT NULL',
            'end_date_string_YYYYMMDD'      =>'VARCHAR(8) NULL',

        ), true);
        
        $this->createIndex('fk_ref_medication_idx', 'event_medication_uses', 'ref_medication_id');
        $this->createIndex('fk_form_id_idx', 'event_medication_uses', 'form_id');
        $this->createIndex('fk_route_idx', 'event_medication_uses', 'route_id');
        $this->createIndex('fk_frequency_idx', 'event_medication_uses', 'frequency_id');
        $this->createIndex('fk_event_1_idx', 'event_medication_uses', 'event_id');
        $this->createIndex('fk_event_2_idx', 'event_medication_uses', 'copied_from_med_use_id');
       
        $this->addForeignKey('fk_ref_medication_2', 'event_medication_uses', 'ref_medication_id', 'ref_medication', 'id', 'NO ACTION' ,'NO ACTION');
        $this->addForeignKey('fk_form', 'event_medication_uses', 'form_id', 'ref_medication_form', 'id', 'NO ACTION' ,'NO ACTION');
        $this->addForeignKey('fk_route', 'event_medication_uses', 'route_id', 'ref_medication_route', 'id', 'NO ACTION' ,'NO ACTION');
        $this->addForeignKey('fk_frequency', 'event_medication_uses', 'frequency_id', 'ref_medication_frequency', 'id', 'NO ACTION' ,'NO ACTION');
        $this->addForeignKey('fk_event_1', 'event_medication_uses', 'event_id', 'event', 'id', 'NO ACTION' ,'NO ACTION');
        $this->addForeignKey('fk_event_2', 'event_medication_uses', 'copied_from_med_use_id', 'event', 'id', 'NO ACTION' ,'NO ACTION');
        
        $this->createOETable('ref_set_rules', array(
            'id'                => 'pk',
            'ref_set_id'        => 'INT NOT NULL',
            'subspecialty_id'   => 'INT NULL',
            'site_id'           => 'INT NULL',
            'usage_code'        => 'VARCHAR(255) NULL',
            'deleted_date'      => 'DATE NULL',
        ), true);
        
        $this->createIndex('fk_ref_set_idx', 'ref_set_rules', 'ref_set_id');
        $this->addForeignKey('fk_ref_set_3', 'ref_set_rules', 'ref_set_id', 'ref_set', 'id', 'NO ACTION' ,'NO ACTION');
        
	}

	public function down()
	{
        $this->dropForeignKey('fk_ref_set_3', 'ref_set_rules');
        $this->dropIndex('fk_ref_set_idx', 'ref_set_rules');
        $this->dropOETable('ref_set_rules', true);
        
        $this->dropForeignKey('fk_ref_medication_2', 'event_medication_uses');
        $this->dropForeignKey('fk_form', 'event_medication_uses');
        $this->dropForeignKey('fk_route', 'event_medication_uses');
        $this->dropForeignKey('fk_frequency', 'event_medication_uses');
        $this->dropForeignKey('fk_event_1', 'event_medication_uses');
        $this->dropForeignKey('fk_event_2', 'event_medication_uses');
        $this->dropIndex('fk_ref_medication_idx', 'event_medication_uses');
        $this->dropIndex('fk_form_id_idx', 'event_medication_uses');
        $this->dropIndex('fk_route_idx', 'event_medication_uses');
        $this->dropIndex('fk_frequency_idx', 'event_medication_uses');
        $this->dropIndex('fk_event_1_idx', 'event_medication_uses');
        $this->dropIndex('fk_event_2_idx', 'event_medication_uses');
        $this->dropOETable('event_medication_uses', true);
        
        $this->dropOETable('ref_medication_dose', true);
        
        $this->dropForeignKey('fk_ref_medications', 'ref_medication_set');
        $this->dropForeignKey('fk_ref_set_2', 'ref_medication_set');
        $this->dropForeignKey('fk_default_route', 'ref_medication_set');
        $this->dropForeignKey('fk_default_form', 'ref_medication_set');
        $this->dropForeignKey('fk_default_frequency', 'ref_medication_set');  
        $this->dropIndex('fk_ref_medications_idx', 'ref_medication_set');
        $this->dropIndex('fk_ref_set_idx', 'ref_medication_set');
        $this->dropIndex('fk_default_route_idx', 'ref_medication_set');
        $this->dropIndex('fk_default_form_idx', 'ref_medication_set');
        $this->dropIndex('fk_default_frequency_idx', 'ref_medication_set');
        $this->dropOETable('ref_medication_set', true);
        
        $this->dropOETable('ref_medication_route', true);
        $this->dropOETable('ref_medication_form', true);
        $this->dropOETable('ref_medication_frequency', true);
       
        $this->dropForeignKey('fk_ref_set', 'ref_set');
        $this->dropIndex('fk_ref_set_idx', 'ref_set');
        $this->dropOETable('ref_set', true);
       
        $this->dropForeignKey('fk_ref_medication', 'ref_medications_search_index');
        $this->dropIndex('fk_ref_medication_idx', 'ref_medications_search_index');
        $this->dropOETable('ref_medications_search_index', true);
       
        $this->dropOETable('ref_medication', true);
       
	}

}