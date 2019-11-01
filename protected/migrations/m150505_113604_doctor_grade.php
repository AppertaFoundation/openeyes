<?php

class m150505_113604_doctor_grade extends CDbMigration
{
    private $grades = array(
        array('display_order' => 1, 'grade' => 'Consultant'),
        array('display_order' => 2, 'grade' => 'Associate specialist'),
        array('display_order' => 3, 'grade' => 'Trust doctor'),
        array('display_order' => 4, 'grade' => 'Fellow'),
        array('display_order' => 5, 'grade' => 'Specialist Registrar'),
        array('display_order' => 6, 'grade' => 'Senior House Officer'),
        array('display_order' => 7, 'grade' => 'House officer'),
    );

    public function up()
    {
        $this->createTable(
            'doctor_grade',
            array(
                'id' => 'pk',
                'grade' => 'varchar(180) not null', ## This has been reduced from 250 due to it failing when migrating up from clean db. Longest entry in db at time of typing this was 43
                'display_order' => 'int(3) not null',
            )
        );

        $this->createIndex('doctor_grade_unique_id', 'doctor_grade', 'display_order', true);
        $this->createIndex('doctor_grade_unique_grade', 'doctor_grade', 'grade', true);

        foreach ($this->grades as $grade) {
            $this->insert('doctor_grade', $grade);
        }
    }

    public function down()
    {
        $this->dropTable('doctor_grade');
    }
}
