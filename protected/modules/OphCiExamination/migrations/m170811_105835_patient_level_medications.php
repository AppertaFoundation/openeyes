<?php

class m170811_105835_patient_level_medications extends OEMigration
{
    protected static $archive_tables = array(
        'medication_stop_reason',
        'medication',
        'medication_adherence' // NB this is being archived, but this data is will not be migrated.
    );
    protected static $archive_prefix = 'archive_';

    public function up()
    {
        $this->createElementType('OphCiExamination', 'Medications', array(
            'class_name' => 'OEModule\OphCiExamination\models\HistoryMedications',
            'display_order' => 5,
            'parent_class' => 'OEModule\OphCiExamination\models\Element_OphCiExamination_History',
        ));
        $this->createOETable('et_ophciexamination_history_medications', array(
            'id' => 'pk',
            'event_id' => 'int(10) unsigned NOT NULL',
        ), true);
        $this->addForeignKey(
            'et_ophciexamination_hmeds_ev_fk',
            'et_ophciexamination_history_medications',
            'event_id',
            'event',
            'id'
        );

        $this->duplicateTable(
            'medication_stop_reason',
            'ophciexamination_medication_stop_reason',
            array(
                'name' => 'varchar(255)',
                'active' => 'boolean default true',
                'display_order' => 'int(11)'
            )
        );

        $this->createOETable('ophciexamination_history_medications_entry', array(
            'id' => 'pk',
            'element_id' => 'int(11) NOT NULL',
            'drug_id' => 'int(10) unsigned',
            'medication_drug_id' => 'int(11)',
            'medication_name' => 'varchar(511)',
            'dose' => 'varchar(255)',
            'units' => 'varchar(31)',
            'route_id' => 'int(10) unsigned',
            'option_id' => 'int(10) unsigned',
            'frequency_id' => 'int(10) unsigned',
            'start_date' => 'date NOT NULL',
            'end_date' => 'date',
            'stop_reason_id' => 'int(11)'
        ), true);

        $this->addForeignKey(
            'ophciexamination_history_meds_entry_el_fk',
            'ophciexamination_history_medications_entry',
            'element_id',
            'et_ophciexamination_history_medications',
            'id'
        );
        $this->addForeignKey(
            'ophciexamination_history_meds_entry_did_fk',
            'ophciexamination_history_medications_entry',
            'drug_id',
            'drug',
            'id'
        );
        $this->addForeignKey(
            'ophciexamination_history_meds_entry_mdid_fk',
            'ophciexamination_history_medications_entry',
            'medication_drug_id',
            'medication_drug',
            'id'
        );
        $this->addForeignKey(
            'ophciexamination_history_meds_entry_route_fk',
            'ophciexamination_history_medications_entry',
            'route_id',
            'drug_route',
            'id'
        );
        $this->addForeignKey(
            'ophciexamination_history_meds_entry_roption_fk',
            'ophciexamination_history_medications_entry',
            'option_id',
            'drug_route_option',
            'id'
        );
        $this->addForeignKey(
            'ophciexamination_history_meds_entry_freq_fk',
            'ophciexamination_history_medications_entry',
            'frequency_id',
            'drug_frequency',
            'id'
        );
        $this->addForeignKey(
            'ophciexamination_history_meds_entry_stopid_fk',
            'ophciexamination_history_medications_entry',
            'stop_reason_id',
            'ophciexamination_medication_stop_reason',
            'id'
        );

        foreach (static::$archive_tables as $table) {
            $this->renameTable($table, static::$archive_prefix . $table);
            $this->renameTable($table . '_version', static::$archive_prefix . $table . '_version');
        }
    }

    public function down()
    {
        foreach (static::$archive_tables as $table) {
            $this->renameTable(static::$archive_prefix . $table, $table);
            $this->renameTable(static::$archive_prefix . $table . '_version', $table . '_version');
        }

        $this->dropOETable('ophciexamination_history_medications_entry', true);
        $this->dropOETable('ophciexamination_medication_stop_reason', true);
        $this->dropOETable('et_ophciexamination_history_medications', true);

        $id = $this->getIdOfElementTypeByClassName('OEModule\OphCiExamination\models\HistoryMedications');
        $this->delete(
            'ophciexamination_element_set_item',
            'element_type_id = :element_type_id',
            array(':element_type_id' => $id)
        );
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
