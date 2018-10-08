<?php

class m180731_004552_change_pcr_risk_parent extends OEMigration
{
    public function up()
    {
        $this->execute("UPDATE element_type SET `parent_element_type_id` = NULL WHERE `class_name` = 'OEModule\\\\OphCiExamination\\\\models\\\\Element_OphCiExamination_PcrRisk'");
    }

    public function down()
    {
        echo "no down function provided";
    }
}
