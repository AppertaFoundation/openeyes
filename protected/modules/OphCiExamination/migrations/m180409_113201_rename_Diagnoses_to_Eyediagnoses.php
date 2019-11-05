<?php

class m180409_113201_rename_Diagnoses_to_Eyediagnoses extends CDbMigration
{
    public function up()
    {
        $this->execute("UPDATE element_type SET `name` = 'Ophthalmic Diagnoses' WHERE `class_name` = 'OEModule\\\\OphCiExamination\\\\models\\\\Element_OphCiExamination_Diagnoses'");
    }

    public function down()
    {
        $this->execute("UPDATE element_type SET `name` = 'Diagnoses' WHERE `class_name` = 'OEModule\\\\OphCiExamination\\\\models\\\\Element_OphCiExamination_Diagnoses'");
    }

}
