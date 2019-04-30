<?php

class m190404_135620_create_contacts_examination_element extends OEMigration
{
    public function up()
    {
        $this->createOETable('et_ophciexamination_contacts', array(
            'id' => 'pk',
            'event_id' => 'INT(10) UNSIGNED NOT NULL DEFAULT 0',
        ), true);


        $this->addForeignKey('et_ophciexamination_contact_ev_fk', 'et_ophciexamination_contacts', 'event_id', 'event', 'id');

        $this->createElementType('OphCiExamination', 'Contacts', array(
            'class_name' => 'OEModule\OphCiExamination\models\Element_OphCiExamination_Contacts',
            'display_order' => 1
        ));
    }

    public function down()
    {
        $this->dropOETable('et_ophciexamination_contacts');
    }
}