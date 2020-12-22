<?php

class m180529_052005_create_element_med_mgment extends OEMigration
{
    private $_class_name = 'OEModule\OphCiExamination\models\MedicationManagement';

    public function up()
    {
        $this->createElementType('OphCiExamination', 'Medication Management', array(
            'class_name' => $this->_class_name,
            'display_order' => 0,
            'parent_class' => 'OEModule\OphCiExamination\models\Element_OphCiExamination_Management'
        ));

        $this->createOETable('et_ophciexamination_medicationmanagement', array(
            'id' => 'pk',
            'event_id' => 'int(10) unsigned'
        ), true);

        $this->addForeignKey(
            'et_ophciexamination_medmgmt_ev_fk',
            'et_ophciexamination_medicationmanagement',
            'event_id',
            'event',
            'id'
        );
    }

    public function down()
    {
        $this->dropForeignKey('et_ophciexamination_medmgmt_ev_fk', 'et_ophciexamination_medicationmanagement');
        $this->dropOETable('et_ophciexamination_medicationmanagement', true);

        $e_id = $this->getIdOfElementTypeByClassName($this->_class_name);
        $this->execute("DELETE FROM element_type WHERE id = $e_id");
    }
}
