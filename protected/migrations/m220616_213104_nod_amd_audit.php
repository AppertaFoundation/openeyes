<?php

class m220616_213104_nod_amd_audit extends CDbMigration
{
    public function up()
    {
        $query = <<<EOL
            CREATE OR REPLACE VIEW v_patient_oct AS
            SELECT p.id AS patient_id,
            ev.event_date AS event_date,
            'R' AS eye,
            gae.crt AS crt,
            gae.avg_thickness AS avg_thickness,
            gae.total_vol AS total_vol,
            gae.irf AS irf,
            gae.srf AS srf,
            gae.cysts AS cysts,
            gae.retinal_thickening AS retinal_thickening,
            gae.ped AS ped,
            gae.cmo AS cmo,
            gae.dmo AS dmo,
            gae.heamorrhage AS heamorrhage,
            gae.exudates AS exudates,
            gae.avg_rnfl AS avg_rnfl,
            gae.cct AS cct,
            gae.cd_ratio AS cd_ratio,
            gae.cst AS cst
            FROM patient p
            JOIN episode ep ON ep.patient_id=p.id
            JOIN event ev ON ev.episode_id=ep.id
            JOIN et_ophgeneric_assessment ga ON ga.event_id=ev.id
            JOIN ophgeneric_assessment_entry gae ON gae.element_id=ga.id
            WHERE ga.eye_id<>1
            UNION
            SELECT p.id AS patient_id,
            ev.event_date AS event_date,
            'L' AS eye,
            gae.crt AS crt,
            gae.avg_thickness AS avg_thickness,
            gae.total_vol AS total_vol,
            gae.irf AS irf,
            gae.srf AS srf,
            gae.cysts AS cysts,
            gae.retinal_thickening AS retinal_thickening,
            gae.ped AS ped,
            gae.cmo AS cmo,
            gae.dmo AS dmo,
            gae.heamorrhage AS heamorrhage,
            gae.exudates AS exudates,
            gae.avg_rnfl AS avg_rnfl,
            gae.cct AS cct,
            gae.cd_ratio AS cd_ratio,
            gae.cst AS cst
            FROM patient p
            JOIN episode ep ON ep.patient_id=p.id
            JOIN event ev ON ev.episode_id=ep.id
            JOIN et_ophgeneric_assessment ga ON ga.event_id=ev.id
            JOIN ophgeneric_assessment_entry gae ON gae.element_id=ga.id
            WHERE ga.eye_id<>0;
        EOL;
        $this->dbConnection->createCommand($query)->execute();

        $query = <<<EOL
            CREATE OR REPLACE VIEW v_patient_intravitreal_injections AS
            SELECT p.id AS patient_id,
            'R' AS eye,
            ev.event_date AS event_date,
            ijad.name AS pre_antisept_drug,
            ijsd.name AS pre_skin_drug,
            ijtd.name AS drug,
            ijt.right_number AS number,
            ijt.right_batch_number AS batch_number,
            ijt.right_batch_expiry_date AS batch_expiry_date,
            CONCAT(user.title,' ',user.first_name,' ',user.last_name) AS injection_given_by,
            ijt.right_injection_time AS injection_time,
            CASE ijt.right_pre_ioplowering_required
                WHEN 0 THEN 'No'
                WHEN 1 THEN 'Yes'
            END AS pre_ioplowering_required,
            CASE ijt.right_post_ioplowering_required
                WHEN 0 THEN 'No'
                WHEN 1 THEN 'Yes'
            END AS post_ioplowering_required,
            ijls.name AS lens_status,
            ijc.right_oth_descrip AS complication,
            user.role AS administrator,
            dg.grade AS doctor_grade,
            site.name AS site,
            institution.name AS institution
            FROM patient p
            JOIN episode ep ON ep.patient_id=p.id
            JOIN event ev ON ev.episode_id=ep.id
            JOIN et_ophtrintravitinjection_treatment ijt ON ijt.event_id=ev.id
            LEFT JOIN et_ophtrintravitinjection_site ijs ON ijs.event_id=ev.id
            LEFT JOIN ophtrintravitinjection_antiseptic_drug ijad ON ijad.id=ijt.right_pre_antisept_drug_id
            LEFT JOIN ophtrintravitinjection_skin_drug ijsd ON ijsd.id=ijt.right_pre_skin_drug_id
            LEFT JOIN ophtrintravitinjection_treatment_drug ijtd ON ijtd.id=ijt.right_drug_id
            LEFT JOIN et_ophtrintravitinjection_anteriorseg ija ON ija.event_id=ev.id
            LEFT JOIN ophtrintravitinjection_lens_status ijls ON ijls.id=ija.left_lens_status_id
            LEFT JOIN et_ophtrintravitinjection_complications ijc ON ijc.event_id=ev.id
            LEFT JOIN user user ON user.id=ijt.right_injection_given_by_id
            LEFT JOIN doctor_grade dg ON dg.id=user.doctor_grade_id
            LEFT JOIN site ON site.id=ijs.site_id
            LEFT JOIN institution ON institution.id=site.institution_id
            WHERE ijt.eye_id<>1
            UNION
            SELECT p.id AS patient_id,
            'L' AS eye,
            ev.event_date AS event_date,
            ijad.name AS pre_antisept_drug,
            ijsd.name AS pre_skin_drug,
            ijtd.name AS drug,
            ijt.left_number AS number,
            ijt.left_batch_number AS batch_number,
            ijt.left_batch_expiry_date AS batch_expiry_date,
            CONCAT(user.title,' ',user.first_name,' ',user.last_name) AS injection_given_by,
            ijt.left_injection_time AS injection_time,
            CASE ijt.left_pre_ioplowering_required
                WHEN 0 THEN 'No'
                WHEN 1 THEN 'Yes'
            END AS pre_ioplowering_required,
            CASE ijt.left_post_ioplowering_required
                WHEN 0 THEN 'No'
                WHEN 1 THEN 'Yes'
            END AS post_ioplowering_required,
            ijls.name AS lens_status,
            ijc.left_oth_descrip AS complication,
            user.role AS administrator,
            dg.grade AS doctor_grade,
            site.name AS site,
            institution.name AS institution
            FROM patient p
            JOIN episode ep ON ep.patient_id=p.id
            JOIN event ev ON ev.episode_id=ep.id
            JOIN et_ophtrintravitinjection_treatment ijt ON ijt.event_id=ev.id
            LEFT JOIN et_ophtrintravitinjection_site ijs ON ijs.event_id=ev.id
            LEFT JOIN ophtrintravitinjection_antiseptic_drug ijad ON ijad.id=ijt.left_pre_antisept_drug_id
            LEFT JOIN ophtrintravitinjection_skin_drug ijsd ON ijsd.id=ijt.left_pre_skin_drug_id
            LEFT JOIN ophtrintravitinjection_treatment_drug ijd ON ijd.id=ijt.left_pre_antisept_drug_id
            LEFT JOIN ophtrintravitinjection_treatment_drug ijtd ON ijtd.id=ijt.left_drug_id
            LEFT JOIN et_ophtrintravitinjection_anteriorseg ija ON ija.event_id=ev.id
            LEFT JOIN ophtrintravitinjection_lens_status ijls ON ijls.id=ija.left_lens_status_id
            LEFT JOIN et_ophtrintravitinjection_complications ijc ON ijc.event_id=ev.id
            LEFT JOIN user user ON user.id=ijt.left_injection_given_by_id
            LEFT JOIN doctor_grade dg ON dg.id=user.doctor_grade_id
            LEFT JOIN site ON site.id=ijs.site_id
            LEFT JOIN institution ON institution.id=site.institution_id
            WHERE ijt.eye_id<>0;
        EOL;
        $this->dbConnection->createCommand($query)->execute();

        $query = <<<EOL
            CREATE OR REPLACE VIEW v_patient_social_history AS
            SELECT p.id AS patient_id,
            ev.event_date AS event_date,
            o.name AS occupation,
            ss.name AS smoking_status,
            a.name AS accommodation,
            c.name AS carer,
            sm.name AS substance_misuse,
            eosh.alcohol_intake AS alcohol_intake
            FROM patient p
            JOIN episode ep ON ep.patient_id=p.id
            JOIN event ev ON ev.episode_id=ep.id
            JOIN et_ophciexamination_socialhistory eosh ON eosh.event_id=ev.id
            LEFT JOIN ophciexamination_socialhistory_occupation o ON o.id=eosh.occupation_id
            LEFT JOIN ophciexamination_socialhistory_smoking_status ss ON ss.id=eosh.smoking_status_id
            LEFT JOIN ophciexamination_socialhistory_accommodation a ON a.id=eosh.accommodation_id
            LEFT JOIN ophciexamination_socialhistory_carer c ON c.id=eosh.carer_id
            LEFT JOIN ophciexamination_socialhistory_substance_misuse sm ON sm.id=eosh.substance_misuse_id
            LEFT JOIN ophciexamination_socialhistory_driving_status_assignment dsa ON dsa.element_id=eosh.id
            LEFT JOIN ophciexamination_socialhistory_driving_status ds ON ds.id=dsa.driving_status_id;
        EOL;
        $this->dbConnection->createCommand($query)->execute();

        $query = <<<EOL
            CREATE OR REPLACE VIEW v_patient_laser_procedure AS
            SELECT p.id AS patient_id,
            proc.id AS procedure_id,
            proc.term AS term,
            proc.short_format AS short_term,
            proc.snomed_code AS snomed_code,
            proc.snomed_term AS snomed_term,
            proc.ecds_code AS ecds_code,
            proc.ecds_term AS ecds_term,
            'R' AS eye,
            ev.event_date AS event_date
            FROM patient p
            JOIN episode ep ON ep.patient_id=p.id
            JOIN event ev ON ev.episode_id=ep.id
            JOIN et_ophtrlaser_treatment lt ON lt.event_id=ev.id
            JOIN ophtrlaser_laserprocedure_assignment lpa ON lpa.treatment_id=lt.id
            JOIN proc ON proc.id=lpa.procedure_id
            WHERE lpa.eye_id<>1
            UNION
            SELECT p.id AS patient_id,
            proc.id AS procedure_id,
            proc.term AS term,
            proc.short_format AS short_term,
            proc.snomed_code AS snomed_code,
            proc.snomed_term AS snomed_term,
            proc.ecds_code AS ecds_code,
            proc.ecds_term AS ecds_term,
            'L' AS eye,
            ev.event_date AS event_date
            FROM patient p
            JOIN episode ep ON ep.patient_id=p.id
            JOIN event ev ON ev.episode_id=ep.id
            JOIN et_ophtrlaser_treatment lt ON lt.event_id=ev.id
            JOIN ophtrlaser_laserprocedure_assignment lpa ON lpa.treatment_id=lt.id
            JOIN proc ON proc.id=lpa.procedure_id
            WHERE lpa.eye_id<>0;
        EOL;
        $this->dbConnection->createCommand($query)->execute();

        // Adding foreign keys
        $query = <<<EOL
            ALTER TABLE et_ophtrintravitinjection_anteriorseg ADD CONSTRAINT et_ophtrintravitinjection_anteriorseg_evi_fk FOREIGN KEY IF NOT EXISTS (event_id) REFERENCES event(id);
            ALTER TABLE et_ophciexamination_socialhistory ADD CONSTRAINT et_ophciexamination_socialhistory_evi_fk FOREIGN KEY IF NOT EXISTS (event_id) REFERENCES event(id);
        EOL;
        $this->dbConnection->createCommand($query)->execute();
    }

    public function down()
    {
        $query = <<<EOL
            DROP VIEW IF EXISTS v_patient_oct;
            DROP VIEW IF EXISTS v_patient_intravitreal_injections;
            DROP VIEW IF EXISTS v_patient_social_history;
            DROP VIEW IF EXISTS v_patient_laser_procedure;
            ALTER TABLE et_ophtrintravitinjection_anteriorseg DROP FOREIGN KEY IF EXISTS et_ophtrintravitinjection_anteriorseg_evi_fk;
            ALTER TABLE et_ophciexamination_socialhistory DROP FOREIGN KEY IF EXISTS et_ophciexamination_socialhistory_evi_fk;
        EOL;
        $this->dbConnection->createCommand($query)->execute();
    }
}
