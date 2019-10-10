<?php

class m171026_084312_add_FKs_to_examination_cxl_tables extends OEMigration
{
    public function up()
    {
        // et_ophciexamination_cxl_history
        $this->addForeignKey('et_ophciexamination_cxl_history_event', 'et_ophciexamination_cxl_history', 'event_id', 'event', 'id');
        $this->alterColumn('et_ophciexamination_cxl_history', 'eye_id', 'int(10) UNSIGNED DEFAULT 3');
        $this->alterColumn('et_ophciexamination_cxl_history_version', 'eye_id', 'int(10) UNSIGNED DEFAULT 3');
        $this->addForeignKey('et_ophciexamination_cxl_history_eye', 'et_ophciexamination_cxl_history', 'eye_id', 'eye', 'id');
        $this->addForeignKey('et_ophciexamination_cxl_history_ocular_surface_disease', 'et_ophciexamination_cxl_history', 'ocular_surface_disease_id', 'ophciexamination_cxl_ocular_surface_disease', 'id');

        //et_ophciexamination_cxl_outcome
        $this->addForeignKey('et_ophciexamination_cxl_outcome_event', 'et_ophciexamination_cxl_outcome', 'event_id', 'event', 'id');
        $this->alterColumn('et_ophciexamination_cxl_outcome', 'eye_id', 'int(10) UNSIGNED DEFAULT 3');
        $this->alterColumn('et_ophciexamination_cxl_outcome_version', 'eye_id', 'int(10) UNSIGNED DEFAULT 3');
        $this->addForeignKey('et_ophciexamination_cxl_outcomey_eye', 'et_ophciexamination_cxl_outcome', 'eye_id', 'eye', 'id');
        $this->addForeignKey('et_ophciexamination_cxl_outcomey_diagnosis', 'et_ophciexamination_cxl_outcome', 'diagnosis_id', 'ophciexamination_cxl_outcome_diagnosis', 'id');
        $this->addForeignKey('et_ophciexamination_cxl_outcomey_outcome', 'et_ophciexamination_cxl_outcome', 'outcome_id', 'ophciexamination_cxl_outcome', 'id');

        //et_ophciexamination_keratometry
        $this->addForeignKey('et_ophciexamination_keratometry_event', 'et_ophciexamination_keratometry', 'event_id', 'event', 'id');
        $this->alterColumn('et_ophciexamination_keratometry', 'eye_id', 'int(10) UNSIGNED DEFAULT 3');
        $this->alterColumn('et_ophciexamination_keratometry_version', 'eye_id', 'int(10) UNSIGNED DEFAULT 3');
        $this->addForeignKey('et_ophciexamination_keratometry_eye', 'et_ophciexamination_keratometry', 'eye_id', 'eye', 'id');
        $this->addForeignKey('et_ophciexamination_keratometry_tomographer', 'et_ophciexamination_keratometry', 'tomographer_id', 'ophciexamination_tomographer_device', 'id');

        $this->dropColumn('et_ophciexamination_keratometry', 'tomographer_scan_quality_id');
        $this->dropColumn('et_ophciexamination_keratometry_version', 'tomographer_scan_quality_id');

        $this->addForeignKey('et_ophciexamination_keratometry_rgf', 'et_ophciexamination_keratometry', 'right_quality_front', 'ophciexamination_cxl_quality_score', 'id');
        $this->addForeignKey('et_ophciexamination_keratometry_rgb', 'et_ophciexamination_keratometry', 'right_quality_back', 'ophciexamination_cxl_quality_score', 'id');
        $this->addForeignKey('et_ophciexamination_keratometry_lgf', 'et_ophciexamination_keratometry', 'left_quality_front', 'ophciexamination_cxl_quality_score', 'id');
        $this->addForeignKey('et_ophciexamination_keratometry_lgb', 'et_ophciexamination_keratometry', 'left_quality_back', 'ophciexamination_cxl_quality_score', 'id');

        $this->addForeignKey('et_ophciexamination_keratometry_rclr', 'et_ophciexamination_keratometry', 'right_cl_removed', 'ophciexamination_cxl_cl_removed', 'id');
        $this->addForeignKey('et_ophciexamination_keratometry_lclr', 'et_ophciexamination_keratometry', 'left_cl_removed', 'ophciexamination_cxl_cl_removed', 'id');

        //et_ophtroperationnote_cxl
        $this->addForeignKey('et_ophtroperationnote_cxl_event', 'et_ophtroperationnote_cxl', 'event_id', 'event', 'id');
        $this->addForeignKey('et_ophtroperationnote_cxl_protocol', 'et_ophtroperationnote_cxl', 'protocol_id', 'ophtroperationnote_cxl_protocol', 'id');
        $this->alterColumn('et_ophtroperationnote_cxl', 'protocol_id', 'int(11)');
        $this->alterColumn('et_ophtroperationnote_cxl_version', 'protocol_id', 'int(11)');

        $this->alterColumn('et_ophtroperationnote_cxl', 'epithelial_removal_method_id', 'int(11)');
        $this->alterColumn('et_ophtroperationnote_cxl_version', 'epithelial_removal_method_id', 'int(11)');
        $this->addForeignKey('et_ophtroperationnote_cxl_ephrm', 'et_ophtroperationnote_cxl', 'epithelial_removal_method_id', 'ophtroperationnote_cxl_epithelial_removal_method', 'id');

        $this->alterColumn('et_ophtroperationnote_cxl', 'epithelial_removal_diameter_id', 'int(11)');
        $this->alterColumn('et_ophtroperationnote_cxl_version', 'epithelial_removal_diameter_id', 'int(11)');
        $this->addForeignKey('et_ophtroperationnote_cxl_eprd', 'et_ophtroperationnote_cxl', 'epithelial_removal_diameter_id', 'ophtroperationnote_cxl_epithelial_removal_diameter', 'id');

        $this->alterColumn('et_ophtroperationnote_cxl', 'riboflavin_preparation_id', 'int(11)');
        $this->alterColumn('et_ophtroperationnote_cxl_version', 'riboflavin_preparation_id', 'int(11)');
        $this->addForeignKey('et_ophtroperationnote_cxl_prep', 'et_ophtroperationnote_cxl', 'riboflavin_preparation_id', 'ophtroperationnote_cxl_riboflavin_preparation', 'id');

        $this->alterColumn('et_ophtroperationnote_cxl', 'interval_between_drops_id', 'int(11)');
        $this->alterColumn('et_ophtroperationnote_cxl_version', 'interval_between_drops_id', 'int(11)');
        $this->addForeignKey('et_ophtroperationnote_cxl_bdrops', 'et_ophtroperationnote_cxl', 'interval_between_drops_id', 'ophtroperationnote_cxl_interval_between_drops', 'id');

        $this->alterColumn('et_ophtroperationnote_cxl', 'soak_duration_range_id', 'int(11)');
        $this->alterColumn('et_ophtroperationnote_cxl_version', 'soak_duration_range_id', 'int(11)');
        $this->addForeignKey('et_ophtroperationnote_cxl_soakd', 'et_ophtroperationnote_cxl', 'soak_duration_range_id', 'ophtroperationnote_cxl_soak_duration', 'id');

        $this->alterColumn('et_ophtroperationnote_cxl', 'uv_irradiance_range_id', 'int(11)');
        $this->alterColumn('et_ophtroperationnote_cxl_version', 'uv_irradiance_range_id', 'int(11)');
        $this->addForeignKey('et_ophtroperationnote_cxl_irrange', 'et_ophtroperationnote_cxl', 'uv_irradiance_range_id', 'ophtroperationnote_cxl_uv_irradiance', 'id');

        $this->alterColumn('et_ophtroperationnote_cxl', 'total_exposure_time_id', 'int(11)');
        $this->alterColumn('et_ophtroperationnote_cxl_version', 'total_exposure_time_id', 'int(11)');
        $this->addForeignKey('et_ophtroperationnote_cxl_totalexp', 'et_ophtroperationnote_cxl', 'total_exposure_time_id', 'ophtroperationnote_cxl_total_exposure_time', 'id');

        $this->alterColumn('et_ophtroperationnote_cxl', 'uv_pulse_duration_id', 'int(11)');
        $this->alterColumn('et_ophtroperationnote_cxl_version', 'uv_pulse_duration_id', 'int(11)');
        $this->addForeignKey('et_ophtroperationnote_cxl_pulsedur', 'et_ophtroperationnote_cxl', 'uv_pulse_duration_id', 'ophtroperationnote_cxl_uv_pulse_duration', 'id');

        $this->alterColumn('et_ophtroperationnote_cxl', 'interpulse_duration_id', 'int(11)');
        $this->alterColumn('et_ophtroperationnote_cxl_version', 'interpulse_duration_id', 'int(11)');
        $this->addForeignKey('et_ophtroperationnote_cxl_interpulsedur', 'et_ophtroperationnote_cxl', 'interpulse_duration_id', 'ophtroperationnote_cxl_interpulse_duration', 'id');

        $this->alterColumn('et_ophtroperationnote_cxl', 'device_id', 'int(11)');
        $this->alterColumn('et_ophtroperationnote_cxl_version', 'device_id', 'int(11)');
        $this->addForeignKey('et_ophtroperationnote_cxl_device', 'et_ophtroperationnote_cxl', 'device_id', 'ophtroperationnote_cxl_devices', 'id');

        $this->alterColumn('et_ophtroperationnote_cxl', 'iontophoresis_id', 'int(11)');
        $this->alterColumn('et_ophtroperationnote_cxl_version', 'iontophoresis_id', 'int(11)');
        $this->addForeignKey('et_ophtroperationnote_cxl_ion', 'et_ophtroperationnote_cxl', 'iontophoresis_id', 'ophtroperationnote_cxl_iontophoresis', 'id');

        $this->alterColumn('et_ophtroperationnote_cxl', 'mitomycin_c', 'int(11)');
        $this->alterColumn('et_ophtroperationnote_cxl_version', 'mitomycin_c', 'int(11)');
        $this->addForeignKey('et_ophtroperationnote_cxl_mitomycin', 'et_ophtroperationnote_cxl', 'mitomycin_c', 'ophtroperationnote_cxl_mitomycin', 'id');

        $this->alterColumn('et_ophtroperationnote_cxl', 'epithelial_status_id', 'int(11)');
        $this->alterColumn('et_ophtroperationnote_cxl_version', 'epithelial_status_id', 'int(11)');
        $this->addForeignKey('et_ophtroperationnote_cxl_epith', 'et_ophtroperationnote_cxl', 'epithelial_status_id', 'ophtroperationnote_cxl_epithelial_status', 'id');
    }

