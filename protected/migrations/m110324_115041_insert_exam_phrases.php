<?php

class m110324_115041_insert_exam_phrases extends CDbMigration
{
    public function up()
    {
		$phrases = array(
			// Specialty ID, Part, Phraes, Order
			"(8, 2, 'Congenital Cataract', 0)",
			"(8, 2, 'unnatural cataract', 1)",
			"(8, 11, 'Congenital Cataract', 0)",
			"(8, 11, 'unnatural cataract', 1)",
			"(8, 1, 'Stroke', 0)",
			"(8, 1, 'diabetes', 1)",
			"(8, 3, 'ibuprofen', 0)",
			"(8, 4, 'pollen', 0)",
			"(8, 9, 'drug use', 0)",
			"(8, 9, 'alcoholism', 0)",
			"(8, 0, 'Loss of vision', 0)",
			"(8, 0, 'Peripheral field loss', 1)",
			"(8, 0, 'Distortion of vision', 2)",
			"(8, 0, 'Central vision disturbance', 3)",
			"(8, 14, 'Mild', 0)",
			"(8, 14, 'Moderate', 1)",
			"(8, 14, 'Severe', 2)",
			"(8, 15, 'Gradual onset', 0)",
			"(8, 15, 'Sudden onset', 1)",
			"(8, 17, 'Right eye', 0)",
			"(8, 17, 'Left eye', 1)",
			"(8, 17, 'Both eyes', 2)",
			"(8, 16, '1 day', 0)",
			"(8, 16, '2-3 days', 1)",
			"(8, 16, '1 week', 2)",
			"(8, 16, '2 weeks', 3)",
			"(8, 16, '1 month', 4)",
			"(8, 16, '6 months', 5)",
			"(8, 16, '1 year or more', 6)",
			"(8, 7, 'Topical treatment', 0)",
			"(8, 7, 'Discharge', 1)",
			"(8, 8, 'Some treatment', 0)",
			"(8, 12, 'Decision to admit', 0)",
			"(8, 12, 'Discharge', 1)",
			"(8, 12, 'Review', 2)",
			"(8, 12, 'Refer to Cataract service', 3)",
			"(8, 12, 'Refer to Glaucoma service', 3)",
			"(8, 13, '1 day', 0)",
			"(8, 13, '2-3 days', 1)",
			"(8, 13, '1 week', 2)",
			"(8, 13, '2 weeks', 3)",
			"(8, 13, '1 month', 4)",
			"(8, 13, '6 months', 5)",
			"(8, 13, '1 year or more', 6)",
		);

		$command = $this->dbConnection->createCommand('SET foreign_key_checks = 0;');
		$command->execute();

		$sql = "INSERT INTO `exam_phrase` (`specialty_id`, `part`, `phrase`, `order`) VALUES\n";
		foreach ($phrases as $values) {
			$sql .= $values;
			if ($values != end($phrases)) {
				$sql .= ", ";
			}
			$sql .= "\n";
		}
		$command = $this->dbConnection->createCommand($sql);
		echo "inserting into exam_phrase\n";
		$command->execute();


		$command = $this->dbConnection->createCommand('SET foreign_key_checks = 1;');
		$command->execute();
    }

    public function down()
    {
		$this->truncateTable('exam_phrase');
    }
}