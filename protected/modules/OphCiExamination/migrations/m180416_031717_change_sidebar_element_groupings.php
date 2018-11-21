<?php

class m180416_031717_change_sidebar_element_groupings extends OEMigration
{
    public $moved_elements = [
        [
            'class_name' => 'Element_OphCiExamination_Refraction',
            'old_parent' => null,
            'new_parent' => 'Anterior Segment',
        ],
        [
            'class_name' => 'Element_OphCiExamination_CXL_History',
            'old_parent' => null,
            'new_parent' => 'Anterior Segment',
        ],
        [
            'class_name' => 'Element_OphCiExamination_AnteriorSegment_CCT',
            'old_parent' => null,
            'new_parent' => 'Anterior Segment',
        ],
        [
            'class_name' => 'Element_OphCiExamination_Gonioscopy',
            'old_parent' => null,
            'new_parent' => 'Anterior Segment',
        ],

        [
            'class_name' => 'Element_OphCiExamination_PcrRisk',
            'old_parent' => null,
            'new_parent' => 'Risks',
        ],

        [
            'class_name' => 'Element_OphCiExamination_Fundus',
            'old_parent' => null,
            'new_parent' => 'Retina',
        ],
        [
            'class_name' => 'Element_OphCiExamination_DRGrading',
            'old_parent' => null,
            'new_parent' => 'Retina',
        ],
        [
            'class_name' => 'Element_OphCiExamination_PosteriorPole',
            'old_parent' => null,
            'new_parent' => 'Retina',
        ],
    ];

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
        $this->insert('element_group', array(
            'name' => 'Retina',
            'event_type_id' => $this->dbConnection->createCommand()->select('id')->from('event_type')->where('name="Examination"')->queryScalar(),
            'display_order' => 160));

        foreach ($this->moved_elements as $moved_element) {
            $new_parent_id = $this->dbConnection->createCommand()
                ->select('id')
                ->from('element_group')
                ->where('name = "' . $moved_element['new_parent'] . '"')
                ->queryScalar();

            $this->update('element_type', array('element_group_id' => $new_parent_id ?: null),
                'id = :id', array(':id' => $this->getElementId($moved_element['class_name'])));
        }

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

        $this->delete('element_group', 'id NOT IN (SELECT element_group_id FROM element_type)');
    }

    public function safeDown()
    {
        foreach ($this->moved_elements as $moved_element) {
            $old_parent_id = $this->dbConnection->createCommand()
                ->select('id')
                ->from('element_group')
                ->where('name = "' . $moved_element['old_parent'] . '"')
                ->queryScalar();
            $this->update('element_type', array('element_group_id' => $old_parent_id),
                'id = :id', array(':id' => $this->getElementId($moved_element['class_name'])));
        }
    }
}