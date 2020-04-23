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
        EventMedicationUse::$local_to_dmd_conversion = true;
        $t = microtime(true);
        echo "[" . (date("Y-m-d H:i:s")) . "] Local medication to DMD set (localmedicationtodmdmedication) ... ";
        $drugs = Drug::model()->findAll();
        foreach ($drugs as $drug) {
            // check for medication ID
            $current_medication = Medication::model()->find("source_old_id = :old_id AND source_type='LOCAL' AND source_subtype='drug' AND deleted_date is NULL", [":old_id" => $drug->id]);
            $target_medication = Medication::model()->find("preferred_code = :national_code AND source_type='DM+D' AND deleted_date is NULL", [":national_code" => $drug->national_code]);
            // Link drugs that do not have a national code set
            if (!$target_medication) {
                $target_medication = medication::model()->find("preferred_term = :local_name and source_type='dm+d' and deleted_date is null", [":local_name" => $drug->name]);
            }

            if ($current_medication && $target_medication) {
                // Only drug medication has source_old_id if target medication has one it means one drug with
                // this national code was already merged in with dmd data
                if ($target_medication->source_old_id) {
                    if (!$this->activeMedicationMergeExists($current_medication, $target_medication)) {
                        $transaction = Yii::app()->db->beginTransaction();
                        $medication_merge = $this->createMedicationMerge($current_medication, $target_medication, $drug);

                        if (!$medication_merge->save()) {
                            $transaction->rollback();
                            Yii::log("ERROR: unable to save merge of drug " . $drug->name . "!\n");
                        } else {
                            $transaction->commit();
                        }
                    }
                } else {
                    $transaction = Yii::app()->db->beginTransaction();
                    $this->updateLocalMedicationWithDmdMedicationAttributes($current_medication, $target_medication);
                     $target_medication_set_items =  MedicationSetItem::model()->findAllByAttributes(['medication_id' => $target_medication->id]);

                    foreach ($target_medication_set_items as $set_item) {
                        $current_medication_set_item = MedicationSetItem::model()->findByAttributes([
                            'medication_id' => $current_medication->id,
                            'medication_set_id' => $set_item->medication_set_id
                        ]);
                        if (isset($current_medication_set_item)) {
                            $set_item->delete();
                        } else {
                            $set_item->medication_id = $current_medication->id;
                            $set_item->save();
                        }
                    }

                    /* Auto set */
                    foreach (['event_medication_use', 'medication_allergy_assignment', 'medication_attribute_assignment', 'medication_common',
                              'medication_search_index', 'medication_set_auto_rule_medication', 'medication_set_item'] as $table_with_medication_id) {
                        try {
                            Yii::app()->db->createCommand("UPDATE {$table_with_medication_id} 
                                         SET medication_id = {$current_medication->id} 
                                         WHERE medication_id = {$target_medication->id}")->execute();
                        } catch (Exception $exception) {
                            Yii::log($exception->getMessage());
                        }
                    }

                    if ($current_medication->save() && $target_medication->save()) {
                        $transaction->commit();
                    } else {
                        $transaction->rollback();
                        Yii::log('Unable to update Medication with id :' . $current_medication->id .
                            " with Medication id: " . $target_medication->id . " attributes (MedicationSetAutoRuleMedication)");
                    }
                }
            }
        }

        MedicationMerge::model()->mergeAll();
        EventMedicationUse::$local_to_dmd_conversion = false;
        echo "OK - took: " . (microtime(true) - $t) . "s\n";
    }

    /**
     * @param Medication $source_medication
     * @param Medication $target_medication
     * @param Drug $source_drug
     * @return MedicationMerge
     */
    private function createMedicationMerge($source_medication, $target_medication, $source_drug = null)
    {
        $new_merge = new MedicationMerge();
        if ($source_drug) {
            $new_merge->source_drug_id = $source_drug->id;
            $new_merge->source_name = $source_drug->national_code;
        }

        $new_merge->source_medication_id = $source_medication->id;
        $new_merge->target_code = $target_medication->preferred_code;
        $new_merge->target_name = $target_medication->preferred_term;
        $new_merge->target_id = $target_medication->id;

        return $new_merge;
    }

    /**
     * @param Medication $current_medication
     * @param Medication $target_medication
     * @return bool
     */
    private function activeMedicationMergeExists($source_medication, $target_medication)
    {
        $medication_merge_criteria = new CDbCriteria();

        $medication_merge_criteria->addCondition('t.source_medication_id = :source_medication_id');
        $medication_merge_criteria->addCondition('t.status = :status');
        $medication_merge_criteria->addCondition('t.target_code = :target_code');

        if ($target_medication) {
            $medication_merge_criteria->addCondition('t.target_id = :target_id');
            $medication_merge_criteria->params[':target_id'] = $target_medication->id;
        }

        $medication_merge_criteria->params[':source_medication_id'] = $source_medication->id;
        $medication_merge_criteria->params[':status'] = MedicationMerge::$ACTIVE_STATUS;
        $medication_merge_criteria->params[':target_code'] = $source_medication->preferred_code;

        return MedicationMerge::model()->exists($medication_merge_criteria);
    }

    /**
     * @param Medication $current_medication
     * @param Medication $target_medication
     */
    public function updateLocalMedicationWithDmdMedicationAttributes($current_medication, $target_medication)
    {
        $source_old_id = $current_medication->source_old_id;
        $old_id = $current_medication->id;
        $current_medication->attributes = $target_medication->attributes;
        $current_medication->source_old_id = $source_old_id;
        $current_medication->id = $old_id;
        $target_medication->deleted_date = date("Y-m-d H:i:s");
    }
}
