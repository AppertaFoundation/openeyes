<?php

class m180518_004552_change_refraction_parent extends OEMigration
{

    public $model_prefix = 'OEModule\\OphCiExamination\\models\\';

    private function getElementId($class_name)
    {
        $command = $this->getDbConnection()->createCommand('SELECT id FROM element_type WHERE class_name = ?');

        return $command->queryScalar(array($this->model_prefix . $class_name));
    }

    public function safeUp()
    {
        $group_id = $this->dbConnection->createCommand()->select('id')->from('element_group')->where('name = "Visual Function"')->queryScalar();

        $this->update('element_type', array('element_group_id' => $group_id),
            'id = :id', array(':id' => $this->getElementId('Element_OphCiExamination_Refraction')));

    }

    public function safeDown()
    {
        $this->update('element_type', array('element_group_id' => null),
            'id = :id', array(':id' => $this->getElementId('Element_OphCiExamination_Refraction')));
    }
}