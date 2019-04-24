<?php

class m190321_114813_create_history_iop_element extends OEMigration
{
    public function safeUp()
    {
        $this->createOETable('et_ophciexamination_history_iop', [
            'id' => 'pk',
            'event_id' => 'INT(10) UNSIGNED NOT NULL DEFAULT 0',
            'eye_id' => "int(10) unsigned NOT NULL DEFAULT '3'",
        ], true);
        $this->addForeignKey('et_ophciexamination_history_iop_ev_fk', 'et_ophciexamination_history_iop', 'event_id', 'event', 'id');
        $this->addForeignKey('et_ophciexamination_history_iop_eye_fk', 'et_ophciexamination_history_iop', 'eye_id', 'eye', 'id');
        $this->insert('element_group', array(
            'name' => 'HistoryIOP',
            'event_type_id' => $this->dbConnection->createCommand()->select('id')->from('event_type')->where('class_name = ?', array('OphCiExamination'))->queryScalar(),
            'display_order' => 65,
        ));
        $this->createElementType('OphCiExamination', 'History IOP', [
            'class_name' => 'OEModule\OphCiExamination\models\HistoryIOP',
            'display_order' => 310,
            'group_name' => 'HistoryIOP'
        ]);
    }
    public function safeDown()
    {
        $this->delete('element_type', 'class_name = :class_name', [':class_name' => 'OEModule\OphCiExamination\models\HistoryIOP']);
        $this->delete('element_group', 'name = :element_group_name', [':element_group_name' => 'HistoryIOP']);
        $this->dropOETable('et_ophciexamination_history_iop', true);
    }
}