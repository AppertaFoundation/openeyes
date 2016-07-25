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

        $this->insertOEElementType(array('OEModule\OphCiExamination\models\Element_OphCiExamination_OptomComments' => array(
            'name' => 'Optometrist Comments',
            'required' => 0,
        )), $examinationEvent);

        $this->createOETable('et_ophciexamination_optom_comments', array(
            'id' => 'pk',
            'event_id' => 'int(10) unsigned',
            'comment' => 'text'
        ), true);

    }

    public function down()
    {
        $this->delete('element_type', 'class_name = ?', array('OEModule\OphCiExamination\models\Element_OphCiExamination_OptomComments'));
        $this->dropOETable('et_ophciexamination_optom_comments', true);
    }
}