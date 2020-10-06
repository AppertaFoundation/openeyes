<?php

class m200903_035926_rename_axis_k1_eyeside_field extends CDbMigration
{
    public function up()
    {
        $this->renameColumn('et_ophinbiometry_measurement', 'axis_k1_left', 'k1_axis_left');
        $this->renameColumn('et_ophinbiometry_measurement', 'axis_k1_right', 'k1_axis_right');
        $this->renameColumn('et_ophinbiometry_measurement_version', 'axis_k1_left', 'k1_axis_left');
        $this->renameColumn('et_ophinbiometry_measurement_version', 'axis_k1_right', 'k1_axis_right');
        $this->execute("CREATE OR REPLACE VIEW et_ophtroperationnote_biometry AS 
            SELECT 
                eol.id                                                                                                      AS id,
                eol.eye_id                                                                                                  AS eye_id,
                eol.last_modified_date                                                                                      AS last_modified_date,
                eoc.target_refraction_left                                                                                  AS target_refraction_left,
                eoc.target_refraction_right                                                                                 AS target_refraction_right,
                eos.lens_id_left                                                                                            AS lens_id_left,
                (
                    SELECT oll.name
                    FROM ophinbiometry_lenstype_lens oll
                    WHERE (oll.id = eos.lens_id_left)
                )                                                                                                           AS lens_left,

                (
                    SELECT coalesce(nullif(oll.display_name, ''), oll.description)
                    FROM ophinbiometry_lenstype_lens oll
                    WHERE (oll.id = eos.lens_id_left)
                )                                                                                                           AS lens_display_name_left,

                (
                    SELECT oll.description
                    FROM ophinbiometry_lenstype_lens oll
                    WHERE (oll.id = eos.lens_id_left)
                )                                                                                                           AS lens_description_left,

                (
                    SELECT eoirv.constant
                    FROM et_ophinbiometry_iol_ref_values eoirv
                    WHERE 
                        (
                            (eoirv.event_id = eol.event_id) 
                            AND (eoirv.lens_id = eos.lens_id_left) 
                            AND (eoirv.formula_id = eos.formula_id_left)
                        )
                    LIMIT 1
                )                                                                                                           AS lens_acon_left,
                eos.lens_id_right                                                                                           AS lens_id_right,
                (
                    SELECT oll.name
                    FROM ophinbiometry_lenstype_lens oll
                    WHERE (oll.id = eos.lens_id_right)
                )                                                                                                           AS lens_right,

                (
                    SELECT coalesce(nullif(oll.display_name, ''), oll.description)
                    FROM ophinbiometry_lenstype_lens oll
                    WHERE (oll.id = eos.lens_id_right)
                )                                                                                                           AS lens_display_name_right,

                (
                    SELECT oll.description
                    FROM ophinbiometry_lenstype_lens oll
                    WHERE (oll.id = eos.lens_id_right)
                )                                                                                                           AS lens_description_right,

                (
                    SELECT eoirv.constant
                    FROM et_ophinbiometry_iol_ref_values eoirv
                    WHERE (
                        (eoirv.event_id = eol.event_id) 
                        AND (eoirv.lens_id = eos.lens_id_right) 
                        AND (eoirv.formula_id = eos.formula_id_right)
                        )
                    LIMIT 1
                )                                                                                                            AS lens_acon_right,
                eol.k1_left                                                                                                  AS k1_left,
                eol.k1_right                                                                                                 AS k1_right,
                eol.k2_left                                                                                                  AS k2_left,
                eol.k2_right                                                                                                 AS k2_right,
                eol.k1_axis_left                                                                                             AS k1_axis_left,
                eol.k1_axis_right                                                                                            AS k1_axis_right,
                eol.axial_length_left                                                                                        AS axial_length_left,
                eol.axial_length_right                                                                                       AS axial_length_right,
                eol.snr_left                                                                                                 AS snr_left,
                eol.snr_right                                                                                                AS snr_right,
                eos.iol_power_left                                                                                           AS iol_power_left,
                eos.iol_power_right                                                                                          AS iol_power_right,
                eos.predicted_refraction_left                                                                                AS predicted_refraction_left,
                eos.predicted_refraction_right                                                                               AS predicted_refraction_right,
                ep.patient_id                                                                                                AS patient_id,
                eol.k2_axis_left                                                                                             AS k2_axis_left,
                eol.k2_axis_right                                                                                            AS k2_axis_right,
                eol.delta_k_left                                                                                             AS delta_k_left,
                eol.delta_k_right                                                                                            AS delta_k_right,
                eol.delta_k_axis_left                                                                                        AS delta_k_axis_left,
                eol.delta_k_axis_right                                                                                       AS delta_k_axis_right,
                eol.acd_left                                                                                                 AS acd_left,
                eol.acd_right                                                                                                AS acd_right,

                (
                    SELECT oes.name
                    FROM dicom_eye_status oes
                    WHERE (oes.id = eol.eye_status_left)
                )                                                                                                            AS status_left,

                (
                    SELECT oes.name
                    FROM dicom_eye_status oes
                    WHERE (oes.id = eol.eye_status_right)
                )                                                                                                            AS status_right,

                eoc.comments                                                                                                 AS comments,
                eoc.event_id                                                                                                 AS event_id,

                (
                    SELECT ocf.name
                    FROM ophinbiometry_calculation_formula ocf
                    WHERE (ocf.id = eos.formula_id_left)
                )                                                                                                            AS formula_left,

                (
                    SELECT ocf.name
                    FROM ophinbiometry_calculation_formula ocf
                    WHERE (ocf.id = eos.formula_id_right)
                )                                                                                                            AS formula_right
            FROM 
            (
                (
                    (
                        (
                            et_ophinbiometry_measurement eol 
                            JOIN et_ophinbiometry_calculation eoc ON ((eoc.event_id = eol.event_id))
                        ) 
                        LEFT JOIN et_ophinbiometry_selection eos ON ((eos.event_id = eol.event_id))
                    ) 
                    JOIN event ev ON ((ev.id = eol.event_id))
                )
                JOIN episode ep ON ((ep.id = ev.episode_id))
            )
            ORDER BY eol.last_modified_date;
        ");
    }

    public function down()
    {
        $this->renameColumn('et_ophinbiometry_measurement', 'k1_axis_left', 'axis_k1_left');
        $this->renameColumn('et_ophinbiometry_measurement', 'k1_axis_right', 'axis_k1_right');
        $this->renameColumn('et_ophinbiometry_measurement_version', 'k1_axis_left', 'axis_k1_left');
        $this->renameColumn('et_ophinbiometry_measurement_version', 'k1_axis_right', 'axis_k1_right');
        $this->execute("CREATE OR REPLACE VIEW et_ophtroperationnote_biometry AS 
            SELECT 
                eol.id                                                                                                      AS id,
                eol.eye_id                                                                                                  AS eye_id,
                eol.last_modified_date                                                                                      AS last_modified_date,
                eoc.target_refraction_left                                                                                  AS target_refraction_left,
                eoc.target_refraction_right                                                                                 AS target_refraction_right,
                eos.lens_id_left                                                                                            AS lens_id_left,
                (
                    SELECT oll.name
                    FROM ophinbiometry_lenstype_lens oll
                    WHERE (oll.id = eos.lens_id_left)
                )                                                                                                           AS lens_left,

                (
                    SELECT coalesce(nullif(oll.display_name, ''), oll.description)
                    FROM ophinbiometry_lenstype_lens oll
                    WHERE (oll.id = eos.lens_id_left)
                )                                                                                                           AS lens_display_name_left,

                (
                    SELECT oll.description
                    FROM ophinbiometry_lenstype_lens oll
                    WHERE (oll.id = eos.lens_id_left)
                )                                                                                                           AS lens_description_left,

                (
                    SELECT eoirv.constant
                    FROM et_ophinbiometry_iol_ref_values eoirv
                    WHERE 
                        (
                            (eoirv.event_id = eol.event_id) 
                            AND (eoirv.lens_id = eos.lens_id_left) 
                            AND (eoirv.formula_id = eos.formula_id_left)
                        )
                    LIMIT 1
                )                                                                                                           AS lens_acon_left,
                eos.lens_id_right                                                                                           AS lens_id_right,
                (
                    SELECT oll.name
                    FROM ophinbiometry_lenstype_lens oll
                    WHERE (oll.id = eos.lens_id_right)
                )                                                                                                           AS lens_right,

                (
                    SELECT coalesce(nullif(oll.display_name, ''), oll.description)
                    FROM ophinbiometry_lenstype_lens oll
                    WHERE (oll.id = eos.lens_id_right)
                )                                                                                                           AS lens_display_name_right,

                (
                    SELECT oll.description
                    FROM ophinbiometry_lenstype_lens oll
                    WHERE (oll.id = eos.lens_id_right)
                )                                                                                                           AS lens_description_right,

                (
                    SELECT eoirv.constant
                    FROM et_ophinbiometry_iol_ref_values eoirv
                    WHERE (
                        (eoirv.event_id = eol.event_id) 
                        AND (eoirv.lens_id = eos.lens_id_right) 
                        AND (eoirv.formula_id = eos.formula_id_right)
                        )
                    LIMIT 1
                )                                                                                                            AS lens_acon_right,
                eol.k1_left                                                                                                  AS k1_left,
                eol.k1_right                                                                                                 AS k1_right,
                eol.k2_left                                                                                                  AS k2_left,
                eol.k2_right                                                                                                 AS k2_right,
                eol.axis_k1_left                                                                                             AS axis_k1_left,
                eol.axis_k1_right                                                                                            AS axis_k1_right,
                eol.axial_length_left                                                                                        AS axial_length_left,
                eol.axial_length_right                                                                                       AS axial_length_right,
                eol.snr_left                                                                                                 AS snr_left,
                eol.snr_right                                                                                                AS snr_right,
                eos.iol_power_left                                                                                           AS iol_power_left,
                eos.iol_power_right                                                                                          AS iol_power_right,
                eos.predicted_refraction_left                                                                                AS predicted_refraction_left,
                eos.predicted_refraction_right                                                                               AS predicted_refraction_right,
                ep.patient_id                                                                                                AS patient_id,
                eol.k2_axis_left                                                                                             AS k2_axis_left,
                eol.k2_axis_right                                                                                            AS k2_axis_right,
                eol.delta_k_left                                                                                             AS delta_k_left,
                eol.delta_k_right                                                                                            AS delta_k_right,
                eol.delta_k_axis_left                                                                                        AS delta_k_axis_left,
                eol.delta_k_axis_right                                                                                       AS delta_k_axis_right,
                eol.acd_left                                                                                                 AS acd_left,
                eol.acd_right                                                                                                AS acd_right,

                (
                    SELECT oes.name
                    FROM dicom_eye_status oes
                    WHERE (oes.id = eol.eye_status_left)
                )                                                                                                            AS status_left,

                (
                    SELECT oes.name
                    FROM dicom_eye_status oes
                    WHERE (oes.id = eol.eye_status_right)
                )                                                                                                            AS status_right,

                eoc.comments                                                                                                 AS comments,
                eoc.event_id                                                                                                 AS event_id,

                (
                    SELECT ocf.name
                    FROM ophinbiometry_calculation_formula ocf
                    WHERE (ocf.id = eos.formula_id_left)
                )                                                                                                            AS formula_left,

                (
                    SELECT ocf.name
                    FROM ophinbiometry_calculation_formula ocf
                    WHERE (ocf.id = eos.formula_id_right)
                )                                                                                                            AS formula_right
            FROM 
            (
                (
                    (
                        (
                            et_ophinbiometry_measurement eol 
                            JOIN et_ophinbiometry_calculation eoc ON ((eoc.event_id = eol.event_id))
                        ) 
                        LEFT JOIN et_ophinbiometry_selection eos ON ((eos.event_id = eol.event_id))
                    ) 
                    JOIN event ev ON ((ev.id = eol.event_id))
                )
                JOIN episode ep ON ((ep.id = ev.episode_id))
            )
            ORDER BY eol.last_modified_date;
        ");
    }
}
