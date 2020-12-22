<?php

class m190405_133950_create_new_element_group_for_contacts extends CDbMigration
{
    public function up()
    {
        $examination_event_type = $this->dbConnection->createCommand("SELECT id FROM event_type WHERE class_name = 'OphCiExamination'")
        ->queryScalar();
        $display_order = 20;

        $this->insert('element_group', array(
            'name' => 'Contacts',
            'event_type_id' => $examination_event_type,
            'display_order' => $display_order,
        ));
        $element_group_id = $this->dbConnection->createCommand()->select('MAX(id)')->from('element_group')->queryScalar();

        $this->update(
            'element_type',
            array('element_group_id' => $element_group_id, 'display_order' => 130),
            'class_name  = :class_name',
            array(':class_name' => "OEModule\OphCiExamination\models\Element_OphCiExamination_Contacts")
        );

    }

    public function down()
    {
        $this->update(
            'element_type',
            array('element_group_id' => null),
            'class_name  = :class_name',
            array(':class_name' => "OEModule\OphCiExamination\models\Element_OphCiExamination_Contacts")
        );

        $examination_event_type = $this->dbConnection->createCommand("SELECT id FROM event_type WHERE class_name = 'OphCiExamination'")
            ->queryScalar();
        $this->delete(
            'element_group',
            'name = ? and event_type_id = ?',
            ['Contacts', $examination_event_type]
        );
    }
}
