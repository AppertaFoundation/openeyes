<?php

class m200121_154701_add_short_names_to_doctor_grade extends OEMigration
{
    public function up()
    {
        $short_names = [
            'Consultant' => 'Cons',
            'Locum Consultant' => 'Locum Cons',
            'Associate Specialist' => 'Associate Sp',
            'Registrar' => 'StR',
            'Staff Grade' => 'Staff Gr',
            'Trust Doctor' => 'Trust Dr',
            'Specialty trainee (year 1)' => 'ST1',
            'Specialty trainee (year 2)' => 'ST2',
            'Specialty trainee (year 3)' => 'ST3',
            'Specialty trainee (year 4)' => 'ST4',
            'Specialty trainee (year 5)' => 'ST5',
            'Specialty trainee (year 6)' => 'ST6',
            'Foundation Year 1 Doctor' => 'F1',
            'Foundation Year 2 Doctor' => 'F2',
            'GP with a special interest in ophthalmology' => 'Ophthalmic GP',
            'Clinical nurse specialist' => 'Specialist nurse',
            'Health Care Assistant' => 'HCA',
            'Ophthalmic Technician' => 'OpTech',
            'Surgical Care Practitioner' => 'SCP',
            'Clinical Assistant' => 'CA',
            'Administration staff' => 'Admin',
        ];

        $this->addColumn('doctor_grade', 'short_name', 'varchar(255)');

        foreach (DoctorGrade::model()->findAll() as $doctor_grade) {
            if (array_key_exists($doctor_grade->grade, $short_names)) {
                $short_name = $short_names[$doctor_grade->grade];
            } else {
                $short_name = $doctor_grade->grade;
            }

            $this->update('doctor_grade', array('short_name' => $short_name), "grade='{$doctor_grade->grade}'");
        }
    }

    public function down()
    {
        $this->dropColumn('doctor_grade', 'short_name');
    }
}
