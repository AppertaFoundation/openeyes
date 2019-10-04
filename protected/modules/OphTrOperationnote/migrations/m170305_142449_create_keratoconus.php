<?php

class m170305_142449_create_keratoconus extends OEMigration
{
    public function up()
    {
        $this->createOETable('et_ophciexamination_cxl_history', array(
            'id' => 'pk',
            'event_id' => 'int(10) unsigned NOT NULL',
            'right_previous_cxl_value' => 'int(10)',
            'right_previous_refractive_value' => 'int(10)',
            'right_intacs_kera_ring_value' => 'int(10)',
            'right_trans_prk_value' => 'int(10)',
            'right_previous_hsk_keratitis_value' => 'int(10)',
            'left_previous_cxl_value' => 'int(10)',
            'left_previous_refractive_value' => 'int(10)',
            'left_intacs_kera_ring_value' => 'int(10)',
            'left_trans_prk_value' => 'int(10)',
            'left_previous_hsk_keratitis_value' => 'int(10)',
            'asthma_id' => 'int(10)',
            'eczema_id' => 'int(10)',
            'eye_rubber_id' => 'int(10)',
            'hayfever_id' => 'int(10)',
            'eye_id' => 'int(10)',
            'ocular_surface_disease_id' => 'int(10)'
        ), true);

        $this->createOETable('et_ophciexamination_cxl_outcome', array(
            'id' => 'pk',
            'event_id' => 'int(10) unsigned NOT NULL',
            'eye_id' => 'int(10)',
            'diagnosis_id' => 'int(10)',
            'outcome_id' => 'int(10)'
        ), true);

        $this->createOETable('et_ophciexamination_keratometry', array(
            'id' => 'pk',
            'event_id' => 'int(10) unsigned NOT NULL',
            'eye_id' => 'int(10)',
            'right_anterior_k1_value' => 'DECIMAL(5,2)',
            'right_axis_anterior_k1_value' => 'DECIMAL(5,2)',
            'right_anterior_k2_value' => 'DECIMAL(5,2)',
            'right_axis_anterior_k2_value' => 'DECIMAL(5,2)',
            'right_kmax_value' => 'DECIMAL(5,2)',
            'left_anterior_k1_value' => 'DECIMAL(5,2)',
            'left_axis_anterior_k1_value' => 'DECIMAL(5,2)',
            'left_anterior_k2_value' => 'DECIMAL(5,2)',
            'left_axis_anterior_k2_value' => 'DECIMAL(5,2)',
            'left_kmax_value' => 'DECIMAL(5,2)',
            'tomographer_id' => 'int(10)',
            'tomographer_scan_quality_id' => 'int(10)',
            'right_posterior_k2_value' => 'DECIMAL(5,2)',
            'right_thinnest_point_pachymetry_value' => 'int(10)',
            'right_ba_index_value' => 'DECIMAL(5,2)',
            'left_posterior_k2_value' => 'DECIMAL(5,2)',
            'left_thinnest_point_pachymetry_value' => 'int(10)',
            'left_ba_index_value' => 'DECIMAL(5,2)',
            'right_quality_front' => 'int(10)',
            'right_quality_back' => 'int(10)',
            'left_quality_front' => 'int(10)',
            'left_quality_back' => 'int(10)',
            'right_cl_removed' => 'int(10)',
            'left_cl_removed' => 'int(10)',
            'right_flourescein_value' => 'int(1)',
            'left_flourescein_value' => 'int(1)'
        ), true);

        $this->createOETable('et_ophciexamination_slit_lamp', array(
            'id' => 'pk',
            'event_id' => 'int(10) unsigned NOT NULL',
            'eye_id' => 'int(10)',
            'right_allergic_conjunctivitis_id' => 'int(10)',
            'right_blepharitis_id' => 'int(10)',
            'right_dry_eye_id' => 'int(10)',
            'right_cornea_id' => 'int(10)',
            'left_allergic_conjunctivitis_id' => 'int(10)',
            'left_blepharitis_id' => 'int(10)',
            'left_dry_eye_id' => 'int(10)',
            'left_cornea_id' => 'int(10)'
        ), true);

        $this->createOETable('et_ophciexamination_specular_microscopy', array(
            'id' => 'pk',
            'event_id' => 'int(10) unsigned NOT NULL',
            'eye_id' => 'int(10)',
            'scan_quality_id' => 'int(10)',
            'specular_microscope_id' => 'int(10)',
            'right_endothelial_cell_density_value' => 'int(10)',
            'right_coefficient_variation_value' => 'dec(5,2)',
            'left_endothelial_cell_density_value' => 'int(10)',
            'left_coefficient_variation_value' => 'dec(5,2)'
        ), true);

        $this->createOETable('et_ophtroperationnote_cxl', array(
            'id' => 'pk',
            'event_id' => 'int(10) unsigned NOT NULL',
            'protocol_id' => 'int(10)',
            'epithelial_removal_method_id' => 'int(10)',
            'epithelial_removal_diameter_id' => 'int(10)',
            'riboflavin_preparation_id' => 'int(10)',
            'interval_between_drops_id' => 'dec(5,2)',
            'soak_duration_range_id' => 'int(10)',
            'uv_irradiance_range_id' => 'dec(5,2)',
            'total_exposure_time_id' => 'int(10)',
            'uv_pulse_duration_id' => 'int(10)',
            'interpulse_duration_id' => 'int(10)',
            'uv_total_energy_value' => 'int(10)',
            'device_id' => 'int(10)',
            'iontophoresis_id' => 'int(10)',
            'iontophoresis_current_value' => 'int(10)',
            'iontophoresis_duration_value' => 'int(10)',
            'cxl_comments' => 'VARCHAR(1024)'
        ), true);

        $this->createOETable('ophciexamination_cxl_cl_removed', array(
            'id' => 'pk',
            'name' => 'VARCHAR(128)',
            'display_order' => 'int(10)'
        ), true);

        $this->createOETable('ophciexamination_cxl_ocular_surface_disease', array(
            'id' => 'pk',
            'name' => 'VARCHAR(128)',
            'display_order' => 'int(10)'
        ), true);

        $this->createOETable('ophciexamination_cxl_outcome', array(
            'id' => 'pk',
            'name' => 'varchar(128)',
            'display_order' => 'int(10)'
        ), true);

        $this->createOETable('ophciexamination_cxl_outcome_diagnosis', array(
            'id' => 'pk',
            'name' => 'varchar(128)',
            'display_order' => 'int(10)'
        ), true);

        $this->createOETable('ophciexamination_cxl_quality_score', array(
            'id' => 'pk',
            'name' => 'VARCHAR(128)',
            'display_order' => 'int(10)'
        ), true);

        $this->createOETable('ophciexamination_scan_quality', array(
            'id' => 'pk',
            'name' => 'VARCHAR(128)',
            'display_order' => 'int(10)'
        ), true);

        $this->createOETable('ophciexamination_slit_lamp_conditions', array(
            'id' => 'pk',
            'name' => 'VARCHAR(128)',
            'display_order' => 'int(10)'
        ), true);

        $this->createOETable('ophciexamination_slit_lamp_cornea', array(
            'id' => 'pk',
            'name' => 'varchar(128)',
            'display_order' => 'int(10)'
        ), true);

        $this->createOETable('ophciexamination_specular_microscope', array(
            'id' => 'pk',
            'name' => 'VARCHAR(128)',
            'display_order' => 'int(10)'
        ), true);

        $this->createOETable('ophciexamination_tomographer_device', array(
            'id' => 'pk',
            'name' => 'VARCHAR(128)',
            'display_order' => 'int(10)'
        ), true);

        $this->createOETable('ophciexamination_topographer_device', array(
            'id' => 'pk',
            'name' => 'VARCHAR(128)',
            'display_order' => 'int(10)'
        ), true);

        $this->createOETable('ophtroperationnote_cxl_complications', array(
            'id' => 'pk',
            'name' => 'VARCHAR(128)',
            'display_order' => 'int(10)',
            'active' => 'tinyint(1)'
        ), true);

        $this->createOETable('ophtroperationnote_cxl_devices', array(
            'id' => 'pk',
            'name' => 'VARCHAR(128)',
            'display_order' => 'int(10)',
            'defaultChoice' => 'int(11)'
        ), true);

        $this->createOETable('ophtroperationnote_cxl_epithelial_removal_diameter', array(
            'id' => 'pk',
            'name' => 'VARCHAR(128)',
            'display_order' => 'int(10)',
            'defaultChoice' => 'int(11)'
        ), true);

        $this->createOETable('ophtroperationnote_cxl_epithelial_removal_method', array(
            'id' => 'pk',
            'name' => 'VARCHAR(128)',
            'display_order' => 'int(10)',
            'defaultChoice' => 'int(11)'
        ), true);

        $this->createOETable('ophtroperationnote_cxl_interpulse_duration', array(
            'id' => 'pk',
            'name' => 'VARCHAR(128)',
            'display_order' => 'int(10)',
            'defaultChoice' => 'int(11)'
        ), true);

        $this->createOETable('ophtroperationnote_cxl_interval_between_drops', array(
            'id' => 'pk',
            'name' => 'VARCHAR(128)',
            'display_order' => 'int(10)',
            'defaultChoice' => 'int(11)'
        ), true);

        $this->createOETable('ophtroperationnote_cxl_iontophoresis', array(
            'id' => 'pk',
            'name' => 'VARCHAR(128)',
            'display_order' => 'int(10)'
        ), true);

        $this->createOETable('ophtroperationnote_cxl_protocol', array(
            'id' => 'pk',
            'name' => 'VARCHAR(128)',
            'display_order' => 'int(10)',
            'defaultChoice' => 'int(11)'
        ), true);

        $this->createOETable('ophtroperationnote_cxl_riboflavin_preparation', array(
            'id' => 'pk',
            'name' => 'VARCHAR(128)',
            'display_order' => 'int(10)',
            'defaultChoice' => 'int(11)'
        ), true);

        $this->createOETable('ophtroperationnote_cxl_soak_duration', array(
            'id' => 'pk',
            'name' => 'VARCHAR(128)',
            'display_order' => 'int(10)',
            'defaultChoice' => 'int(11)'
        ), true);

        $this->createOETable('ophtroperationnote_cxl_total_exposure_time', array(
            'id' => 'pk',
            'name' => 'int(10)',
            'display_order' => 'int(10)',
            'defaultChoice' => 'int(11)'
        ), true);

        $this->createOETable('ophtroperationnote_cxl_uv_irradiance', array(
            'id' => 'pk',
            'name' => 'int(10)',
            'display_order' => 'int(10)',
            'defaultChoice' => 'int(11)'
        ), true);

        $this->createOETable('ophtroperationnote_cxl_uv_pulse_duration', array(
            'id' => 'pk',
            'name' => 'int(10)',
            'display_order' => 'int(10)',
            'defaultChoice' => 'int(11)'
        ), true);

    }

