<?php /**
* OpenEyes
*
* (C) OpenEyes Foundation, 2019
* This file is part of OpenEyes.
* OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
* OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
* You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
*
* @package OpenEyes
* @link http://www.openeyes.org.uk
* @author OpenEyes <info@openeyes.org.uk>
* @copyright Copyright (c) 2019, OpenEyes Foundation
* @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
*/

class LocalMedicationToDmdMedicationCommand extends CConsoleCommand
{

	/**
	 * @return string
	 */
	public function getName()
	{
		return 'Update Local Medication with National Codes with existing DM+D medication data.';
	}

	/**
	 * @return string
	 */
	public function getHelp()
	{
		return <<<EOH
        
'Update Local Medication with National Codes with existing DM+D medication data
        
This command is using national code and local source type to update/merge with dm+d existing data 

USAGE
  php yiic localmedicationtodmdmedication 
         
EOH;

	}

	public function actionIndex()
	{
		$drugs_with_national_code = Drug::model()->findAll("national_code is NOT NULL");
		foreach ($drugs_with_national_code as $drug) {
			// check for medication ID
			$current_medication = Medication::model()->find("source_old_id = :old_id AND source_type='LOCAL' AND source_subtype='drug' AND deleted_date is NULL", array(":old_id" => $drug->id));
			$target_medication = Medication::model()->find("preferred_code = :national_code AND source_type='DM+D' AND deleted_date is NULL", array(":national_code" => $drug->national_code));


			if ($current_medication && $target_medication) {
				$transaction = Yii::app()->db->beginTransaction();
				if ($target_medication->source_old_id) {
					// Only drug medication has source_old_id if target medication has one it means one drug with
					// this national code was already merged in with dmd data
					$new_merge = new MedicationMerge();
					$new_merge->source_drug_id = $drug->id;
					$new_merge->source_medication_id = $current_medication->id;
					$new_merge->source_name = $drug->name;
					$new_merge->target_code = $drug->national_code;
					$new_merge->target_name = $target_medication->preferred_term;
					if ($target_medication) {
						$new_merge->target_id = $target_medication->id;
					}

					if (!$new_merge->save()) {
						$transaction->rollback();
						Yii::log("ERROR: unable to save drug " . $drug->name . "!\n");
					} else {
						$transaction->commit();
					}
				} else {
										$transaction = Yii::app()->db->beginTransaction();
					$source_old_id = $current_medication->source_old_id;
					$current_medication->attributes = $target_medication->attributes;
					$current_medication->source_old_id = $source_old_id;
					$target_medication->deleted_date = date("Y-m-d H:i:s");

					if ($current_medication->save() && $target_medication->save()) {
						$transaction->commit();
					} else {
						$transaction->rollback();
						Yii::log('Unable to update Medication with id :' . $current_medication->id .
							" with Medication id: " . $target_medication->id . " attributes");
					}
				}
			}
		}

		MedicationMerge::model()->mergeAll();
	}
}