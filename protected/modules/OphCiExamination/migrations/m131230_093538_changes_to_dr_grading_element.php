<?php

class m131230_093538_changes_to_dr_grading_element extends CDbMigration
{
    public function up()
    {
        $exam = $this->dbConnection->createCommand()->select('*')->from('event_type')->where('class_name = :class_name', array(':class_name' => 'OphCiExamination'))->queryRow();

        $this->update('element_type', array('parent_element_type_id' => null), "event_type_id = {$exam['id']} and class_name = 'Element_OphCiExamination_DRGrading'");
    }

    public function down()
    {
        $exam = $this->dbConnection->createCommand()->select('*')->from('event_type')->where('class_name = :class_name', array(':class_name' => 'OphCiExamination'))->queryRow();

        $posterior_pole = $this->dbConnection->createCommand()->select('*')->from('element_type')->where("event_type_id = {$exam['id']} and class_name = 'Element_OphCiExamination_PosteriorPole'")->queryRow();

        $this->update('element_type', array('parent_element_type_id' => $posterior_pole['id']), "event_type_id = {$exam['id']} and class_name = 'Element_OphCiExamination_DRGrading'");
    }
}
