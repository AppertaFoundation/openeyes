<?php

class m150505_113604_doctor_grade extends CDbMigration
{
	private $grades = array(
		array('id'=> 1, 'grade' => 'Consultant'),
		array('id'=> 2, 'grade' => 'Associate specialist'),
		array('id'=> 3, 'grade' => 'Trust doctor'),
		array('id'=> 4, 'grade' => 'Fellow'),
		array('id'=> 5, 'grade' => 'Specialist Registrar'),
		array('id'=> 6, 'grade' => 'Senior House Officer'),
		array('id'=> 7, 'grade' => 'House officer'),
	);

	public function up()
	{
		$this->createTable(
			'doctor_grade',
			array(
				'id' => 'int(3) not null',
				'grade' => 'varchar(100) not null',
			)
		);

		$this->createIndex('doctor_grade_unique_id', 'doctor_grade', 'id', true);
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