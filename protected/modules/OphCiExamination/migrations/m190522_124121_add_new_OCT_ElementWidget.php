<?php

class m190522_124121_add_new_OCT_ElementWidget extends \OEMigration
{
    public function safeUp()
    {
        $this->update('element_type', ['name' => 'OCT (Deprecated)'], 'class_name = :class_name', [':class_name' => 'OEModule\OphCiExamination\models\Element_OphCiExamination_OCT']);
        $this->createOETable('et_ophciexamination_oct_new', [
            'id' => 'pk',
            'eye_id' => "int(10) unsigned NOT NULL DEFAULT '3'",
            'event_id' => 'int(10) unsigned NOT NULL',
        ], true);

        $this->addForeignKey('et_ophciexamination_oct_new_ev_fk', 'et_ophciexamination_oct_new', 'event_id', 'event', 'id');
        $this->addForeignKey('et_ophciexamination_oct_new_eye_fk', 'et_ophciexamination_oct_new', 'eye_id', 'eye', 'id');

        $this->createElementType('OphCiExamination', 'OCT', [
            'class_name' => 'OEModule\OphCiExamination\models\OCT',
            'display_order' => 395,
            'parent_class' => 'OEModule\OphCiExamination\models\OCT',
            'group_name' => 'Investigation'
        ]);
    }

    public function safeDown()
    {
        $this->update('element_type', ['name' => 'OCT'], 'class_name = ?', ['OEModule\OphCiExamination\models\Element_OphCiExamination_OCT']);
        $this->dropForeignKey('et_ophciexamination_oct_new_ev_fk', 'et_ophciexamination_oct_new');
        $this->dropForeignKey('et_ophciexamination_oct_new_eye_fk', 'et_ophciexamination_oct_new');
        $this->dropOETable('et_ophciexamination_oct_new', true);

        $this->delete('element_type', 'class_name = :class_name', [':class_name' => 'OEModule\OphCiExamination\models\OCT']);
    }
}