    public function down()
    {

        $this->dropOETable('et_ophciexamination_cxl_history', true);
        $this->dropOETable('et_ophciexamination_cxl_outcome', true);
        $this->dropOETable('et_ophciexamination_keratometry', true);
        $this->dropOETable('et_ophciexamination_slit_lamp', true);
        $this->dropOETable('et_ophciexamination_specular_microscopy', true);
        $this->dropOETable('et_ophtroperationnote_cxl', true);
        $this->dropOETable('ophciexamination_scan_quality', true);
        $this->dropOETable('ophciexamination_slit_lamp_conditions', true);
        $this->dropOETable('ophciexamination_specular_microscope', true);
        $this->dropOETable('ophciexamination_cxl_cl_removed', true);
        $this->dropOETable('ocular_surface_disease', true);
        $this->dropOETable('ophciexamination_cxl_outcome', true);
        $this->dropOETable('ophciexamination_cxl_outcome_diagnosis', true);
        $this->dropOETable('ophciexamination_cxl_quality_score', true);
        $this->dropOETable('ophciexamination_slit_lamp_cornea', true);
        $this->dropOETable('ophciexamination_tomographer_device', true);
        $this->dropOETable('ophciexamination_topographer_device', true);
        $this->dropOETable('ophtroperationnote_cxl_complications', true);
        $this->dropOETable('ophtroperationnote_cxl_devices', true);
        $this->dropOETable('ophtroperationnote_cxl_iontophoresis', true);
        $this->dropOETable('ophtroperationnote_cxl_epithelial_removal_diameter', true);
        $this->dropOETable('ophtroperationnote_cxl_epithelial_removal_method', true);
        $this->dropOETable('ophtroperationnote_cxl_interpulse_duration', true);
        $this->dropOETable('ophtroperationnote_cxl_interval_between_drops', true);
        $this->dropOETable('ophtroperationnote_cxl_protocol', true);
        $this->dropOETable('ophtroperationnote_cxl_riboflavin_preparation', true);
        $this->dropOETable('ophtroperationnote_cxl_soak_duration', true);
        $this->dropOETable('ophtroperationnote_cxl_total_exposure_time', true);
        $this->dropOETable('ophtroperationnote_cxl_uv_irradiance', true);
        $this->dropOETable('ophtroperationnote_cxl_uv_pulse_duration', true);
    }

    /*
    // Use safeUp/safeDown to do migration with transaction
    public function safeUp()
    {
    }

    public function safeDown()
    {
    }
    */
}