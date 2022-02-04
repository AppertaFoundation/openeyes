<?php

class m210920_104101_remove_anterior_uveitis_plan_element_type extends OEMigration
{

    public function up()
    {
        $this->deleteElementType(
            'OphCiExamination',
            'OEModule\OphCiExamination\models\Element_OphCiExamination_RecurrentAnteriorUveitisManagementPlan'
        );
    }

    public function down()
    {
        echo "m210920_104101_remove_anterior_uveitis_plan_element_type does not support migration down.\n";
        return true;
    }
}
