<?php

class m160318_132703_new_doctor_grades extends CDbMigration
{
    public function safeUp()
    {
        // we are looking for the current dataset and decide if we need to run the import again

        $rowNum = $this->dbConnection->createCommand()->select('count(*) as rownum')->from('doctor_grade')->queryRow();

        // the old dataset had 7 rows
        if ($rowNum['rownum'] < 8) {
            $doctor_grades = array(
                '1' => 'Consultant',
                '2' => 'Locum Consultant',
                '3' => 'Associate Specialist',
                '4' => 'Fellow',
                '5' => 'Registrar',
                '6' => 'Staff Grade',
                '7' => 'Trust Doctor',
                '8' => 'Senior House Officer',
                '9' => 'Specialty trainee (year 1)',
                '10' => 'Specialty trainee (year 2)',
                '11' => 'Specialty trainee (year 3)',
                '12' => 'Specialty trainee (year 4)',
                '13' => 'Specialty trainee (year 5)',
                '14' => 'Specialty trainee (year 6)',
                '15' => 'Specialty trainee (year 7)',
                '16' => 'Foundation Year 1 Doctor',
                '17' => 'Foundation Year 2 Doctor',
                '18' => 'GP with a special interest in ophthalmology',
                '19' => 'Community ophthalmologist',
                '20' => 'Anaesthetist',
                '21' => 'Orthoptist',
                '22' => 'Optometrist',
                '23' => 'Clinical nurse specialist',
                '24' => 'Nurse',
                '25' => 'Health Care Assistant',
                '26' => 'Ophthalmic Technician',
                '27' => 'Surgical Care Practitioner',
                '28' => 'Clinical Assistant',
                '29' => 'RG1',
                '30' => 'RG2',
                '31' => 'ODP',
                '32' => 'Administration staff',
                '33' => 'Other', );

            // insert into doctor__grade

            $this->addColumn('doctor_grade', 'has_pcr_risk', 'boolean default true');
            foreach ($doctor_grades as $id => $doctor_grade) {
                var_dump($doctor_grade);
                if ($id <= 7) {
                    $this->update('doctor_grade', array('grade' => $doctor_grade), 'id='.$id);
                } else {
                    $this->insert(
                        'doctor_grade', array('id' => $id,
                        'grade' => $doctor_grade,
                        'display_order' => $id,
                        'has_pcr_risk' => ($id >= 20) ? false : true, ));
                }
            }

            // update existing users!!
            // 2->3 (Associate Specialist -> Associate Specialist)
            // 3->7 (Trust Doctor -> Trust Doctor)
            // 6->8 (Senior House Officer -> Senior House Officer)
            // 7->9 (House Officer -> Specialty trainee (year 1))
            $this->update('user', array('doctor_grade_id' => 9), 'doctor_grade_id = 7');
            $this->update('user', array('doctor_grade_id' => 7), 'doctor_grade_id = 3');
            $this->update('user', array('doctor_grade_id' => 3), 'doctor_grade_id = 2');
            $this->update('user', array('doctor_grade_id' => 8), 'doctor_grade_id = 6');
        } else {
            return true;
        }
    }

    public function safeDown()
    {
        return true;
    }

    /*
    // Use safeUp/safeDown to do migration with transaction
    public function safeUp()
    {
    }

    public function safeDown()
    {
    }
    */
}
