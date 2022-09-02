<?php

class m220801_153813_fix_cvi_view extends OEMigration
{
    public function up()
    {
        $this->dbConnection->createCommand("
            CREATE OR REPLACE VIEW v_patient_cvi_status AS
			SELECT e.patient_id AS patient_id,
			e.event_id AS event_id,
			e.event_date AS event_date,
			cvi.cvi_status_id AS cvi_status_id,
			s.name AS cvi_status,
			cvi.element_date AS registration_date
			FROM et_ophciexamination_cvi_status cvi
			JOIN v_patient_events e ON e.event_id=cvi.event_id
			JOIN patient_oph_info_cvi_status s ON s.id=cvi.cvi_status_id
			WHERE e.event_date=(SELECT MAX(maxe.event_date)
				FROM v_patient_events maxe
				JOIN et_ophciexamination_cvi_status cvi2 ON maxe.event_id=cvi2.event_id
				WHERE maxe.patient_id=e.patient_id)
			UNION
			SELECT cvi.patient_id AS patient_id,
			NULL AS event_id,
			cvi.created_date AS event_date,
			cvi.cvi_status_id AS cvi_status_id,
			s.name AS cvi_status,
			cvi.cvi_status_date AS registration_date
			FROM patient_oph_info cvi
			JOIN patient_oph_info_cvi_status s ON s.id=cvi.cvi_status_id;")->execute();
    }

    public function down()
    {
        echo "m220801_153813_fix_cvi_view does not support migration down.\n";
        return false;
    }
}
