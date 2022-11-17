<?php

class m220801_175602_new_patient_views extends OEMigration
{
    public function safeUp()
    {
        $this->execute("
		CREATE OR REPLACE
		ALGORITHM = UNDEFINED VIEW `v_patient_events` AS
		select
			`p`.`id` AS `patient_id`,
			`ev`.`id` AS `event_id`,
			`ep`.`id` AS `episode_id`,
			`ev`.`event_type_id` AS `event_type_id`,
			`et`.`name` AS `event_name`,
			`et`.`class_name` AS `event_class`,
			`ev`.`event_date` AS `event_date`,
			`ev`.`created_date` AS `event_created_date`,
			`ev`.`last_modified_date` AS `event_last_modified_date`,
			`f`.`name` AS `firm_name`,
			`f`.`id` AS `firm_id`,
			`s`.`id` AS `subspecialty_id`,
			`s`.`name` AS `subspecialty`,
			`ev`.`institution_id` AS `institution_id`,
			`i`.`name` AS `institution_name`,
			`ev`.`site_id` AS `site_id`,
			`si`.`name` AS `site_name`,
			`ev`.`worklist_patient_id`
		from
			((((((((`patient` `p`
		join `episode` `ep` on
			(`ep`.`patient_id` = `p`.`id`))
		left join `event` `ev` on
			(`ev`.`episode_id` = `ep`.`id`))
		left join `event_type` `et` on
			(`et`.`id` = `ev`.`event_type_id`))
		left join `firm` `f` on
			(`f`.`id` = `ep`.`firm_id`))
		left join `service_subspecialty_assignment` `ssa` on
			(`ssa`.`id` = `f`.`service_subspecialty_assignment_id`))
		left join `subspecialty` `s` on
			(`s`.`id` = `ssa`.`subspecialty_id`))
		left join `institution` `i` on
			(`i`.`id` = `ev`.`institution_id`))
		left join `site` `si` on
			(`si`.`id` = `ev`.`site_id`))
		where
			`ev`.`deleted` = 0
			and `ep`.`deleted` = 0;");

        $this->dbConnection->createCommand("
            CREATE OR REPLACE VIEW v_patient_appointments AS
			SELECT wp.patient_id AS patient_id,
				w.name AS worklist_name,
				wp.id AS worklist_patient_id,
				GROUP_CONCAT(DISTINCT CONCAT(wa.name,':',wpa.attribute_value)) AS worklist_patient_attributes,
				w.start AS worklist_start,
				w.end AS worklist_end,
				wp.when AS scheduled_time,
				IFNULL(p.start_time, psc.start_time) AS arrival_time,
				IFNULL(p.end_time, psd.start_time) AS discharge_time,
				IF(p.did_not_attend='0',false,true) AS did_not_attend
			FROM worklist w
				JOIN worklist_patient wp ON wp.worklist_id=w.id
				JOIN worklist_attribute wa ON wa.worklist_id=w.id
				JOIN worklist_patient_attribute wpa ON wpa.worklist_attribute_id=wa.id AND wpa.worklist_patient_id=wp.id
				LEFT JOIN pathway p ON p.worklist_patient_id=wp.id
				LEFT JOIN pathway_step psc ON psc.pathway_id=p.id
				LEFT JOIN pathway_step_type pstc ON pstc.id=psc.step_type_id AND pstc.short_name='checkin'
				LEFT JOIN pathway_step psd ON psd.pathway_id=p.id
				LEFT JOIN pathway_step_type pstd ON pstd.id=psd.step_type_id AND pstc.short_name='discharge'
			GROUP BY wp.id;
		")->execute();

        $this->dbConnection->createCommand("
            CREATE OR REPLACE VIEW v_patient_investigations AS
			SELECT p.id AS patient_id,
				ev.id AS event_id,
				eoie.date AS investigation_date,
				eoie.time AS investigation_time,
				eoie.investigation_code AS investigation_code_id,
				eoic.name AS investigation_name,
				IFNULL(eoie.comments, oic.comments) AS investigation_comments,
				eoic.snomed_term AS snomed_term,
				eoic.snomed_code AS snomed_code,
				oc.name AS opcs_code,
				oc.description AS opcs_terms,
				eoic.ecds_code AS ecds_code,
				IFNULL(proc.ecds_term, d.ecds_term) AS ecds_term,
				eoic.specialty_id AS specialty_id,
				ev.worklist_patient_id AS worklist_patient_id,
				eoi.created_user_id AS created_user_id,
				eoi.created_date AS created_date,
				eoi.last_modified_user_id AS last_modified_user_id,
				eoi.last_modified_date AS last_modified_date
			FROM patient p
				JOIN episode ep ON ep.patient_id=p.id
				JOIN event ev ON ev.episode_id=ep.id
				JOIN et_ophciexamination_investigation eoi ON eoi.event_id=ev.id
				JOIN et_ophciexamination_investigation_entry eoie ON eoie.element_id=eoi.id
				JOIN et_ophciexamination_investigation_codes eoic ON eoic.id=eoie.investigation_code
				LEFT JOIN ophciexamination_investigation_comments oic ON oic.investigation_code=eoic.id
				LEFT JOIN proc ON proc.ecds_code=eoic.ecds_code
				LEFT JOIN disorder d ON d.ecds_code=eoic.ecds_code
				LEFT JOIN proc_opcs_assignment poa ON poa.proc_id=proc.id
				LEFT JOIN opcs_code oc ON oc.id=poa.opcs_code_id;
		")->execute();

        $this->dbConnection->createCommand("
            CREATE OR REPLACE VIEW v_patient_clinic_procedures AS
			SELECT p.id AS patient_id,
				ev.id AS event_id,
				1 AS eye_id,
				'L' AS side,
				ocpe.date AS procedure_date,
				ocpe.outcome_time AS precedure_time,
				ocpe.procedure_id AS procedure_id,
				proc.term AS procedure_term,
				proc.short_format AS short_format,
				ocpe.comments AS procedure_comments,
				proc.aliases AS aliases,
				s.name AS specialty,
				ss.name AS subspecialty,
				proc.snomed_code AS snomed_code,
				proc.snomed_term AS snomed_term,
				GROUP_CONCAT(oc.name) AS opcs_code,
				GROUP_CONCAT(oc.description) AS opcs_description,
				proc.ecds_code AS ecds_code,
				proc.ecds_term AS ecds_term,
				ev.worklist_patient_id AS worklist_patient_id,
				ocpe.created_user_id AS created_user_id,
				ocpe.created_date AS created_date,
				ocpe.last_modified_user_id AS last_modified_user_id,
				ocpe.last_modified_date AS last_modified_date
			FROM patient p
				JOIN episode ep ON ep.patient_id=p.id
				JOIN event ev ON ev.episode_id=ep.id
				JOIN et_ophciexamination_clinic_procedures eocp ON eocp.event_id=ev.id
				JOIN ophciexamination_clinic_procedures_entry ocpe ON ocpe.element_id=eocp.id
				JOIN eye ON eye.id=ocpe.eye_id
				JOIN subspecialty ss ON ss.id=ocpe.subspecialty_id
				JOIN specialty s ON s.id=ss.specialty_id
				JOIN proc ON proc.id=ocpe.procedure_id
				LEFT JOIN proc_opcs_assignment poa ON poa.proc_id=proc.id
				LEFT JOIN opcs_code oc ON oc.id=poa.opcs_code_id
			WHERE ocpe.eye_id IN (1,3)
			GROUP BY ocpe.id
			UNION
			SELECT p.id AS patient_id,
				ev.id AS event_id,
				2 AS eye_id,
				'R' AS side,
				ocpe.date AS procedure_date,
				ocpe.outcome_time AS precedure_time,
				ocpe.procedure_id AS procedure_id,
				proc.term AS procedure_term,
				proc.short_format AS short_format,
				ocpe.comments AS procedure_comments,
				proc.aliases AS aliases,
				s.name AS specialty,
				ss.name AS subspecialty,
				proc.snomed_code AS snomed_code,
				proc.snomed_term AS snomed_term,
				GROUP_CONCAT(oc.name) AS opcs_code,
				GROUP_CONCAT(oc.description) AS opcs_description,
				proc.ecds_code AS ecds_code,
				proc.ecds_term AS ecds_term,
				ev.worklist_patient_id AS worklist_patient_id,
				ocpe.created_user_id AS created_user_id,
				ocpe.created_date AS created_date,
				ocpe.last_modified_user_id AS last_modified_user_id,
				ocpe.last_modified_date AS last_modified_date
			FROM patient p
				JOIN episode ep ON ep.patient_id=p.id
				JOIN event ev ON ev.episode_id=ep.id
				JOIN et_ophciexamination_clinic_procedures eocp ON eocp.event_id=ev.id
				JOIN ophciexamination_clinic_procedures_entry ocpe ON ocpe.element_id=eocp.id
				JOIN eye ON eye.id=ocpe.eye_id
				JOIN subspecialty ss ON ss.id=ocpe.subspecialty_id
				JOIN specialty s ON s.id=ss.specialty_id
				JOIN proc ON proc.id=ocpe.procedure_id
				LEFT JOIN proc_opcs_assignment poa ON poa.proc_id=proc.id
				LEFT JOIN opcs_code oc ON oc.id=poa.opcs_code_id
			WHERE ocpe.eye_id IN (2,3)
			GROUP BY ocpe.id;
		")->execute();

        $this->execute("CREATE OR REPLACE
		ALGORITHM = UNDEFINED VIEW `ophthalmic_diagnosis_examination_events` AS
		select
			`event`.`event_date` AS `event_date`,
			`event`.`created_date` AS `created_date`,
			`et`.`event_id` AS `event_id`,
			`episode`.`patient_id` AS `patient_id`
		from
			((`et_ophciexamination_diagnoses` `et`
		join `event` on
			(`et`.`event_id` = `event`.`id`))
		join `episode` on
			(`event`.`episode_id` = `episode`.`id`
				and `event`.`deleted` = 0));");

        $this->execute("CREATE OR REPLACE
		ALGORITHM = UNDEFINED VIEW `systemic_diagnosis_examination_events` AS
		select
			`event`.`event_date` AS `event_date`,
			`event`.`created_date` AS `created_date`,
			`et`.`event_id` AS `event_id`,
			`episode`.`patient_id` AS `patient_id`
		from
			((`et_ophciexamination_systemic_diagnoses` `et`
		join `event` on
			(`et`.`event_id` = `event`.`id`))
		join `episode` on
			(`event`.`episode_id` = `episode`.`id`
				and `event`.`deleted` = 0));");

        $this->execute("CREATE OR REPLACE
		ALGORITHM = UNDEFINED VIEW `latest_ophthalmic_diagnosis_examination_events` AS
		select
			`t1`.`event_id` AS `event_id`,
			`t1`.`patient_id` AS `patient_id`
		from
			(`ophthalmic_diagnosis_examination_events` `t1`
		left join `ophthalmic_diagnosis_examination_events` `t2` on
			(`t1`.`patient_id` = `t2`.`patient_id`
				and (`t1`.`event_date` < `t2`.`event_date`
					or `t1`.`event_date` = `t2`.`event_date`
					and `t1`.`created_date` < `t2`.`created_date`)))
		where
			`t2`.`patient_id` is null;");

        $this->execute("CREATE OR REPLACE
		ALGORITHM = UNDEFINED VIEW `latest_systemic_diagnosis_examination_events` AS
		select
			`t1`.`event_id` AS `event_id`,
			`t1`.`patient_id` AS `patient_id`
		from
			(`systemic_diagnosis_examination_events` `t1`
		left join `systemic_diagnosis_examination_events` `t2` on
			(`t1`.`patient_id` = `t2`.`patient_id`
				and (`t1`.`event_date` < `t2`.`event_date`
					or `t1`.`event_date` = `t2`.`event_date`
					and `t1`.`created_date` < `t2`.`created_date`)))
		where
			`t2`.`patient_id` is null;");

        $this->dbConnection->createCommand("
		CREATE OR REPLACE VIEW v_patient_diagnoses AS
		SELECT ev.patient_id AS patient_id,
			od.eye_id AS side_id,
			CASE od.eye_id 
				WHEN 1 THEN 'L'
				WHEN 2 THEN 'R'
				WHEN 3 THEN 'B'
			END AS side,
			od.disorder_id AS disorder_id,
			d.term AS disorder_term,
			d.fully_specified_name AS disorder_fully_specified,
			od.`date` AS disorder_date,
			d.aliases AS disorder_aliases,
			d.icd10_code AS icd10_code,
			d.icd10_term AS icd10_term,
			s.name AS specialty,
			ev.event_id,
			ev.worklist_patient_id AS last_verified_worklist_patient_id
		FROM et_ophciexamination_diagnoses eod
				INNER JOIN v_patient_events ev ON ev.event_id = eod.event_id 
				INNER JOIN ophciexamination_diagnosis od ON od.element_diagnoses_id = eod.id
				INNER JOIN disorder d ON d.id = od.disorder_id
				INNER JOIN specialty s ON s.id = d.specialty_id
				INNER JOIN latest_ophthalmic_diagnosis_examination_events lodee ON lodee.event_id = ev.event_id AND lodee.patient_id = ev.patient_id 
				
		UNION 
		
		SELECT ev.patient_id AS patient_id,
			od.side_id AS side_id,
			CASE od.side_id
				WHEN 1 THEN 'L'
				WHEN 2 THEN 'R'
				WHEN 3 THEN 'B'
				ELSE NULL
			END AS side,
			od.disorder_id AS disorder_id,
			d.term AS disorder_term,
			d.fully_specified_name AS disorder_fully_specified,
			od.`date` AS disorder_date,
			d.aliases AS disorder_aliases,
			d.icd10_code AS icd10_code,
			d.icd10_term AS icd10_term,
			s.name AS specialty,
			ev.event_id,
			ev.worklist_patient_id AS last_verified_worklist_patient_id
		FROM et_ophciexamination_systemic_diagnoses eod
				INNER JOIN v_patient_events ev ON ev.event_id = eod.event_id 
				INNER JOIN ophciexamination_systemic_diagnoses_diagnosis od ON od.element_id = eod.id
				INNER JOIN disorder d ON d.id = od.disorder_id
				LEFT JOIN specialty s ON s.id = d.specialty_id
				INNER JOIN latest_systemic_diagnosis_examination_events lodee ON lodee.event_id = ev.event_id AND lodee.patient_id = ev.patient_id
				
		;
		")->execute();
    }

    public function safeDown()
    {
        $this->dbConnection->createCommand("
		DROP VIEW IF EXISTS v_patient_appointments;
		DROP VIEW IF EXISTS v_patient_investigations;
		DROP VIEW IF EXISTS v_patient_clinic_procedures;
		")->execute();

        $this->dbConnection->createCommand("
			CREATE OR REPLACE VIEW v_patient_diagnoses AS
			SELECT p.patient_id AS patient_id,
				p.eye_id AS eye_id,
				p.side AS side,
				d.id AS disorder_id,
				d.term AS disorder_term,
				d.fully_specified_name AS disorder_fully_specified,
				d.icd10_code AS icd10_code,
				d.icd10_term AS icd10_term,
				p.disorder_date AS disorder_date,
				d.aliases AS disorder_aliases,
				s.name AS specialty
			FROM v_patient_episodes p
				JOIN disorder d ON p.disorder_id = d.id
				LEFT JOIN subspecialty ss ON ss.id = p.subspecialty_id
				LEFT JOIN specialty s ON s.id = ss.specialty_id
			UNION
			SELECT sd.patient_id AS patient_id,
				sd.eye_id AS eye_id,
				CASE sd.eye_id
					WHEN 1 THEN 'L'
					WHEN 2 THEN 'R'
					WHEN 3 THEN 'B'
				END AS side,
				d.id AS disorder_id,
				d.term AS disorder_term,
				d.fully_specified_name AS disorder_fully_specified,
				d.icd10_code AS icd10_code,
				d.icd10_term AS icd10_term,
				sd.date AS disorder_date,
				d.aliases AS disorder_aliases,
				s.name AS specialty
			FROM secondary_diagnosis sd
				JOIN disorder d ON d.id = sd.disorder_id
				LEFT JOIN specialty s ON s.id = d.specialty_id
			ORDER BY patient_id;
		")->execute();

        $this->execute("
		CREATE OR REPLACE
		ALGORITHM = UNDEFINED VIEW `v_patient_events` AS
		select
			`p`.`id` AS `patient_id`,
			`ev`.`id` AS `event_id`,
			`ep`.`id` AS `episode_id`,
			`ev`.`event_type_id` AS `event_type_id`,
			`et`.`name` AS `event_name`,
			`et`.`class_name` AS `event_class`,
			`ev`.`event_date` AS `event_date`,
			`ev`.`created_date` AS `event_created_date`,
			`ev`.`last_modified_date` AS `event_last_modified_date`,
			`f`.`name` AS `firm_name`,
			`f`.`id` AS `firm_id`,
			`s`.`id` AS `subspecialty_id`,
			`s`.`name` AS `subspecialty`,
			`ev`.`institution_id` AS `institution_id`,
			`i`.`name` AS `institution_name`,
			`ev`.`site_id` AS `site_id`,
			`si`.`name` AS `site_name`
		from
			((((((((`patient` `p`
		join `episode` `ep` on
			(`ep`.`patient_id` = `p`.`id`))
		left join `event` `ev` on
			(`ev`.`episode_id` = `ep`.`id`))
		left join `event_type` `et` on
			(`et`.`id` = `ev`.`event_type_id`))
		left join `firm` `f` on
			(`f`.`id` = `ep`.`firm_id`))
		left join `service_subspecialty_assignment` `ssa` on
			(`ssa`.`id` = `f`.`service_subspecialty_assignment_id`))
		left join `subspecialty` `s` on
			(`s`.`id` = `ssa`.`subspecialty_id`))
		left join `institution` `i` on
			(`i`.`id` = `ev`.`institution_id`))
		left join `site` `si` on
			(`si`.`id` = `ev`.`site_id`))
		where
			`ev`.`deleted` = 0
			and `ep`.`deleted` = 0;");

        $this->execute("DROP VIEW IF EXISTS latest_ophthalmic_diagnosis_examination_events;");
        $this->execute("DROP VIEW IF EXISTS ophthalmic_diagnosis_examination_events;");
        $this->execute("DROP VIEW IF EXISTS latest_systemic_diagnosis_examination_events;");
        $this->execute("DROP VIEW IF EXISTS systemic_diagnosis_examination_events;");
    }
}
