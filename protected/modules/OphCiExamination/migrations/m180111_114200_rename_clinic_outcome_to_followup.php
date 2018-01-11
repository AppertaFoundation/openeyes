<?php

class m180111_114200_rename_clinic_outcome_to_followup extends CDbMigration
{
    public function up()
    {
        $this->update('element_type', array('name' => 'Follow-up'), '`class_name` = :elelemt_class', array(':elelemt_class' => 'OEModule\OphCiExamination\models\Element_OphCiExamination_ClinicOutcome'));

    }

    public function down()
    {
        $this->update('element_type', array('name' => 'Clinic Outcome'), '`class_name` = :elelemt_class', array(':elelemt_class' => 'OEModule\OphCiExamination\models\Element_OphCiExamination_ClinicOutcome'));

    }
}
