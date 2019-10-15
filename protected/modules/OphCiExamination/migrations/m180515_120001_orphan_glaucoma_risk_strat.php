<?php

class m180515_120001_orphan_glaucoma_risk_strat extends CDbMigration
{
    public function up()
    {
        $this->execute("UPDATE element_type SET `parent_element_type_id` = NULL, `display_order` = '94', `name`='Glaucoma Risk' WHERE `class_name` = 'OEModule\\\\OphCiExamination\\\\models\\\\Element_OphCiExamination_GlaucomaRisk'");
    }

    public function down()
    {
        $this->execute("UPDATE element_type SET `parent_element_type_id` = (SELECT id from element_type WHERE 'class_name' = 'OEModule\\\\OphCiExamination\\\\models\\\\Element_OphCiExamination_Risks') WHERE `class_name` = 'OEModule\\\\OphCiExamination\\\\models\\\\Element_OphCiExamination_GlaucomaRisk'");
    }

}
