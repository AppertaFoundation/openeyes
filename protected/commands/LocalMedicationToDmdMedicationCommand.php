<?php


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
            $current_medication = Medication::model()->find("source_old_id = :old_id AND source_type='LOCAL' AND source_subtype='drug'", array(":old_id" => $drug->id));
            $target_medication = Medication::model()->find("preferred_code = :national_code AND source_type='DM+D' AND deleted_date is NULL", array(":national_code" => $drug->national_code));

            if ($current_medication && $target_medication) {
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

                    $transaction = Yii::app()->db->beginTransaction();

                    if (!$new_merge->save(false)) {
                        $transaction->rollback();
                        echo "ERROR: unable to save drug ".$drug->name."!\n";
                    } else {
                        $transaction->commit();
                    }
                } else {
                    $source_old_id = $current_medication->source_old_id;
                    $current_medication->attributes = $target_medication->attributes;
                    $current_medication->source_old_id = $source_old_id;
                    $current_medication->save();

                    $target_medication->deleted_date = date("Y-m-d H:i:s");
                    $target_medication->save();
                }
            }
        }

        MedicationMerge::model()->mergeAll();
    }
}