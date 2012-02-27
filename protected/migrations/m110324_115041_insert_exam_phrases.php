<?php
/**
 * OpenEyes
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2012
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2008-2011, Moorfields Eye Hospital NHS Foundation Trust
 * @copyright Copyright (c) 2011-2012, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */

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
		echo "    > inserting into exam_phrase\n";
		$command->execute();


		$command = $this->dbConnection->createCommand('SET foreign_key_checks = 1;');
		$command->execute();
    }

    public function down()
    {
		$this->truncateTable('exam_phrase');
    }
}
