<?php

class m140609_095913_rename_visual_acuity_element_visual_function extends CDbMigration
{
    public function up()
    {
        $this->dbConnection->createCommand("update element_type set name = 'Visual Function' where class_name = 'OEModule\\\\OphCiExamination\\\\models\\\\Element_OphCiExamination_VisualAcuity'")->query();
    }

    public function down()
    {
        $this->dbConnection->createCommand("update element_type set name = 'Visual Acuity' where class_name = 'OEModule\\\\OphCiExamination\\\\models\\\\Element_OphCiExamination_VisualAcuity'")->query();
    }
}
