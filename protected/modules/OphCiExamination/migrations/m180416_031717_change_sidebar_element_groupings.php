<?php

class m180416_031717_change_sidebar_element_groupings extends OEMigration
{
    public $moved_elements = [
        [
            'class_name' => 'Element_OphCiExamination_Refraction',
            'old_parent' => null,
            'new_parent' => 'Element_OphCiExamination_AnteriorSegment',
        ],
        [
            'class_name' => 'Element_OphCiExamination_CXL_History',
            'old_parent' => null,
            'new_parent' => 'Element_OphCiExamination_AnteriorSegment',
        ],
        [
            'class_name' => 'Element_OphCiExamination_AnteriorSegment_CCT',
            'old_parent' => null,
            'new_parent' => 'Element_OphCiExamination_AnteriorSegment',
        ],
        [
            'class_name' => 'Element_OphCiExamination_Gonioscopy',
            'old_parent' => null,
            'new_parent' => 'Element_OphCiExamination_AnteriorSegment',
        ],

        [
            'class_name' => 'Element_OphCiExamination_PcrRisk',
            'old_parent' => null,
            'new_parent' => 'Element_OphCiExamination_Risks',
        ],

        [
            'class_name' => 'Element_OphCiExamination_Fundus',
            'old_parent' => null,
            'new_parent' => 'Element_OphCiExamination_PosteriorPole',
        ],
        [
            'class_name' => 'Element_OphCiExamination_DRGrading',
            'old_parent' => null,
            'new_parent' => 'Element_OphCiExamination_PosteriorPole',
        ]
    ];

    public $group_titles = [
        'Element_OphCiExamination_PosteriorPole' => 'Retina',
        'Element_OphCiExamination_Management' => 'Management',
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
        }

        // Add the group type column for labelling element groups without using the parent's name
        $this->addColumn('element_type', 'group_title', 'VARCHAR(255)');

        // Set the group title to default to the element name
        $this->update('element_type', array('group_title' => new CDbExpression('name')),
            array('parent_element_type_id IS NOT NULL'));

        // Set new group titles
        foreach ($this->group_titles as $class_name => $group_title) {
            $this->update('element_type', array('group_title' => $group_title), 'id = :id',
                array(':id' => $this->getElementId($class_name)));
        }
    }

    public function safeDown()
    {
        foreach ($this->moved_elements as $moved_element) {
            $old_parent_id = $moved_element['new_parent'] ? $this->getElementId($moved_element['old_parent']) : null;

            $this->update('element_type', array('parent_element_type_id' => $old_parent_id ?: null),
                'id = :id', array(':id' => $this->getElementId($moved_element['class_name'])));
        }

        $this->dropColumn('element_type', 'group_title');
    }
}