    public function down()
    {
        // et_ophciexamination_cxl_history
        $this->dropForeignKey('et_ophciexamination_cxl_history_event', 'et_ophciexamination_cxl_history');
        $this->dropForeignKey('et_ophciexamination_cxl_history_eye', 'et_ophciexamination_cxl_history');
        $this->alterColumn('et_ophciexamination_cxl_history', 'eye_id', 'int(11) SIGNED DEFAULT 3');
        $this->alterColumn('et_ophciexamination_cxl_history_version', 'eye_id', 'int(11) SIGNED DEFAULT 3');
        $this->dropForeignKey('et_ophciexamination_cxl_history_ocular_surface_disease', 'et_ophciexamination_cxl_history');

        //et_ophciexamination_cxl_outcome
        $this->dropForeignKey('et_ophciexamination_cxl_outcome_event', 'et_ophciexamination_cxl_outcome');
        $this->dropForeignKey('et_ophciexamination_cxl_outcomey_eye', 'et_ophciexamination_cxl_outcome');
        $this->alterColumn('et_ophciexamination_cxl_outcome', 'eye_id', 'int(11) SIGNED DEFAULT 3');
        $this->alterColumn('et_ophciexamination_cxl_outcome_version', 'eye_id', 'int(11) SIGNED DEFAULT 3');
        $this->dropForeignKey('et_ophciexamination_cxl_outcomey_diagnosis', 'et_ophciexamination_cxl_outcome');
        $this->dropForeignKey('et_ophciexamination_cxl_outcomey_outcome', 'et_ophciexamination_cxl_outcome');

        //et_ophciexamination_keratometry
        $this->dropForeignKey('et_ophciexamination_keratometry_event', 'et_ophciexamination_keratometry');
        $this->dropForeignKey('et_ophciexamination_keratometry_eye', 'et_ophciexamination_keratometry');
        $this->alterColumn('et_ophciexamination_keratometry', 'eye_id', 'int(11) SIGNED DEFAULT 3');
        $this->alterColumn('et_ophciexamination_keratometry_version', 'eye_id', 'int(11) SIGNED DEFAULT 3');

        $this->dropForeignKey('et_ophciexamination_keratometry_tomographer', 'et_ophciexamination_keratometry');

        $this->addColumn('et_ophciexamination_keratometry', 'tomographer_scan_quality_id', 'INT(10)');
        $this->addColumn('et_ophciexamination_keratometry_version', 'tomographer_scan_quality_id', 'INT(10)');

        $this->dropForeignKey('et_ophciexamination_keratometry_rgf', 'et_ophciexamination_keratometry');
        $this->dropForeignKey('et_ophciexamination_keratometry_rgb', 'et_ophciexamination_keratometry');
        $this->dropForeignKey('et_ophciexamination_keratometry_lgf', 'et_ophciexamination_keratometry');
        $this->dropForeignKey('et_ophciexamination_keratometry_lgb', 'et_ophciexamination_keratometry');

        $this->dropForeignKey('et_ophciexamination_keratometry_rclr', 'et_ophciexamination_keratometry');
        $this->dropForeignKey('et_ophciexamination_keratometry_lclr', 'et_ophciexamination_keratometry');

        //et_ophtroperationnote_cxl
        $this->dropForeignKey('et_ophtroperationnote_cxl_event', 'et_ophtroperationnote_cxl');
        $this->dropForeignKey('et_ophtroperationnote_cxl_protocol', 'et_ophtroperationnote_cxl');
        $this->alterColumn('et_ophtroperationnote_cxl', 'protocol_id', 'int(10)');
        $this->alterColumn('et_ophtroperationnote_cxl_version', 'protocol_id', 'int(10)');

        $this->dropForeignKey('et_ophtroperationnote_cxl_ephrm', 'et_ophtroperationnote_cxl');
        $this->alterColumn('et_ophtroperationnote_cxl', 'epithelial_removal_method_id', 'int(10)');

        $this->dropForeignKey('et_ophtroperationnote_cxl_eprd', 'et_ophtroperationnote_cxl');
        $this->alterColumn('et_ophtroperationnote_cxl', 'epithelial_removal_diameter_id', 'int(10)');
        $this->alterColumn('et_ophtroperationnote_cxl_version', 'epithelial_removal_diameter_id', 'int(10)');

        $this->dropForeignKey('et_ophtroperationnote_cxl_prep', 'et_ophtroperationnote_cxl');
        $this->alterColumn('et_ophtroperationnote_cxl', 'riboflavin_preparation_id', 'int(10)');
        $this->alterColumn('et_ophtroperationnote_cxl_version', 'riboflavin_preparation_id', 'int(10)');

        $this->dropForeignKey('et_ophtroperationnote_cxl_bdrops', 'et_ophtroperationnote_cxl');
        $this->alterColumn('et_ophtroperationnote_cxl', 'interval_between_drops_id', 'int(10)');
        $this->alterColumn('et_ophtroperationnote_cxl_version', 'interval_between_drops_id', 'int(10)');

        $this->dropForeignKey('et_ophtroperationnote_cxl_soakd', 'et_ophtroperationnote_cxl');
        $this->alterColumn('et_ophtroperationnote_cxl', 'soak_duration_range_id', 'int(10)');
        $this->alterColumn('et_ophtroperationnote_cxl_version', 'soak_duration_range_id', 'int(10)');

        $this->dropForeignKey('et_ophtroperationnote_cxl_irrange', 'et_ophtroperationnote_cxl');
        $this->alterColumn('et_ophtroperationnote_cxl', 'uv_irradiance_range_id', 'int(10)');
        $this->alterColumn('et_ophtroperationnote_cxl_version', 'uv_irradiance_range_id', 'int(10)');

        $this->dropForeignKey('et_ophtroperationnote_cxl_totalexp', 'et_ophtroperationnote_cxl');
        $this->alterColumn('et_ophtroperationnote_cxl', 'total_exposure_time_id', 'int(10)');
        $this->alterColumn('et_ophtroperationnote_cxl_version', 'total_exposure_time_id', 'int(10)');

        $this->dropForeignKey('et_ophtroperationnote_cxl_pulsedur', 'et_ophtroperationnote_cxl');
        $this->alterColumn('et_ophtroperationnote_cxl', 'uv_pulse_duration_id', 'int(10)');
        $this->alterColumn('et_ophtroperationnote_cxl_version', 'uv_pulse_duration_id', 'int(10)');

        $this->dropForeignKey('et_ophtroperationnote_cxl_interpulsedur', 'et_ophtroperationnote_cxl');
        $this->alterColumn('et_ophtroperationnote_cxl', 'interpulse_duration_id', 'int(10)');
        $this->alterColumn('et_ophtroperationnote_cxl_version', 'interpulse_duration_id', 'int(10)');

        $this->dropForeignKey('et_ophtroperationnote_cxl_device', 'et_ophtroperationnote_cxl');
        $this->alterColumn('et_ophtroperationnote_cxl', 'device_id', 'int(10)');
        $this->alterColumn('et_ophtroperationnote_cxl_version', 'device_id', 'int(10)');

        $this->dropForeignKey('et_ophtroperationnote_cxl_ion', 'et_ophtroperationnote_cxl');
        $this->alterColumn('et_ophtroperationnote_cxl', 'iontophoresis_id', 'int(10)');
        $this->alterColumn('et_ophtroperationnote_cxl_version', 'iontophoresis_id', 'int(10)');

        $this->dropForeignKey('et_ophtroperationnote_cxl_mitomycin', 'et_ophtroperationnote_cxl');
        $this->alterColumn('et_ophtroperationnote_cxl', 'mitomycin_c', 'int(10)');
        $this->alterColumn('et_ophtroperationnote_cxl_version', 'mitomycin_c', 'int(10)');

        $this->dropForeignKey('et_ophtroperationnote_cxl_epith', 'et_ophtroperationnote_cxl');
        $this->alterColumn('et_ophtroperationnote_cxl', 'epithelial_status_id', 'int(10)');
        $this->alterColumn('et_ophtroperationnote_cxl_version', 'epithelial_status_id', 'int(10)');
    }
}