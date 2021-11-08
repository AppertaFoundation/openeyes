<?php

class m210721_095446_remove_element_type_entry_for_deprecated_allergy_element extends OEMigration
{
    public function up()
    {
        $this->deleteElementType('OphCiExamination', 'OEModule\OphCiExamination\models\Element_OphCiExamination_Allergy');
    }

    public function down()
    {
        $this->createElementType('OphCiExamination', 'Allergies', array(
            'class_name' => 'OEModule\OphCiExamination\models\Element_OphCiExamination_Allergy',
            'group_name' => 'History',
            'display_order' => 30
        ));
    }
}
