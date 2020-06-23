<?php

class m170418_111623_move_medical_lids_element_type extends CDbMigration
{
    public function up()
    {
        $this->execute(
            'UPDATE element_type JOIN element_type AS j ON j.class_name = :parent
	                  SET element_type.parent_element_type_id = j.id, element_type.display_order=0
                      WHERE element_type.class_name = :child',
            [':parent'=> 'OEModule\OphCiExamination\models\Element_OphCiExamination_AdnexalComorbidity',
            ':child'=>'OEModule\OphCiExamination\models\MedicalLids']
        );
    }

    public function down()
    {
        $this->execute(
            "UPDATE element_type SET parent_element_type_id = NULL, display_order = 55
                      WHERE class_name = :child",
            [':child'=>'OEModule\OphCiExamination\models\MedicalLids']
        );
    }
}
