<?php

class m180606_055445_rename_management_plan_elements extends OEMigration
{
    public function safeUp()
    {
        $this->update(
            'element_type',
            array('name' => 'Glaucoma Overall Plan'),
            'class_name = :class_name',
            array(':class_name' => 'OEModule\OphCiExamination\models\Element_OphCiExamination_OverallManagementPlan')
        );

        $this->update(
            'element_type',
            array('name' => 'Glaucoma Current Plan'),
            'class_name = :class_name',
            array(':class_name' => 'OEModule\OphCiExamination\models\Element_OphCiExamination_CurrentManagementPlan')
        );
    }

    public function safeDown()
    {
        $this->update(
            'element_type',
            array('name' => 'Glaucoma Overall Management Plan'),
            'class_name = :class_name',
            array(':class_name' => 'OEModule\OphCiExamination\models\Element_OphCiExamination_OverallManagementPlan')
        );

        $this->update(
            'element_type',
            array('name' => 'Glaucoma Current Management Plan'),
            'class_name = :class_name',
            array(':class_name' => 'OEModule\OphCiExamination\models\Element_OphCiExamination_CurrentManagementPlan')
        );
    }
}
