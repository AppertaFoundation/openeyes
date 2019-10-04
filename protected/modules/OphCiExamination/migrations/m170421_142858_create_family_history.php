<?php

class m170421_142858_create_family_history extends OEMigration
{
    protected static $archive_tables = array(
        'family_history_relative',
        'family_history_side',
        'family_history_condition',
        'family_history');

    protected static $archive_prefix = 'archive_';

    /**
     * Create $dest table and duplicate data from $source into it
     *
     * @param $source
     * @param $dest
     * @param $cols
     */
    public function duplicateTable($source, $dest, $cols)
    {
        $this->createOETable($dest, array_merge(
            array('id' => 'pk', 'active' => 'boolean default true'), $cols
        ), true);
        $source_rows = $this->dbConnection->createCommand()
            ->select(array_keys($cols))
            ->from($source)
            ->queryAll();
        foreach ($source_rows as $row) {
            $this->insert($dest, $row);
        }
    }

    public function up()
    {
        $event_type_id = $this->dbConnection->createCommand()->select('id')->from('event_type')->where('class_name = :class_name',
            array(':class_name' => 'OphCiExamination'))->queryScalar();

        $element_types = array(
            'OEModule\OphCiExamination\models\FamilyHistory' => array(
                'name' => 'Family History',
                'display_order' => 23,
                'parent_element_type_id' => 'OEModule\OphCiExamination\models\Element_OphCiExamination_History',
            ),
        );

        $this->insertOEElementType($element_types, $event_type_id);

        $this->createOETable('et_ophciexamination_familyhistory', array(
            'id' => 'pk',
            'event_id' => 'int(10) unsigned',
            'no_family_history_date' => 'datetime',
        ), true);

        $this->addForeignKey('et_ophciexamination_familyhistory_ev_fk',
            'et_ophciexamination_familyhistory', 'event_id', 'event', 'id');

        $this->duplicateTable(
            'family_history_relative',
            'ophciexamination_familyhistory_relative',
            array(
                'name' => 'varchar(64)',
                'display_order' => 'tinyint',
                'is_other' => 'boolean'
            ));

        $this->duplicateTable(
            'family_history_side',
            'ophciexamination_familyhistory_side',
            array(
                'id' => 'pk',
                'name' => 'varchar(64)',
                'display_order' => 'tinyint',
            ));

        $this->duplicateTable(
            'family_history_condition',
            'ophciexamination_familyhistory_condition',
                array(
                'id' => 'pk',
                'name' => 'varchar(64)',
                'display_order' => 'tinyint',
                'is_other' => 'boolean'
            ));

        $this->createOETable('ophciexamination_familyhistory_entry', array(
            'id' => 'pk',
            'element_id' => 'int(11) NOT NULL',
            'relative_id' => 'int(11)',
            'side_id' => 'int(11)',
            'condition_id' => 'int(11)',
            'comments' => 'varchar(1024)',
            'other_relative' => 'varchar(255)',
            'other_condition' => 'varchar(255)'
        ), true);

        $this->addForeignKey('ophciexamination_familyhistory_entry_el_fk',
            'ophciexamination_familyhistory_entry', 'element_id', 'et_ophciexamination_familyhistory', 'id');

        $this->addForeignKey('ophciexamination_familyhistory_entry_rel_fk',
            'ophciexamination_familyhistory_entry', 'relative_id', 'ophciexamination_familyhistory_relative', 'id');

        $this->addForeignKey('ophciexamination_familyhistory_entry_side_fk',
            'ophciexamination_familyhistory_entry', 'side_id', 'ophciexamination_familyhistory_side', 'id');

        $this->addForeignKey('ophciexamination_familyhistory_entry_con_fk',
            'ophciexamination_familyhistory_entry', 'condition_id', 'ophciexamination_familyhistory_condition', 'id');

        // archive the old tables (note that the data migration command will operate on the archived table names)
        foreach (static::$archive_tables as $table) {
            $this->renameTable($table, static::$archive_prefix . $table);
            $this->renameTable($table . '_version', static::$archive_prefix . $table . '_version');
        }

        $this->renameColumn('patient', 'no_family_history_date', static::$archive_prefix . 'no_family_history_date');
        $this->renameColumn('patient_version', 'no_family_history_date', static::$archive_prefix . 'no_family_history_date');

    }

    public function down()
    {
        $this->renameColumn('patient', static::$archive_prefix . 'no_family_history_date', 'no_family_history_date');
        $this->renameColumn('patient_version', static::$archive_prefix . 'no_family_history_date', 'no_family_history_date');
        // restore archived tables
        foreach (static::$archive_tables as $table) {
            $this->renameTable(static::$archive_prefix . $table, $table);
            $this->renameTable(static::$archive_prefix . $table . '_version', $table . '_version');
        }
        $this->dropOETable('ophciexamination_familyhistory_entry', true);
        $this->dropOETable('et_ophciexamination_familyhistory', true);
        $this->dropOETable('ophciexamination_familyhistory_condition', true);
        $this->dropOETable('ophciexamination_familyhistory_side', true);
        $this->dropOETable('ophciexamination_familyhistory_relative', true);

        $event_type_id = $this->dbConnection->createCommand()->select('id')->from('event_type')->where('class_name = :class_name',
            array(':class_name' => 'OphCiExamination'))->queryScalar();

        $element_type_id = $this->dbConnection->createCommand()
            ->select('id')
            ->from('element_type')
            ->where('class_name = :class_name AND event_type_id = :eid',
                array(':class_name' => 'OEModule\OphCiExamination\models\FamilyHistory', ':eid' => $event_type_id))
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