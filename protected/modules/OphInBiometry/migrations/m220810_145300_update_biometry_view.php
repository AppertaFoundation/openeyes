<?php

class m220810_145300_update_biometry_view extends OEMigration
{
    public function up()
    {
        $this->dbConnection->createCommand("
        CREATE OR REPLACE VIEW et_ophtroperationnote_biometry AS
        SELECT
            `eol`.`id` AS `id`,
            `eol`.`eye_id` AS `eye_id`,
            `eol`.`last_modified_date` AS `last_modified_date`,
            `eoc`.`target_refraction_left` AS `target_refraction_left`,
            `eoc`.`target_refraction_right` AS `target_refraction_right`,
            `eos`.`lens_id_left` AS `lens_id_left`,
            `oll`.`name` AS 'lens_left',
            coalesce(nullif(`oll`.`display_name`, ''), `oll`.`description`) AS 'lens_display_name_left',
            `oll`.`description` AS 'lens_description_left',
            eoirvl.constant AS 'lens_acon_left',
            `eos`.`lens_id_right` AS `lens_id_right`,
            `olr`.`name` AS 'lens_right',
            coalesce(nullif(`olr`.`display_name`, ''), `olr`.`description`) AS 'lens_display_name_right',
            `olr`.`description` AS 'lens_description_right',
            eoirvr.constant AS 'lens_acon_right',
            `eol`.`k1_left` AS `k1_left`,
            `eol`.`k1_right` AS `k1_right`,
            `eol`.`k2_left` AS `k2_left`,
            `eol`.`k2_right` AS `k2_right`,
            `eol`.`k1_axis_left` AS `k1_axis_left`,
            `eol`.`k1_axis_right` AS `k1_axis_right`,
            `eol`.`axial_length_left` AS `axial_length_left`,
            `eol`.`axial_length_right` AS `axial_length_right`,
            `eol`.`snr_left` AS `snr_left`,
            `eol`.`snr_right` AS `snr_right`,
            `eos`.`iol_power_left` AS `iol_power_left`,
            `eos`.`iol_power_right` AS `iol_power_right`,
            `eos`.`predicted_refraction_left` AS `predicted_refraction_left`,
            `eos`.`predicted_refraction_right` AS `predicted_refraction_right`,
            `ep`.`patient_id` AS `patient_id`,
            `eol`.`k2_axis_left` AS `k2_axis_left`,
            `eol`.`k2_axis_right` AS `k2_axis_right`,
            `eol`.`delta_k_left` AS `delta_k_left`,
            `eol`.`delta_k_right` AS `delta_k_right`,
            `eol`.`delta_k_axis_left` AS `delta_k_axis_left`,
            `eol`.`delta_k_axis_right` AS `delta_k_axis_right`,
            `eol`.`acd_left` AS `acd_left`,
            `eol`.`acd_right` AS `acd_right`,
            `oesl`.`name` AS 'status_left',
            `oesr`.`name` AS 'status_right',
            `eoc`.`comments` AS `comments`,
            `eoc`.`event_id` AS `event_id`,
            `ocfl`.`name` AS 'formula_left',
            `ocfr`.`name` AS 'formula_right'
        FROM
            `et_ophinbiometry_measurement` `eol`
        JOIN `et_ophinbiometry_calculation` `eoc` ON
                `eoc`.`event_id` = `eol`.`event_id`
        LEFT JOIN `et_ophinbiometry_selection` `eos` ON
                `eos`.`event_id` = `eol`.`event_id`
        JOIN `event` `ev` ON
                `ev`.`id` = `eol`.`event_id`
        JOIN `episode` `ep` ON
                `ep`.`id` = `ev`.`episode_id`
        LEFT JOIN ophinbiometry_lenstype_lens oll ON
            oll.id = eos.lens_id_left
        LEFT JOIN ophinbiometry_lenstype_lens olr ON
            olr.id = eos.lens_id_right
        LEFT JOIN et_ophinbiometry_iol_ref_values eoirvl ON
            eoirvl.event_id = eos.event_id
            AND eoirvl.eye_id = eos.eye_id
            AND eoirvl.formula_id = eos.formula_id_left
            AND eoirvl.lens_id = eos.lens_id_left
        LEFT JOIN et_ophinbiometry_iol_ref_values eoirvr ON
            eoirvr.event_id = eos.event_id
            AND eoirvr.eye_id = eos.eye_id
            AND eoirvr.formula_id = eos.formula_id_right
            AND eoirvr.lens_id = eos.lens_id_right
        LEFT JOIN dicom_eye_status oesl ON
            oesl.id = eol.eye_status_left
        LEFT JOIN dicom_eye_status oesr ON
            oesr.id = eol.eye_status_right
        LEFT JOIN ophinbiometry_calculation_formula ocfl ON
            ocfl.id = eos.formula_id_left
        LEFT JOIN ophinbiometry_calculation_formula ocfr ON
            ocfr.id = eos.formula_id_right
        ")->execute();
    }

    public function down()
    {
        $this->dbConnection->createCommand("
        CREATE OR REPLACE VIEW et_ophtroperationnote_biometry AS
        select
            `eol`.`id` AS `id`,
            `eol`.`eye_id` AS `eye_id`,
            `eol`.`last_modified_date` AS `last_modified_date`,
            `eoc`.`target_refraction_left` AS `target_refraction_left`,
            `eoc`.`target_refraction_right` AS `target_refraction_right`,
            `eos`.`lens_id_left` AS `lens_id_left`,
            (
            select
                `oll`.`name`
            from
                `ophinbiometry_lenstype_lens` `oll`
            where
                `oll`.`id` = `eos`.`lens_id_left`) AS `lens_left`,
            (
            select
                coalesce(nullif(`oll`.`display_name`, ''), `oll`.`description`)
            from
                `ophinbiometry_lenstype_lens` `oll`
            where
                `oll`.`id` = `eos`.`lens_id_left`) AS `lens_display_name_left`,
            (
            select
                `oll`.`description`
            from
                `ophinbiometry_lenstype_lens` `oll`
            where
                `oll`.`id` = `eos`.`lens_id_left`) AS `lens_description_left`,
            (
            select
                `eoirv`.`constant`
            from
                `et_ophinbiometry_iol_ref_values` `eoirv`
            where
                `eoirv`.`event_id` = `eol`.`event_id`
                and `eoirv`.`lens_id` = `eos`.`lens_id_left`
                and `eoirv`.`formula_id` = `eos`.`formula_id_left`
            limit 1) AS `lens_acon_left`,
            `eos`.`lens_id_right` AS `lens_id_right`,
            (
            select
                `oll`.`name`
            from
                `ophinbiometry_lenstype_lens` `oll`
            where
                `oll`.`id` = `eos`.`lens_id_right`) AS `lens_right`,
            (
            select
                coalesce(nullif(`oll`.`display_name`, ''), `oll`.`description`)
            from
                `ophinbiometry_lenstype_lens` `oll`
            where
                `oll`.`id` = `eos`.`lens_id_right`) AS `lens_display_name_right`,
            (
            select
                `oll`.`description`
            from
                `ophinbiometry_lenstype_lens` `oll`
            where
                `oll`.`id` = `eos`.`lens_id_right`) AS `lens_description_right`,
            (
            select
                `eoirv`.`constant`
            from
                `et_ophinbiometry_iol_ref_values` `eoirv`
            where
                `eoirv`.`event_id` = `eol`.`event_id`
                and `eoirv`.`lens_id` = `eos`.`lens_id_right`
                and `eoirv`.`formula_id` = `eos`.`formula_id_right`
            limit 1) AS `lens_acon_right`,
            `eol`.`k1_left` AS `k1_left`,
            `eol`.`k1_right` AS `k1_right`,
            `eol`.`k2_left` AS `k2_left`,
            `eol`.`k2_right` AS `k2_right`,
            `eol`.`k1_axis_left` AS `k1_axis_left`,
            `eol`.`k1_axis_right` AS `k1_axis_right`,
            `eol`.`axial_length_left` AS `axial_length_left`,
            `eol`.`axial_length_right` AS `axial_length_right`,
            `eol`.`snr_left` AS `snr_left`,
            `eol`.`snr_right` AS `snr_right`,
            `eos`.`iol_power_left` AS `iol_power_left`,
            `eos`.`iol_power_right` AS `iol_power_right`,
            `eos`.`predicted_refraction_left` AS `predicted_refraction_left`,
            `eos`.`predicted_refraction_right` AS `predicted_refraction_right`,
            `ep`.`patient_id` AS `patient_id`,
            `eol`.`k2_axis_left` AS `k2_axis_left`,
            `eol`.`k2_axis_right` AS `k2_axis_right`,
            `eol`.`delta_k_left` AS `delta_k_left`,
            `eol`.`delta_k_right` AS `delta_k_right`,
            `eol`.`delta_k_axis_left` AS `delta_k_axis_left`,
            `eol`.`delta_k_axis_right` AS `delta_k_axis_right`,
            `eol`.`acd_left` AS `acd_left`,
            `eol`.`acd_right` AS `acd_right`,
            (
            select
                `oes`.`name`
            from
                `dicom_eye_status` `oes`
            where
                `oes`.`id` = `eol`.`eye_status_left`) AS `status_left`,
            (
            select
                `oes`.`name`
            from
                `dicom_eye_status` `oes`
            where
                `oes`.`id` = `eol`.`eye_status_right`) AS `status_right`,
            `eoc`.`comments` AS `comments`,
            `eoc`.`event_id` AS `event_id`,
            (
            select
                `ocf`.`name`
            from
                `ophinbiometry_calculation_formula` `ocf`
            where
                `ocf`.`id` = `eos`.`formula_id_left`) AS `formula_left`,
            (
            select
                `ocf`.`name`
            from
                `ophinbiometry_calculation_formula` `ocf`
            where
                `ocf`.`id` = `eos`.`formula_id_right`) AS `formula_right`
        from
            ((((`et_ophinbiometry_measurement` `eol`
        join `et_ophinbiometry_calculation` `eoc` on
            (`eoc`.`event_id` = `eol`.`event_id`))
        left join `et_ophinbiometry_selection` `eos` on
            (`eos`.`event_id` = `eol`.`event_id`))
        join `event` `ev` on
            (`ev`.`id` = `eol`.`event_id`))
        join `episode` `ep` on
            (`ep`.`id` = `ev`.`episode_id`))
        order by
            `eol`.`last_modified_date`;")->execute();
    }
}
