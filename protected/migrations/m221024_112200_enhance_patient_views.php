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
    }

    public function safeDown()
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
