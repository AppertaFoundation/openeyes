<?php

class m170504_140049_outcome_comments_shortcode extends OEMigration
{
    public function up()
    {
        $event_type_id = $this->dbConnection->createCommand()->select('id')->from('event_type')->where('class_name = :class_name', array(':class_name' => 'OphCiExamination'))->queryScalar();

        $this->registerShortcode($event_type_id, 'occ', 'getLetterClinicOutcomeComments', 'Clinic Outcome comments from Examination');

    }

    public function down()
    {
        $this->delete(
            'patient_shortcode',
            'default_code = :sc and method = :method',
            array(':sc' => 'occ', ':method' => 'getLetterClinicOutcomeComments')
        );
    }
}