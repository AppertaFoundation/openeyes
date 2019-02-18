<?php

class m180731_004552_change_pcr_risk_parent extends OEMigration
{
    public function up()
    {
        $this->update('element_type', array('element_group_id' => null), 'class_name = :class_name', array(
            ':class_name' => "OEModule\OphCiExamination\models\Element_OphCiExamination_PcrRisk",
        ));
    }

    public function down()
    {
        echo "no down function provided";
    }
}
