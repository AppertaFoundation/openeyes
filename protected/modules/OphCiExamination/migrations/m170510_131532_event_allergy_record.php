<?php

class m170510_131532_event_allergy_record extends OEMigration
{
    protected static $archive_tables = array(
        'patient_allergy_assignment',
        'allergy',
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
        $original_allergies_element_id = $this->getIdOfElementTypeByClassName(
            'OEModule\OphCiExamination\models\Element_OphCiExamination_Allergy');
        $this->update('element_type',
            array('default' => false),
            'id = :eid',
            array(':eid' => $original_allergies_element_id));

        $display_order = $this->dbConnection->createCommand()
            ->select(array('display_order'))
            ->from('element_type')
            ->where('id = :id', array(':id' => $original_allergies_element_id))->queryScalar();

        $this->createElementType('OphCiExamination', 'Allergies', array(
            'class_name' => 'OEModule\OphCiExamination\models\Allergies',
            'display_order' => $display_order,
            'parent_class' => 'OEModule\OphCiExamination\models\Element_OphCiExamination_History'
        ));

        $this->createOETable('et_ophciexamination_allergies', array(
            'id' => 'pk',
            'event_id' => 'int(10) unsigned',
            'no_allergies_date' => 'datetime'
        ), true);

        $this->addForeignKey('et_ophciexamination_allergies_ev_fk',
            'et_ophciexamination_allergies', 'event_id', 'event', 'id');

        $this->duplicateTable(
            'allergy',
            'ophciexamination_allergy',
            array(
                'name' => 'varchar(40)',
                'display_order' => 'tinyint(3)'
            ));

        $this->createOETable('ophciexamination_allergy_entry', array(
            'id' => 'pk',
            'element_id' => 'int(11)',
            'allergy_id' => 'int(11)',
            'other' => 'varchar(255)',
            'comments' => 'varchar(255)'
        ), true);

        $this->addForeignKey('ophciexamination_allergy_entry_el_fk',
            'ophciexamination_allergy_entry', 'element_id', 'et_ophciexamination_allergies', 'id');
        $this->addForeignKey('ophciexamination_allergy_entry_al_fk',
            'ophciexamination_allergy_entry', 'allergy_id', 'ophciexamination_allergy', 'id');

        foreach (static::$archive_tables as $table) {
            $this->renameTable($table, static::$archive_prefix . $table);
            $this->renameTable($table . '_version', static::$archive_prefix . $table . '_version');
        }

        $this->renameColumn('patient', 'no_allergies_date', static::$archive_prefix . 'no_allergies_date');
        $this->renameColumn('patient_version', 'no_allergies_date', static::$archive_prefix . 'no_allergies_date');

        $this->createView('allergy_examination_events', <<<EOSQL
select event_date, event.created_date, event_id, patient_id from et_ophciexamination_allergies et
join event on et.event_id = event.id
join episode on event.episode_id = episode.id
EOSQL
);
        $this->createView('latest_allergy_examination_events', <<<EOSQL
select t1.event_id, t1.patient_id from allergy_examination_events t1
left outer join allergy_examination_events t2
on t1.patient_id = t2.patient_id
   and (t1.event_date < t2.event_date
   		or (t1.event_date = t2.event_date and t1.created_date < t2.created_date))
where t2.patient_id is null
EOSQL
);
        $this->createView('patient_allergy_assignment', <<<EOSQL
select aa.id, latest.patient_id as patient_id, aa.allergy_id, aa.other, aa.comments, aa.last_modified_user_id,
aa.last_modified_date,
aa.created_user_id,
aa.created_date from ophciexamination_allergy_entry as aa 
	join et_ophciexamination_allergies element on aa.element_id = element.id
	join latest_allergy_examination_events latest on element.event_id = latest.event_id
EOSQL
);
        $this->createView('allergy', 'select * from ophciexamination_allergy');
    }

    public function down()
    {
        $this->dropView('allergy');
        $this->dropView('patient_allergy_assignment');
        $this->dropView('latest_allergy_examination_events');
        $this->dropView('allergy_examination_events');

        $this->renameColumn('patient', static::$archive_prefix . 'no_allergies_date', 'no_allergies_date');
        $this->renameColumn('patient_version', static::$archive_prefix . 'no_allergies_date', 'no_allergies_date');
        foreach (static::$archive_tables as $table) {
            $this->renameTable(static::$archive_prefix . $table, $table);
            $this->renameTable(static::$archive_prefix . $table . '_version', $table . '_version');
        }

        $this->dropOETable('ophciexamination_allergy_entry', true);
        $this->dropOETable('ophciexamination_allergy', true);
        $this->dropOETable('et_ophciexamination_allergies', true);

        $event_type_id = $this->dbConnection->createCommand()->select('id')->from('event_type')->where('class_name = :class_name',
            array(':class_name' => 'OphCiExamination'))->queryScalar();
        $element_type_id = $this->dbConnection->createCommand()
            ->select('id')
            ->from('element_type')
            ->where('class_name = :class_name AND event_type_id = :eid',
                array(':class_name' => 'OEModule\OphCiExamination\models\Allergies', ':eid' => $event_type_id))
            ->queryScalar();
        $this->delete('ophciexamination_element_set_item', 'element_type_id = :element_type_id',
            array(':element_type_id' => $element_type_id));
        $this->delete('element_type', 'id = :id',
            array(':id' => $element_type_id));
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