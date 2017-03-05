<?php

class m170305_142449_create_keratoconus extends CDbMigration
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
            'hayfever_id' => 'int(10)',
            'eye_id' => 'int(10)',
            'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
            'created_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\''
        ),true);

        $this->createOETable('et_ophciexamination_cxl_history_version', array(
            'version_id' => 'pk',
            'version_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
            'id' => 'int(10) NOT NULL',
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
            'hayfever_id' => 'int(10)',
            'eye_id' => 'int(10)',
            'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
            'created_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\''
        ),true);

        $this->createOETable('et_ophciexamination_keratometry', array(
            'id' => 'pk',
            'event_id' => 'int(10) unsigned NOT NULL',
            'eye_id' => 'int(10)',
            'topographer_id' => 'int(10)',
            'topographer_scan_quality_id' => 'int(10)',
            'right_anterior_k1_value' => 'int(10)',
            'right_axis_anterior_k1_value' => 'int(10)',
            'right_anterior_k2_value' => 'int(10)',
            'right_axis_anterior_k2_value' => 'int(10)',
            'right_kmax_value' => 'int(10)',
            'left_anterior_k1_value' => 'int(10)',
            'left_axis_anterior_k1_value' => 'int(10)',
            'left_anterior_k2_value' => 'int(10)',
            'left_axis_anterior_k2_value' => 'int(10)',
            'left_kmax_value' => 'int(10)',
            'tomographer_id' => 'int(10)',
            'tomographer_scan_quality_id' => 'int(10)',
            'right_posterior_k2_value' => 'int(10)',
            'right_thinnest_point_pachymetry_value' => 'int(10)',
            'right_ba_index_value' => 'int(10)',
            'left_posterior_k2_value' => 'int(10)',
            'left_thinnest_point_pachymetry_value' => 'int(10)',
            'left_ba_index_value' => 'int(10)',
            'keratoconus_stage_id' => 'int(10)',
            'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
            'last_modified_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
            'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
            'created_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\''
        ),true);

        $this->createOETable('et_ophciexamination_keratometry_version', array(
            'version_id' => 'pk',
            'version_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
            'id' => 'int(10) NOT NULL',
            'event_id' => 'int(10) unsigned NOT NULL',
            'eye_id' => 'int(10)',
            'topographer_id' => 'int(10)',
            'topographer_scan_quality_id' => 'int(10)',
            'right_anterior_k1_value' => 'int(10)',
            'right_axis_anterior_k1_value' => 'int(10)',
            'right_anterior_k2_value' => 'int(10)',
            'right_axis_anterior_k2_value' => 'int(10)',
            'right_kmax_value' => 'int(10)',
            'left_anterior_k1_value' => 'int(10)',
            'left_axis_anterior_k1_value' => 'int(10)',
            'left_anterior_k2_value' => 'int(10)',
            'left_axis_anterior_k2_value' => 'int(10)',
            'left_kmax_value' => 'int(10)',
            'tomographer_id' => 'int(10)',
            'tomographer_scan_quality_id' => 'int(10)',
            'right_posterior_k2_value' => 'int(10)',
            'right_thinnest_point_pachymetry_value' => 'int(10)',
            'right_ba_index_value' => 'int(10)',
            'left_posterior_k2_value' => 'int(10)',
            'left_thinnest_point_pachymetry_value' => 'int(10)',
            'left_ba_index_value' => 'int(10)',
            'keratoconus_stage_id' => 'int(10)',
            'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
            'last_modified_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
            'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
            'created_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\''
        ),true);

        $this->createOETable('et_ophciexamination_slit_lamp', array(
            'id' => 'pk',
            'event_id' => 'int(10) unsigned NOT NULL',
            'eye_id' => 'int(10)',
            'allergic_conjunctivitis_id' => 'int(10)',
            'blepharitis_id' => 'int(10)',
            'dry_eye_id' => 'int(10)',
            'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
            'last_modified_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
            'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
            'created_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\''
        ),true);

        $this->createOETable('et_ophciexamination_slit_lamp_version', array(
            'version_id' => 'pk',
            'version_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
            'id' => 'int(10) NOT NULL',
            'event_id' => 'int(10) unsigned NOT NULL',
            'eye_id' => 'int(10)',
            'allergic_conjunctivitis_id' => 'int(10)',
            'blepharitis_id' => 'int(10)',
            'dry_eye_id' => 'int(10)',
            'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
            'last_modified_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
            'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
            'created_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\''
        ),true);

        $this->createOETable('et_ophciexamination_specular_microscopy', array(
            'id' => 'pk',
            'event_id' => 'int(10) unsigned NOT NULL',
            'eye_id' => 'int(10)',
            'scan_quality_id' => 'int(10)',
            'specular_microscope_id' => 'int(10)',
            'right_endothelial_cell_density_value' => 'int(10)',
            'right_coefficient_variation_value' => 'dec(5,2)',
            'left_endothelial_cell_density_value' => 'int(10)',
            'left_coefficient_variation_value' => 'dec(5,2)',
            'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
            'last_modified_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
            'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
            'created_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\''
        ),true);

        $this->createOETable('et_ophciexamination_specular_microscopy_version', array(
            'version_id' => 'pk',
            'version_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
            'id' => 'int(10) NOT NULL',
            'event_id' => 'int(10) unsigned NOT NULL',
            'eye_id' => 'int(10)',
            'scan_quality_id' => 'int(10)',
            'specular_microscope_id' => 'int(10)',
            'right_endothelial_cell_density_value' => 'int(10)',
            'right_coefficient_variation_value' => 'dec(5,2)',
            'left_endothelial_cell_density_value' => 'int(10)',
            'left_coefficient_variation_value' => 'dec(5,2)',
            'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
            'last_modified_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
            'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
            'created_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\''
        ),true);

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
            'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
            'last_modified_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
            'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
            'created_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\''
        ),true);

        $this->createOETable('et_ophtroperationnote_cxl_version', array(
            'version_id' => 'pk',
            'version_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
            'id' => 'int(10) NOT NULL',
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
            'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
            'last_modified_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
            'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
            'created_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\''
        ),true);

        $this->createOETable('ophciexamination_keratoconus_stage', array(
            'id' => 'pk',
            'name' => 'VARCHAR(128)',
            'display_order' => 'int(10)',
            'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
            'last_modified_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
            'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
            'created_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\''
        ),true);

        $this->createOETable('ophciexamination_scan_quality', array(
            'id' => 'pk',
            'name' => 'VARCHAR(128)',
            'display_order' => 'int(10)',
            'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
            'last_modified_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
            'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
            'created_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\''
        ),true);

        $this->createOETable('ophciexamination_slit_lamp_conditions', array(
            'id' => 'pk',
            'name' => 'VARCHAR(128)',
            'display_order' => 'int(10)',
            'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
            'last_modified_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
            'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
            'created_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\''
        ),true);

        $this->createOETable('ophciexamination_specular_microscope', array(
            'id' => 'pk',
            'name' => 'VARCHAR(128)',
            'display_order' => 'int(10)',
            'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
            'last_modified_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
            'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
            'created_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\''
        ),true);

        $this->createOETable('ophtroperationnote_cxl_epithelial_removal_diameter', array(
            'id' => 'pk',
            'name' => 'VARCHAR(128)',
            'display_order' => 'int(10)',
            'defaultChoice' => 'int(11)',
            'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
            'last_modified_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
            'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
            'created_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\''
        ),true);

        $this->createOETable('ophtroperationnote_cxl_epithelial_removal_diameter_version', array(
            'version_id' => 'pk',
            'version_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
            'id' => 'int(10) NOT NULL',
            'name' => 'VARCHAR(128)',
            'display_order' => 'int(10)',
            'defaultChoice' => 'int(11)',
            'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
            'last_modified_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
            'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
            'created_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\''
        ),true);

        $this->createOETable('ophtroperationnote_cxl_epithelial_removal_method', array(
            'id' => 'pk',
            'name' => 'VARCHAR(128)',
            'display_order' => 'int(10)',
            'defaultChoice' => 'int(11)',
            'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
            'last_modified_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
            'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
            'created_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\''
        ),true);

        $this->createOETable('ophtroperationnote_cxl_epithelial_removal_method_version', array(
            'version_id' => 'pk',
            'version_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
            'id' => 'int(10) NOT NULL',
            'name' => 'VARCHAR(128)',
            'display_order' => 'int(10)',
            'defaultChoice' => 'int(11)',
            'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
            'last_modified_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
            'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
            'created_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\''
        ),true);

        $this->createOETable('ophtroperationnote_cxl_interpulse_duration', array(
            'id' => 'pk',
            'name' => 'VARCHAR(128)',
            'display_order' => 'int(10)',
            'defaultChoice' => 'int(11)',
            'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
            'last_modified_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
            'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
            'created_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\''
        ),true);

        $this->createOETable('ophtroperationnote_cxl_interpulse_duration_version', array(
            'version_id' => 'pk',
            'version_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
            'id' => 'int(10) NOT NULL',
            'name' => 'VARCHAR(128)',
            'display_order' => 'int(10)',
            'defaultChoice' => 'int(11)',
            'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
            'last_modified_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
            'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
            'created_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\''
        ),true);

        $this->createOETable('ophtroperationnote_cxl_interval_between_drops', array(
            'id' => 'pk',
            'name' => 'VARCHAR(128)',
            'display_order' => 'int(10)',
            'defaultChoice' => 'int(11)',
            'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
            'last_modified_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
            'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
            'created_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\''
        ),true);

        $this->createOETable('ophtroperationnote_cxl_interval_between_drops_version', array(
            'version_id' => 'pk',
            'version_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
            'id' => 'int(10) NOT NULL',
            'name' => 'VARCHAR(128)',
            'display_order' => 'int(10)',
            'defaultChoice' => 'int(11)',
            'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
            'last_modified_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
            'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
            'created_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\''
        ),true);

        $this->createOETable('ophtroperationnote_cxl_protocol', array(
            'id' => 'pk',
            'name' => 'VARCHAR(128)',
            'display_order' => 'int(10)',
            'defaultChoice' => 'int(11)',
            'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
            'last_modified_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
            'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
            'created_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\''
        ),true);

        $this->createOETable('ophtroperationnote_cxl_protocol_version', array(
            'version_id' => 'pk',
            'version_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
            'id' => 'int(10) NOT NULL',
            'name' => 'VARCHAR(128)',
            'display_order' => 'int(10)',
            'defaultChoice' => 'int(11)',
            'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
            'last_modified_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
            'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
            'created_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\''
        ),true);

        $this->createOETable('ophtroperationnote_cxl_riboflavin_preparation', array(
            'id' => 'pk',
            'name' => 'VARCHAR(128)',
            'display_order' => 'int(10)',
            'defaultChoice' => 'int(11)',
            'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
            'last_modified_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
            'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
            'created_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\''
        ),true);

        $this->createOETable('ophtroperationnote_cxl_riboflavin_preparation_version', array(
            'version_id' => 'pk',
            'version_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
            'id' => 'int(10) NOT NULL',
            'name' => 'VARCHAR(128)',
            'display_order' => 'int(10)',
            'defaultChoice' => 'int(11)',
            'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
            'last_modified_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
            'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
            'created_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\''
        ),true);

        $this->createOETable('ophtroperationnote_cxl_soak_duration', array(
            'id' => 'pk',
            'name' => 'VARCHAR(128)',
            'display_order' => 'int(10)',
            'defaultChoice' => 'int(11)',
            'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
            'last_modified_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
            'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
            'created_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\''
        ),true);

        $this->createOETable('ophtroperationnote_cxl_soak_duration_version', array(
            'version_id' => 'pk',
            'version_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
            'id' => 'int(10) NOT NULL',
            'name' => 'VARCHAR(128)',
            'display_order' => 'int(10)',
            'defaultChoice' => 'int(11)',
            'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
            'last_modified_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
            'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
            'created_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\''
        ),true);

        $this->createOETable('ophtroperationnote_cxl_total_exposure_time', array(
            'id' => 'pk',
            'name' => 'VARCHAR(128)',
            'display_order' => 'int(10)',
            'defaultChoice' => 'int(11)',
            'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
            'last_modified_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
            'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
            'created_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\''
        ),true);

        $this->createOETable('ophtroperationnote_cxl_total_exposure_time_version', array(
            'version_id' => 'pk',
            'version_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
            'id' => 'int(10) NOT NULL',
            'name' => 'VARCHAR(128)',
            'display_order' => 'int(10)',
            'defaultChoice' => 'int(11)',
            'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
            'last_modified_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
            'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
            'created_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\''
        ),true);

        $this->createOETable('ophtroperationnote_cxl_uv_irradiance', array(
            'id' => 'pk',
            'name' => 'int(10)',
            'display_order' => 'int(10)',
            'defaultChoice' => 'int(11)',
            'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
            'last_modified_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
            'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
            'created_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\''
        ),true);

        $this->createOETable('ophtroperationnote_cxl_uv_irradiance_version', array(
            'version_id' => 'pk',
            'version_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
            'id' => 'int(10) NOT NULL',
            'name' => 'INT(10)',
            'display_order' => 'int(10)',
            'defaultChoice' => 'int(11)',
            'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
            'last_modified_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
            'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
            'created_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\''
        ),true);

        $this->createOETable('ophtroperationnote_cxl_uv_pulse_duration', array(
            'id' => 'pk',
            'name' => 'int(10)',
            'display_order' => 'int(10)',
            'defaultChoice' => 'int(11)',
            'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
            'last_modified_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
            'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
            'created_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\''
        ),true);

        $this->createOETable('ophtroperationnote_cxl_uv_pulse_duration_version', array(
            'version_id' => 'pk',
            'version_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
            'id' => 'int(10) NOT NULL',
            'name' => 'INT(10)',
            'display_order' => 'int(10)',
            'defaultChoice' => 'int(11)',
            'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
            'last_modified_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
            'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
            'created_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\''
        ),true);
    }

    public function down()
    {

        $this->dropTable('et_ophciexamination_cxl_history');
        $this->dropTable('et_ophciexamination_cxl_history_version');
        $this->dropTable('et_ophciexamination_keratometry');
        $this->dropTable('et_ophciexamination_keratometry_version');
        $this->dropTable('et_ophciexamination_slit_lamp');
        $this->dropTable('et_ophciexamination_slit_lamp_version');
        $this->dropTable('et_ophciexamination_specular_microscopy');
        $this->dropTable('et_ophciexamination_specular_microscopy_version');
        $this->dropTable('et_ophtroperationnote_cxl');
        $this->dropTable('et_ophtroperationnote_cxl_version');
        $this->dropTable('ophciexamination_keratoconus_stage');
        $this->dropTable('ophciexamination_scan_quality');
        $this->dropTable('ophciexamination_slit_lamp_conditions');
        $this->dropTable('ophciexamination_specular_microscope');
        $this->dropTable('ophtroperationnote_cxl_epithelial_removal_diameter');
        $this->dropTable('ophtroperationnote_cxl_epithelial_removal_diameter_version');
        $this->dropTable('ophtroperationnote_cxl_epithelial_removal_method');
        $this->dropTable('ophtroperationnote_cxl_epithelial_removal_method_version');
        $this->dropTable('ophtroperationnote_cxl_interpulse_duration');
        $this->dropTable('ophtroperationnote_cxl_interpulse_duration_version');
        $this->dropTable('ophtroperationnote_cxl_interval_between_drops');
        $this->dropTable('ophtroperationnote_cxl_interval_between_drops_version');
        $this->dropTable('ophtroperationnote_cxl_protocol');
        $this->dropTable('ophtroperationnote_cxl_protocol_version');
        $this->dropTable('ophtroperationnote_cxl_riboflavin_preparation');
        $this->dropTable('ophtroperationnote_cxl_riboflavin_preparation_version');
        $this->dropTable('ophtroperationnote_cxl_soak_duration');
        $this->dropTable('ophtroperationnote_cxl_soak_duration_version');
        $this->dropTable('ophtroperationnote_cxl_total_exposure_time');
        $this->dropTable('ophtroperationnote_cxl_total_exposure_time_version');
        $this->dropTable('ophtroperationnote_cxl_uv_irradiance');
        $this->dropTable('ophtroperationnote_cxl_uv_irradiance_version');
        $this->dropTable('ophtroperationnote_cxl_uv_pulse_duration');
        $this->dropTable('ophtroperationnote_cxl_uv_pulse_duration_version');
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