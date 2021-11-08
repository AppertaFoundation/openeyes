<?php

class m210726_082517_add_next_steps_element extends OEMigration
{
    // Use safeUp/safeDown to do migration with transaction
    public function safeUp()
    {
        $this->insert('element_group', array(
            'name' => 'Next Steps',
            'event_type_id' => $this->getIdOfEventTypeByClassName('OphCiExamination'),
            'display_order' => 220,
        ));

        $this->createElementType(
            'OphCiExamination',
            'Next Steps',
            array(
                'class_name' => 'OEModule\OphCiExamination\models\Element_OphCiExamination_NextSteps',
                'display_order' => 541,
                'group_name' => 'Next Steps',
                'default' => 0,
                'required' => 0,
            )
        );

        $this->createOETable(
            'et_ophciexamination_next_steps',
            [
                'id' => 'pk',
                'event_id' => 'int(10) unsigned',
            ],
            true
        );

        $this->addForeignKey('et_ophciexamination_next_steps_eid', 'et_ophciexamination_next_steps', 'event_id', 'event', 'id');
    }

    public function safeDown()
    {
        $this->dropForeignKey('et_ophciexamination_next_steps_eid', 'et_ophciexamination_next_steps');
        $this->dropOETable('et_ophciexamination_next_steps', true);

        $id = $this->getIdOfElementTypeByClassName('OEModule\OphCiExamination\models\Element_OphCiExamination_NextSteps');
        $this->delete('element_type', 'id = ?', [$id]);

        $this->delete('element_group', 'name = ?', ['Next Steps']);
    }
}
