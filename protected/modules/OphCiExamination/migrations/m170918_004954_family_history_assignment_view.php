<?php

class m170918_004954_family_history_assignment_view extends OEMigration
{
    public function createView($view_name, $select)
    {
        $this->dbConnection->createCommand('create view ' . $view_name . ' as ' . $select)->execute();
    }

    public function dropView($view_name)
    {
        $this->dbConnection->createCommand('drop view ' . $view_name)->execute();
    }

	public function up()
	{
        $this->createView('familyhist_examination_events', <<<EOSQL
select event_date, event.created_date, event_id, patient_id from et_ophciexamination_familyhistory et
join event on et.event_id = event.id
join episode on event.episode_id = episode.id
EOSQL
        );
        $this->createView('latest_familyhist_examination_events', <<<EOSQL
select t1.event_id, t1.patient_id from familyhist_examination_events t1
left outer join familyhist_examination_events t2
on t1.patient_id = t2.patient_id
   and (t1.event_date < t2.event_date
   		or (t1.event_date = t2.event_date and t1.created_date < t2.created_date))
where t2.patient_id is null
EOSQL
        );
        $this->createView('patient_family_history', <<<EOSQL
select aa.id, latest.patient_id as patient_id, aa.relative_id, aa.side_id, aa.condition_id, aa.other_relative, aa.other_condition, aa.comments, aa.last_modified_user_id,
aa.last_modified_date,
aa.created_user_id,
aa.created_date from ophciexamination_familyhistory_entry as aa 
	join et_ophciexamination_familyhistory element on aa.element_id = element.id
	join latest_familyhist_examination_events latest on element.event_id = latest.event_id
EOSQL
        );
	}

	public function down()
	{
        $this->dropView('patient_family_history');
        $this->dropView('latest_familyhist_examination_events');
        $this->dropView('familyhist_examination_events');
	}
}