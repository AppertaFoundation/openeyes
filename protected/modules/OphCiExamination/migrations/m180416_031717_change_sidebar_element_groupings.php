<?php

class m180416_031717_change_sidebar_element_groupings extends OEMigration
{
    public $renamed_elements = [
        'Element_OphCiExamination_PosteriorPole' => 'Macula',
    ];

    public $model_prefix = 'OEModule\\OphCiExamination\\models\\';

    private function getElementId($class_name)
    {
        $command = $this->getDbConnection()->createCommand('SELECT id FROM element_type WHERE class_name = ?');

        return $command->queryScalar(array($this->model_prefix . $class_name));
    }

    public function safeUp()
    {
        foreach ($this->renamed_elements as $class_name => $new_name) {
            $this->update('element_type', array('name' => $new_name), 'id = :id',
                array(':id' => $this->getElementId($class_name)));
        }

        $this->update('element_type', array('display_order' => 235), 'class_name = :class_name',
            array(':class_name' => $this->model_prefix . 'Element_OphCiExamination_Gonioscopy'));
        $this->update('element_type', array('display_order' => 240), 'class_name = :class_name',
            array(':class_name' => $this->model_prefix . 'Element_OphCiExamination_CXL_History'));
        $this->update('element_type', array('display_order' => 260), 'class_name = :class_name',
            array(':class_name' => $this->model_prefix . 'Element_OphCiExamination_BlebAssessment'));

        $this->update('element_type', array('display_order' => 260), 'class_name = :class_name',
            array(':class_name' => $this->model_prefix . 'Element_OphCiExamination_BlebAssessment'));
    }

    public function safeDown()
    {
    }
}