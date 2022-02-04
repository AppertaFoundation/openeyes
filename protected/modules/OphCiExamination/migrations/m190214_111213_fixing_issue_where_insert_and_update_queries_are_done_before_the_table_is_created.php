<?php

class m190214_111213_fixing_issue_where_insert_and_update_queries_are_done_before_the_table_is_created extends CDbMigration
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

    public $model_prefix = 'OEModule\\OphCiExamination\\models\\';

    private function getElementId($class_name)
    {
        $command = $this->getDbConnection()->createCommand('SELECT id FROM element_type WHERE class_name = ?');

        return $command->queryScalar(array($this->model_prefix . $class_name));
    }

    // Use safeUp/safeDown to do migration with transaction
    public function safeUp()
    {
        $group_id = $this->dbConnection->createCommand()->select('id')->from('element_group')->where('name = "Visual Function"')->queryScalar();
        if ($group_id) {
            $this->update('element_type', ['element_group_id' => $group_id], "class_name='OEModule\\\OphCiExamination\\\models\\\Element_OphCiExamination_PupillaryAbnormalities'");
        }

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

            $this->update(
                'element_type',
                array('element_group_id' => $new_parent_id ?: null),
                'id = :id',
                array(':id' => $this->getElementId($moved_element['class_name']))
            );
        }

        $this->delete('element_group', 'id NOT IN (SELECT element_group_id FROM element_type)');

        $group_id = $this->dbConnection->createCommand()->select('id')->from('element_group')->where('name = "Visual Function"')->queryScalar();

        if ($group_id) {
            $this->update('element_type', array('element_group_id' => $group_id), 'id = :id', array(':id' => $this->getElementId('Element_OphCiExamination_Refraction')));
        }
    }

    public function safeDown()
    {
        $this->update('element_type', ['element_group_id' => null], "class_name='OEModule\\\OphCiExamination\\\models\\\Element_OphCiExamination_PupillaryAbnormalities'");

        $this->delete('element_group', array(
            'name' => 'Retina',
            'event_type_id' => $this->dbConnection->createCommand()->select('id')->from('event_type')->where('name="Examination"')->queryScalar(),
            'display_order' => 160));

        foreach ($this->moved_elements as $moved_element) {
            $old_parent_id = $this->dbConnection->createCommand()
                ->select('id')
                ->from('element_group')
                ->where('name = "' . $moved_element['old_parent'] . '"')
                ->queryScalar();
            $this->update(
                'element_type',
                array('element_group_id' => $old_parent_id),
                'id = :id',
                array(':id' => $this->getElementId($moved_element['class_name']))
            );
        }

        $this->update('element_type', array('element_group_id' => null), 'id = :id', array(':id' => $this->getElementId('Element_OphCiExamination_Refraction')));
    }
}
