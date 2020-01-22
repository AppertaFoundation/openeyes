<?php

class m200121_154701_add_short_names_to_doctor_grade extends OEMigration
{
    public function up()
    {
        $short_names = ['Specialty trainee (year 1)' => 'ST1', 'Specialty trainee (year 2)' => 'ST2', 'Specialty trainee (year 3)' => 'ST3', 'Specialty trainee (year 4)' => 'ST4', 'Specialty trainee (year 5)' => 'ST5', 'Specialty trainee (year 6)' => 'ST6'];
        $this->addColumn('doctor_grade', 'short_name', 'varchar(255)');

        foreach ($this->getDbConnection()->createCommand('SELECT grade from doctor_grade')->queryAll() as $row) {
            if (array_key_exists($row['grade'], $short_names)) {
                $this->update('doctor_grade', array('short_name' => $short_names[$row['grade']]), "grade='{$row['grade']}'");
            } else {
                $this->update('doctor_grade', array('short_name' => $row['grade']), "grade='{$row['grade']}'");
            }
        }
    }

    public function down()
    {
        $this->dropColumn('doctor_grade', 'short_name');
    }
}