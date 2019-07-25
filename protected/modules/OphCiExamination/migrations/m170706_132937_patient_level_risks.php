<?php

class m170706_132937_patient_level_risks extends OEMigration
{
    protected static $archive_tables = array(
        'patient_risk_assignment',
        'risk',
    );
    protected static $archive_prefix = 'archive_';

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
        $this->createElementType('OphCiExamination', 'Risks', array(
            'class_name' => 'OEModule\OphCiExamination\models\HistoryRisks',
            'display_order' => 1,
            'parent_class' => 'OEModule\OphCiExamination\models\Element_OphCiExamination_History',
        ));
        $this->createOETable('et_ophciexamination_history_risks', array(
            'id' => 'pk',
            'event_id' => 'int(10) unsigned NOT NULL',
            'no_risks_date' => 'datetime'
        ), true);
        $this->addForeignKey('et_ophciexamination_hrisks_ev_fk',
            'et_ophciexamination_history_risks', 'event_id', 'event', 'id');


        $this->duplicateTable('risk',
            'ophciexamination_risk',
            array(
                'name' => 'varchar(255)',
                'active' => 'boolean default true'
            ));

        $this->createOETable('ophciexamination_history_risks_entry', array(
            'id' => 'pk',
            'element_id' => 'int(11) NOT NULL',
            'risk_id' => 'int(11)',
            'other' => 'varchar(255)',
            'has_risk' => 'boolean default NULL',
            'comments' => 'text'
        ), true);

        $this->addForeignKey('ophciexamination_history_risks_entry_el_fk',
            'ophciexamination_history_risks_entry', 'element_id', 'et_ophciexamination_history_risks', 'id');
        $this->addForeignKey('ophciexamination_history_risks_entry_r_fk',
            'ophciexamination_history_risks_entry', 'risk_id', 'ophciexamination_risk', 'id');

        foreach (static::$archive_tables as $table) {
            $this->renameTable($table, static::$archive_prefix . $table);
            $this->renameTable($table . '_version', static::$archive_prefix . $table . '_version');
        }

        $this->renameColumn('patient', 'no_risks_date', static::$archive_prefix . 'no_risks_date');
        $this->renameColumn('patient_version', 'no_risks_date', static::$archive_prefix . 'no_risks_date');

        $this->createView('history_risk_examination_events', <<<EOSQL
select event_date, event.created_date, event_id, patient_id from et_ophciexamination_history_risks et
join event on et.event_id = event.id
join episode on event.episode_id = episode.id
EOSQL
        );
        $this->createView('latest_history_risk_examination_events', <<<EOSQL
select t1.event_id, t1.patient_id from history_risk_examination_events t1
left outer join history_risk_examination_events t2
on t1.patient_id = t2.patient_id
   and (t1.event_date < t2.event_date
   		or (t1.event_date = t2.event_date and t1.created_date < t2.created_date))
where t2.patient_id is null
EOSQL
        );
        $this->createView('patient_risk_assignment', <<<EOSQL
select ra.id, latest.patient_id as patient_id, ra.risk_id, ra.other, ra.comments, ra.last_modified_user_id,
ra.last_modified_date,
ra.created_user_id,
ra.created_date from ophciexamination_history_risks_entry as ra 
	join et_ophciexamination_history_risks element on ra.element_id = element.id
	join latest_history_risk_examination_events latest on element.event_id = latest.event_id
where ra.has_risk = true
EOSQL
        );
        $this->createView('risk', 'select * from ophciexamination_risk');
    }

    public function down()
    {
        $this->dropView('risk');
        $this->dropView('patient_risk_assignment');
        $this->dropView('latest_history_risk_examination_events');
        $this->dropView('history_risk_examination_events');

        $this->renameColumn('patient', static::$archive_prefix . 'no_risks_date', 'no_risks_date');
        $this->renameColumn('patient_version', static::$archive_prefix . 'no_risks_date', 'no_risks_date');

        foreach (static::$archive_tables as $table) {
            $this->renameTable(static::$archive_prefix . $table, $table);
            $this->renameTable(static::$archive_prefix . $table . '_version', $table . '_version');
        }

        $this->dropOETable('ophciexamination_history_risks_entry', true);
        $this->dropOETable('ophciexamination_risk', true);
        $this->dropOETable('et_ophciexamination_history_risks', true);

        $id = $this->getIdOfElementTypeByClassName('OEModule\OphCiExamination\models\HistoryRisks');
        $this->delete('element_type', 'id = ?', array($id));
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