<?php

class m140610_095700_move_bleb_assessment_element extends OEMigration
{
    public function up()
    {
        $element_type_id = $this->dbConnection->createCommand()->select('id')->from('element_type')->where(
            'class_name = :class_name',
            array(':class_name' => "OEModule\OphCiExamination\models\Element_OphCiExamination_AnteriorSegment")
        )->queryScalar();
        $this->update(
            'element_type',
            array('parent_element_type_id' => $element_type_id),
            'class_name = :class_name',
            array(':class_name' => 'OEModule\OphCiExamination\models\Element_OphCiExamination_BlebAssessment')
        );
    }

    public function down()
    {
        $this->update(
            'element_type',
            array('parent_element_type_id' => null),
            'class_name = :class_name',
            array(':class_name' => 'OEModule\OphCiExamination\models\Element_OphCiExamination_BlebAssessment')
        );
    }
}
