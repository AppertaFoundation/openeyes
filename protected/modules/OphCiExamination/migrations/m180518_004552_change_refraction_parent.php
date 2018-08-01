<?php

class m180518_004552_change_refraction_parent extends OEMigration
{
    public $moved_elements = [
        [
            'class_name' => 'Element_OphCiExamination_Refraction',

            'old_parent' => 'Element_OphCiExamination_AnteriorSegment',
            'new_parent' => 'Element_OphCiExamination_VisualFunction',

            'old_display_order' => 20,
            'new_display_order' => 40,
        ],
    ];

    public $model_prefix = 'OEModule\\OphCiExamination\\models\\';

    private function getElementId($class_name)
    {
        $command = $this->getDbConnection()->createCommand('SELECT id FROM element_type WHERE class_name = ?');

        return $command->queryScalar(array($this->model_prefix . $class_name));
    }

    public function safeUp()
    {
        foreach ($this->moved_elements as $moved_element) {
            $new_parent_id = $moved_element['new_parent'] ? $this->getElementId($moved_element['new_parent']) : null;

            $this->update('element_type', array('parent_element_type_id' => $new_parent_id ?: null),
                'id = :id', array(':id' => $this->getElementId($moved_element['class_name'])));

            if (array_key_exists('new_display_order', $moved_element)) {
                $this->update('element_type', array('display_order' => $moved_element['new_display_order']),
                    'id = :id', array(':id' => $this->getElementId($moved_element['class_name'])));
            }
        }
    }

    public function safeDown()
    {
        foreach ($this->moved_elements as $moved_element) {
            $old_parent_id = $moved_element['new_parent'] ? $this->getElementId($moved_element['old_parent']) : null;

            $this->update('element_type', array('parent_element_type_id' => $old_parent_id ?: null),
                'id = :id', array(':id' => $this->getElementId($moved_element['class_name'])));

            if (array_key_exists('old_display_order', $moved_element)) {
                $this->update('element_type', array('display_order' => $moved_element['old_display_order']),
                    'id = :id', array(':id' => $this->getElementId($moved_element['class_name'])));
            }
        }
    }
}