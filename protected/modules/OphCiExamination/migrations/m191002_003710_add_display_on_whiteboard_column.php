<?php

class m191002_003710_add_display_on_whiteboard_column extends OEMigration
{
    public function alterView($view_name, $select)
    {
        $this->dbConnection->createCommand('alter view ' . $view_name . ' as ' . $select)->execute();
    }

    public function up()
    {
        $this->addOEColumn('ophciexamination_risk', 'display_on_whiteboard', 'tinyint(1) DEFAULT 1');
        $this->alterView('patient_risk_assignment', <<<EOSQL
select ra.id, latest.patient_id as patient_id, ra.risk_id, ra.other, ra.comments, ra.last_modified_user_id, risk.display_on_whiteboard,
ra.last_modified_date,
ra.created_user_id,
ra.created_date from ophciexamination_history_risks_entry as ra 
    join ophciexamination_risk risk on risk.id = ra.risk_id
	join et_ophciexamination_history_risks element on ra.element_id = element.id
	join latest_history_risk_examination_events latest on element.event_id = latest.event_id
where ra.has_risk = true
EOSQL
        );
    }

    public function down()
    {
        $this->dropOEColumn('ophciexamination_risk', 'display_on_whiteboard');
        $this->alterView('patient_risk_assignment', <<<EOSQL
select ra.id, latest.patient_id as patient_id, ra.risk_id, ra.other, ra.comments, ra.last_modified_user_id,
ra.last_modified_date,
ra.created_user_id,
ra.created_date from ophciexamination_history_risks_entry as ra 
	join et_ophciexamination_history_risks element on ra.element_id = element.id
	join latest_history_risk_examination_events latest on element.event_id = latest.event_id
where ra.has_risk = true
EOSQL
        );
    }
}