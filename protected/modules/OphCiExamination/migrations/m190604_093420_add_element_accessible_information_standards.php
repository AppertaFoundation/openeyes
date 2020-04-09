<?php

class m190604_093420_add_element_accessible_information_standards extends OEMigration
{
    public function safeUp()
    {

        $this->createOETable('et_ophciexamination_accessible_information_standards', array(
            'id' => 'pk',
            'event_id' => 'int(10) unsigned NOT NULL',
            'correspondence_in_large_letters' => 'int(1) unsigned NOT NULL',
        ), true);

        $this->addForeignKey('et_ophciexamination_accessible_information_standards_ev_fk', 'et_ophciexamination_accessible_information_standards', 'event_id', 'event', 'id');

        $this->insert('element_group', array(
            'name' => 'Accessible Information Standards',
            'event_type_id' => $this->getIdOfEventTypeByClassName('OphCiExamination'),
            'display_order' => 10,
        ));

        $this->createElementType('OphCiExamination', 'Accessible Information Standards', array(
            'class_name' => 'OEModule\OphCiExamination\models\Element_OphCiExamination_CommunicationPreferences',
            'display_order' => 135,
            'group_name' => 'Accessible Information Standards',
            'default' => 0,
            'required' => 0,
        ));
    }

    public function safeDown()
    {
        $this->delete('element_group', 'name = ?', ['Accessible Information Standards']);
        $id = $this->getIdOfElementTypeByClassName('OEModule\OphCiExamination\models\Element_OphCiExamination_CommunicationPreferences');
        $this->delete('element_type', 'id = ?', [$id]);
        $this->dropForeignKey('et_ophciexamination_accessible_information_standards_ev_fk', 'et_ophciexamination_accessible_information_standards');
        $this->dropOETable('et_accessible_information_standards');
    }
}
