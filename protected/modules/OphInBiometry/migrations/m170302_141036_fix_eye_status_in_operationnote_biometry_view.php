<?php

class m170302_141036_fix_eye_status_in_operationnote_biometry_view extends CDbMigration
{
    public function up()
    {
        $this->execute('CREATE OR REPLACE VIEW et_ophtroperationnote_biometry AS SELECT
                             eol.id AS id,eol.eye_id AS eye_id,eol.last_modified_date AS last_modified_date,
                             eoc.target_refraction_left AS target_refraction_left,
                             eoc.target_refraction_right AS target_refraction_right,
                            (select oll.name from ophinbiometry_lenstype_lens oll where (oll.id = eos.lens_id_left)) AS lens_left,
                            (select oll.description from ophinbiometry_lenstype_lens oll where (oll.id = eos.lens_id_left)) AS lens_description_left,
                            (select eoirv.constant from et_ophinbiometry_iol_ref_values eoirv where ((eoirv.event_id = eol.event_id) and (eoirv.lens_id = eos.lens_id_left) and (eoirv.formula_id = eos.formula_id_left)) limit 1) AS lens_acon_left,
                            (select oll.name from ophinbiometry_lenstype_lens oll where (oll.id = eos.lens_id_right)) AS lens_right,
                            (select oll.description from ophinbiometry_lenstype_lens oll where (oll.id = eos.lens_id_right)) AS lens_description_right,
                            (select eoirv.constant from et_ophinbiometry_iol_ref_values eoirv where ((eoirv.event_id = eol.event_id) and (eoirv.lens_id = eos.lens_id_right) and (eoirv.formula_id = eos.formula_id_right)) limit 1) AS lens_acon_right,
                            eol.k1_left AS k1_left,eol.k1_right AS k1_right,eol.k2_left AS k2_left,eol.k2_right AS k2_right,
                            eol.axis_k1_left AS axis_k1_left,eol.axis_k1_right AS axis_k1_right,
                            eol.axial_length_left AS axial_length_left,eol.axial_length_right AS axial_length_right,
                            eol.snr_left AS snr_left,eol.snr_right AS snr_right,
                            eos.iol_power_left AS iol_power_left,eos.iol_power_right AS iol_power_right,
                            eos.predicted_refraction_left AS predicted_refraction_left,
                            eos.predicted_refraction_right AS predicted_refraction_right,
                            ep.patient_id AS patient_id,eol.k2_axis_left AS k2_axis_left,eol.k2_axis_right AS k2_axis_right,
                            eol.delta_k_left AS delta_k_left,eol.delta_k_right AS delta_k_right,
                            eol.delta_k_axis_left AS delta_k_axis_left,eol.delta_k_axis_right AS delta_k_axis_right,
                            eol.acd_left AS acd_left,eol.acd_right AS acd_right,
                            (select oes.name from dicom_eye_status oes where (oes.id = eol.eye_status_left)) AS status_left,
                            (select oes.name from dicom_eye_status oes where (oes.id = eol.eye_status_right)) AS status_right,
                            eoc.comments AS comments,eoc.event_id AS event_id,
                            (select ocf.name from ophinbiometry_calculation_formula ocf where (ocf.id = eos.formula_id_left)) AS formula_left,
                            (select ocf.name from ophinbiometry_calculation_formula ocf where (ocf.id = eos.formula_id_right)) AS formula_right 
                        FROM ((((et_ophinbiometry_measurement eol join et_ophinbiometry_calculation eoc on((eoc.event_id = eol.event_id))) 
                                 left join et_ophinbiometry_selection eos on((eos.event_id = eol.event_id))) 
                                 join event ev on((ev.id = eol.event_id))) 
                                 join episode ep on((ep.id = ev.episode_id))) 
                                 order by eol.last_modified_date;');
    }

    public function down()
    {
        $this->execute('CREATE OR REPLACE VIEW et_ophtroperationnote_biometry AS SELECT
							eol.id, eol.eye_id, eol.last_modified_date, target_refraction_left, target_refraction_right,
							(SELECT name FROM ophinbiometry_lenstype_lens oll WHERE oll.id=lens_id_left) as lens_left,
							(SELECT description FROM ophinbiometry_lenstype_lens oll WHERE oll.id=lens_id_left) as lens_description_left,
							(SELECT constant FROM et_ophinbiometry_iol_ref_values eoirv WHERE eoirv.event_id = eol.event_id AND eoirv.lens_id=eos.lens_id_left AND eoirv.formula_id=eos.formula_id_left LIMIT 1) AS lens_acon_left,
							(SELECT name FROM ophinbiometry_lenstype_lens oll WHERE oll.id=lens_id_right) as lens_right,
							(SELECT description FROM ophinbiometry_lenstype_lens oll WHERE oll.id=lens_id_right) as lens_description_right,
							(SELECT constant FROM et_ophinbiometry_iol_ref_values eoirv WHERE eoirv.event_id = eol.event_id AND eoirv.lens_id=eos.lens_id_right AND eoirv.formula_id=eos.formula_id_right LIMIT 1) AS lens_acon_right,
							k1_left, k1_right, k2_left, k2_right, axis_k1_left, axis_k1_right, axial_length_left, axial_length_right,
							snr_left, snr_right, iol_power_left, iol_power_right, predicted_refraction_left, predicted_refraction_right, patient_id,
							k2_axis_left, k2_axis_right, delta_k_left, delta_k_right, delta_k_axis_left, delta_k_axis_right, acd_left, acd_right,
							(SELECT name FROM dicom_eye_status oes WHERE oes.id=lens_id_left) as status_left,
							(SELECT name FROM dicom_eye_status oes WHERE oes.id=lens_id_right) as status_right,
							comments, eoc.event_id,
							(SELECT name FROM ophinbiometry_calculation_formula ocf WHERE ocf.id=eos.formula_id_left) as formula_left,
							(SELECT name FROM ophinbiometry_calculation_formula ocf WHERE ocf.id=eos.formula_id_right) as formula_right
							FROM et_ophinbiometry_measurement eol
							JOIN et_ophinbiometry_calculation eoc ON eoc.event_id=eol.event_id
							LEFT JOIN et_ophinbiometry_selection eos ON eos.event_id=eol.event_id
							JOIN event ev ON ev.id=eol.event_id
							JOIN episode ep ON ep.id=ev.episode_id
							ORDER BY eol.last_modified_date;');
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
