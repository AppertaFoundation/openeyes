<?php

class m170918_095600_medication_views extends OEMigration
{
    public function createView($view_name, $select)
    {
        $this->dbConnection->createCommand('create or replace view ' . $view_name . ' as ' . $select)->execute();
    }

    public function dropView($view_name)
    {
        $this->dbConnection->createCommand('drop view ' . $view_name)->execute();
    }

    public function up()
    {
        $this->createView('history_medication_examination_events', <<<EOSQL
select event_date, event.created_date, event_id, patient_id from et_ophciexamination_history_medications et
join event on et.event_id = event.id
join episode on event.episode_id = episode.id
EOSQL
        );
        $this->createView('latest_history_medication_examination_events', <<<EOSQL
select t1.event_id, t1.patient_id from history_medication_examination_events t1
left outer join history_medication_examination_events t2
on t1.patient_id = t2.patient_id
   and (t1.event_date < t2.event_date
   		or (t1.event_date = t2.event_date and t1.created_date < t2.created_date))
where t2.patient_id is null
EOSQL
        );
        $this->createView('medication', <<<EOSQL
select ma.id, latest.patient_id as patient_id, ma.drug_id, ma.route_id, ma.option_id, ma.frequency_id, ma.start_date, ma.end_date, ma.last_modified_user_id, ma.last_modified_date, ma.created_user_id, ma.created_date, ma.dose, ma.stop_reason_id, ma.medication_drug_id, ma.prescription_item_id
        from ophciexamination_history_medications_entry as ma 
	join et_ophciexamination_history_medications element on ma.element_id = element.id
	join latest_history_medication_examination_events latest on element.event_id = latest.event_id
EOSQL
        );
    }

    public function down()
    {
        $this->dropView('meication');
        $this->dropView('latest_history_medication_examination_events');
        $this->dropView('history_medication_examination_events');
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
