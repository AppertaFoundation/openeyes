<?php

class m220522_132937_patient_level_risks_view_update extends OEMigration
{
    public function up()
    {
        $this->createOrReplaceView('patient_risk_assignment', <<<EOSQL
select ra.id, latest.patient_id as patient_id, ra.risk_id, ra.other, ra.comments, ra.last_modified_user_id, risk.display_on_whiteboard,
ra.last_modified_date,
ra.created_user_id,
ra.created_date from ophciexamination_history_risks_entry as ra
    join ophciexamination_risk risk on ra.risk_id = risk.id
	join et_ophciexamination_history_risks element on ra.element_id = element.id
	join latest_history_risk_examination_events latest on element.event_id = latest.event_id
    join event e on e.id = latest.event_id
where ra.has_risk = true and e.deleted = 0
EOSQL
        );
    }

    public function down()
    {
        $this->dropView('patient_risk_assignment');

        $this->createOrReplaceView('patient_risk_assignment', <<<EOSQL
select ra.id, latest.patient_id as patient_id, ra.risk_id, ra.other, ra.comments, ra.last_modified_user_id, risk.display_on_whiteboard,
ra.last_modified_date,
ra.created_user_id,
ra.created_date from ophciexamination_history_risks_entry as ra
    join ophciexamination_risk risk on ra.risk_id = risk.id
	join et_ophciexamination_history_risks element on ra.element_id = element.id
	join latest_history_risk_examination_events latest on element.event_id = latest.event_id
where ra.has_risk = true
EOSQL
        );
    }
}
