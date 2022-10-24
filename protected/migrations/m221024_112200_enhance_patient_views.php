<?php

class m221024_112200_enhance_patient_views extends OEMigration
{
    public function safeUp()
    {
        $this->dbConnection->createCommand("
            CREATE OR REPLACE VIEW v_patient_appointments AS
			SELECT wp.patient_id AS patient_id,
                w.name AS worklist_name,
                wp.id AS worklist_patient_id,
                GROUP_CONCAT(DISTINCT CONCAT(wa.name,':',wpa.attribute_value)) AS worklist_patient_attributes,
                w.start AS worklist_start,
                w.end AS worklist_end,
                wp.when AS scheduled_time,
                wp.last_modified_date AS last_modified_date,
                wp.created_date AS created_date,
                IFNULL(p.start_time, psc.start_time) AS arrival_time,
                IFNULL(p.end_time, psd.start_time) AS discharge_time,
                IF(p.did_not_attend='0',false,true) AS did_not_attend,
                s.id AS appointment_site_id,
                s.name AS appointment_site_name
            FROM worklist w
                JOIN worklist_patient wp ON wp.worklist_id=w.id
                JOIN worklist_attribute wa ON wa.worklist_id=w.id
                JOIN worklist_patient_attribute wpa ON wpa.worklist_attribute_id=wa.id AND wpa.worklist_patient_id=wp.id
                LEFT JOIN pathway p ON p.worklist_patient_id=wp.id
                LEFT JOIN pathway_step psc ON psc.pathway_id=p.id
                LEFT JOIN pathway_step_type pstc ON pstc.id=psc.step_type_id AND pstc.short_name='checkin'
                LEFT JOIN pathway_step psd ON psd.pathway_id=p.id
                LEFT JOIN pathway_step_type pstd ON pstd.id=psd.step_type_id AND pstc.short_name='discharge'
                LEFT JOIN event ev ON ev.worklist_patient_id=wp.id
                LEFT JOIN site s ON s.id=ev.site_id
            GROUP BY wp.id;
		")->execute();

        $this->dbConnection->createCommand("
            CREATE OR REPLACE VIEW v_patient_follow_up AS
			SELECT ep.patient_id AS patient_id,
                ev.worklist_patient_id AS worklist_patient_id,
                ocs.name AS outcome,
                s.name AS requested_location,
                ss.name AS requested_subspecialty,
                f.name AS requested_appointment_type,
                oce.followup_quantity AS follow_up_value,
                p.name AS follow_up_unit,
                ocr.name AS requested_clinician_type,
                ocrs.name AS risk_priority,
                oce.last_modified_date AS last_modified_date,
                oce.last_modified_user_id AS last_modified_user_id,
                oce.created_date AS created_date,
                oce.created_user_id AS created_user_id
            FROM et_ophciexamination_clinicoutcome eoc
                JOIN event ev ON ev.id=eoc.event_id
                JOIN episode ep ON ep.id=ev.episode_id
                JOIN ophciexamination_clinicoutcome_entry oce ON oce.element_id=eoc.id
                LEFT JOIN ophciexamination_clinicoutcome_status ocs ON ocs.id=oce.status_id
                LEFT JOIN site s ON s.id=oce.site_id
                LEFT JOIN subspecialty ss ON ss.id=oce.subspecialty_id
                LEFT JOIN firm f ON f.id=oce.context_id
                LEFT JOIN period p ON p.id=oce.followup_period_id
                LEFT JOIN ophciexamination_clinicoutcome_role ocr ON ocr.id=oce.role_id
                LEFT JOIN ophciexamination_clinicoutcome_risk_status ocrs ON ocrs.id=oce.risk_status_id;
		")->execute();

        $this->dbConnection->createCommand("
            CREATE OR REPLACE VIEW v_patient_clinical_management AS
			SELECT ep.patient_id AS patient_id,
                ev.worklist_patient_id AS worklist_patient_id,
                eom.comments AS comment,
                eom.last_modified_date AS last_modified_date,
                eom.last_modified_user_id AS last_modified_user_id,
                eom.created_date AS created_date,
                eom.created_user_id AS created_user_id
            FROM et_ophciexamination_management eom
                JOIN event ev ON ev.id=eom.event_id
                JOIN episode ep ON ep.id=ev.episode_id;
		")->execute();

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
			ev.worklist_patient_id AS last_verified_worklist_patient_id,
            od.last_modified_date AS last_modified_date,
            od.last_modified_user_id AS last_modified_user_id
		FROM et_ophciexamination_diagnoses eod
            INNER JOIN v_patient_events ev ON ev.event_id = eod.event_id 
            INNER JOIN ophciexamination_diagnosis od ON od.element_diagnoses_id = eod.id
            INNER JOIN disorder d ON d.id = od.disorder_id
            INNER JOIN specialty s ON s.id = d.specialty_id 	
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
			ev.worklist_patient_id AS last_verified_worklist_patient_id,
            od.last_modified_date AS last_modified_date,
            od.last_modified_user_id AS last_modified_user_id
		FROM et_ophciexamination_systemic_diagnoses eod
            INNER JOIN v_patient_events ev ON ev.event_id = eod.event_id 
            INNER JOIN ophciexamination_systemic_diagnoses_diagnosis od ON od.element_id = eod.id
            INNER JOIN disorder d ON d.id = od.disorder_id
            LEFT JOIN specialty s ON s.id = d.specialty_id;
		")->execute();
    }

    public function safeDown()
    {
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
            INNER JOIN latest_systemic_diagnosis_examination_events lodee ON lodee.event_id = ev.event_id AND lodee.patient_id = ev.patient_id;
		")->execute();

        $this->dbConnection->createCommand("
            DROP VIEW IF EXISTS v_patient_clinical_management;
        ")->execute();

        $this->dbConnection->createCommand("
            DROP VIEW IF EXISTS v_patient_follow_up;
        ")->execute();

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
    }
}
