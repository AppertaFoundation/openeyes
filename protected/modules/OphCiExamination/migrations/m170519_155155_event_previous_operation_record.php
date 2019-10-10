<?php

class m170519_155155_event_previous_operation_record extends OEMigration
{
    private static $archive_tables = array(
        'previous_operation',
    );

    protected static $archive_prefix = 'archive_';


    public function up()
    {
        $this->createElementType('OphCiExamination', 'Previous Ophthalmic History', array(
            'class_name' => 'OEModule\OphCiExamination\models\PastSurgery',
            'display_order' => 20,
            'parent_class' => 'OEModule\OphCiExamination\models\Element_OphCiExamination_History'
        ));

        $this->createOETable('et_ophciexamination_pastsurgery', array(
            'id' => 'pk',
            'event_id' => 'int(10) unsigned NOT NULL',
        ), true);

        $this->addForeignKey('et_ophciexamination_pastsurgery_ev_fk',
            'et_ophciexamination_pastsurgery', 'event_id', 'event', 'id');

        $this->createOETable('ophciexamination_pastsurgery_op', array(
            'id' => 'pk',
            'element_id' => 'int(11) NOT NULL',
            'side_id' => 'int(10) unsigned',
            'operation' => 'varchar(1024) NOT NULL',
            'date' => 'varchar(10)'
        ), true);

        $this->addForeignKey('ophciexamination_pastsurgery_op_el_fk',
            'ophciexamination_pastsurgery_op', 'element_id', 'et_ophciexamination_pastsurgery', 'id');

        $this->addForeignKey('ophciexamination_pastsurgery_op_side_fk',
            'ophciexamination_pastsurgery_op', 'side_id', 'eye', 'id');

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

        $this->dropOETable('ophciexamination_pastsurgery_op', true);
        $this->dropOETable('et_ophciexamination_pastsurgery', true);
        $event_type_id = $this->dbConnection->createCommand()->select('id')->from('event_type')->where('class_name = :class_name',
            array(':class_name' => 'OphCiExamination'))->queryScalar();
        $element_type_id = $this->dbConnection->createCommand()
            ->select('id')
            ->from('element_type')
            ->where('class_name = :class_name AND event_type_id = :eid',
                array(':class_name' => 'OEModule\OphCiExamination\models\PastSurgery', ':eid' => $event_type_id))
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