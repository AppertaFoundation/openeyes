<?php

class m190604_093420_add_element_accessible_information_standards extends OEMigration
{
	public function up()
	{

        $this->createOETable('et_ophciexamination_accessible_information_standards', array(
            'id' => 'pk',
            'event_id' => 'int(10) unsigned NOT NULL',
            'last_modified_user_id' => 'int(10) unsigned',
            'last_modified_date' => 'datetime',
            'created_user_id' => 'int(10) unsigned',
            'created_date' =>'datetime',
            'correspondence_in_large_letters' => 'int(1) unsigned NOT NULL',
        ), true);

        $this->addForeignKey('et_ophciexamination_accessible_information_standards_ev_fk', 'et_ophciexamination_accessible_information_standards', 'event_id', 'event', 'id');

        $this->createElementType('OphCiExamination', 'Accessible Information Standards', array(
            'class_name' => 'OEModule\OphCiExamination\models\Element_OphCiExamination_AccessibleInformationStandards',
            'display_order' => 1
        ));
	}

	public function down()
	{
        $id = $this->getIdOfElementTypeByClassName('OEModule\OphCiExamination\models\Element_OphCiExamination_AccessibleInformationStandards');
        $this->delete('element_type', 'id = ?', array($id));
        $this->dropOETable('et_accessible_information_standards');
	}

}