<?php

class m160725_090151_portal_exam_comment extends OEMigration
{
    public function up()
    {
        $examinationEvent = $this->dbConnection->createCommand()
            ->select('id')
            ->from('event_type')
            ->where('class_name = :class_name', array(':class_name' => 'OphCiExamination'))
            ->queryScalar();

        $displayOrder = $this->dbConnection->createCommand()
            ->select('MAX(display_order)')
            ->from('element_type')
            ->where('event_type_id = :id', array(':id' => $examinationEvent))
            ->queryScalar();

        $this->insertOEElementType(array('OEModule\OphCiExamination\models\Element_OphCiExamination_OptomComments' => array(
            'name' => 'Optometrist Comments',
            'required' => 0,
            'display_order' => $displayOrder++,
        )), $examinationEvent);

        $this->createOETable('et_ophciexamination_optom_comments', array(
            'id' => 'pk',
            'event_id' => 'int(10) unsigned',
            'ready_for_second_eye' => 'boolean default null',
            'comment' => 'text',
        ), true);
    }

    public function down()
    {
        $this->delete('element_type', 'class_name = ?', array('OEModule\OphCiExamination\models\Element_OphCiExamination_OptomComments'));
        $this->dropOETable('et_ophciexamination_optom_comments', true);
    }
}
