<?php

class m170920_050016_patient_medication_assignment_view extends CDbMigration
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
        $this->createView('medication_examination_events', <<<EOSQL
select event_date, event.created_date, event_id, patient_id from et_ophciexamination_history_medications et
join event on et.event_id = event.id
join episode on event.episode_id = episode.id
EOSQL
        );
        $this->createView('latest_medication_examination_events', <<<EOSQL
select t1.event_id, t1.patient_id from medication_examination_events t1
left outer join medication_examination_events t2
on t1.patient_id = t2.patient_id
   and (t1.event_date < t2.event_date
   		or (t1.event_date = t2.event_date and t1.created_date < t2.created_date))
where t2.patient_id is null
EOSQL
        );
        $this->createView('patient_medication_assignment', <<<EOSQL
select
  meds_entry.id,
  latest.patient_id as patient_id,
  meds_entry.drug_id,
  meds_entry.medication_drug_id,
  meds_entry.medication_name,
  meds_entry.dose,
  meds_entry.units,
  meds_entry.route_id,
  meds_entry.option_id,
  meds_entry.frequency_id,
  meds_entry.start_date,
  meds_entry.end_date,
  meds_entry.stop_reason_id,
  meds_entry.prescription_item_id,
  meds_entry.last_modified_user_id,
  meds_entry.last_modified_date,
  meds_entry.created_user_id,
  meds_entry.created_date
from ophciexamination_history_medications_entry as meds_entry 
  join et_ophciexamination_history_medications element on meds_entry.element_id = element.id
  join latest_medication_examination_events latest on element.event_id = latest.event_id
EOSQL
        );
    }

    public function down()
    {
        $this->dropView('patient_medication_assignment');
        $this->dropView('latest_medication_examination_events');
        $this->dropView('medication_examination_events');
    }
}
