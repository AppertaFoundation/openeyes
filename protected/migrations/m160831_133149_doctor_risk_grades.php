<?php

class m160831_133149_doctor_risk_grades extends OEMigration
{
    private $existingPcrGrades = array(
        'Consultant',
        'Locum Consultant',
        'Associate Specialist',
        'Fellow',
        'Registrar',
        'Staff Grade',
        'Trust Doctor',
        'Senior House Officer',
        'Specialty trainee (year 1)',
        'Specialty trainee (year 2)',
        'Specialty trainee (year 3)',
        'Specialty trainee (year 4)',
        'Specialty trainee (year 5)',
        'Specialty trainee (year 6)',
        'Specialty trainee (year 7)',
        'Foundation Year 1 Doctor',
        'Foundation Year 2 Doctor',
        'GP with a special interest in ophthalmology',
        'Community ophthalmologist',
    );

    public function up()
    {
        $this->addColumn('doctor_grade', 'pcr_risk_value', 'DECIMAL(3,2)');
        $values = array(
            'Consultant' => '1',
            'Locum Consultant' => '1',
            'Associate Specialist' => '0.87',
            'Fellow' => '1.65',
            'Staff Grade' => '0.87',
            'Trust Doctor' => '0.36',
            'Specialty trainee (year 1)' => '3.73',
            'Specialty trainee (year 2)' => '3.73',
            'Specialty trainee (year 3)' => '1.6',
            'Specialty trainee (year 4)' => '1.6',
            'Specialty trainee (year 5)' => '1.6',
            'Specialty trainee (year 6)' => '1.6',
        );

        foreach ($values as $grade => $pcrValue) {
            $this->update('doctor_grade', array('pcr_risk_value' => $pcrValue), '`grade` = "'.$grade.'"');
        }

        $this->update('doctor_grade', array('has_pcr_risk' => 0), 'pcr_risk_value IS NULL');

    }

    public function down()
    {
        $this->dropColumn('doctor_grade', 'pcr_risk_value');
        foreach ($this->existingPcrGrades as $grade) {
            $this->update('doctor_grade', array('has_pcr_risk' => 1), 'grade = "'.$grade.'"');
        }
    }

}
