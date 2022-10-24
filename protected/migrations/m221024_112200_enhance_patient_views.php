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
    }

    public function safeDown()
    {
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
