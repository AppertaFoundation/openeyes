<?php

class m141208_085009_examination_diagnoses_shortcode extends CDbMigration
{
    public function up()
    {
        $exam = $this->dbConnection->createCommand()->select('id')->from('event_type')->where('class_name = :cn', array(':cn' => 'OphCiExamination'))->queryScalar();

        $this->insert('patient_shortcode', array(
                'event_type_id' => $exam,
                'default_code' => 'edf',
                'code' => 'edf',
                'method' => 'getLetterDiagnosesAndFindings',
                'description' => 'Examination diagnoses and findings',
        ));
    }

    public function down()
    {
        $exam = $this->dbConnection->createCommand()->select('id')->from('event_type')->where('class_name = :cn', array(':cn' => 'OphCiExamination'))->queryScalar();

        $this->delete('patient_shortcode', "event_type_id = $exam and default_code = 'edf'");
    }
}
