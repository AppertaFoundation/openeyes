<?php

use PhpOffice\PhpSpreadsheet\Spreadsheet;

/**
 * Class UpdateDrugNationalCodesCommand
 */
class UpdateDrugNationalCodesCommand extends CConsoleCommand
{
    private $spreadsheet;

    /**
     * @return string
     */
    public function getName()
    {
        return 'Update National Codes for existing formulary drugs Command.';
    }

    /**
     * @return string
     */
    public function getHelp()
    {
        return <<<EOH
        
Update National Codes for existing formulary drugs
        
This command imports the national codes for existing formulary drugs from an XLSX file containing the following columns:
Openeyes Term; Code; DM+D Term

USAGE
  php yiic updatedrugnationalcodes --filename=[filename.xlsx]
         
EOH;
        
    }

    /**
     * @param $filename
     */
    public function actionIndex($filename)
    {
        $this->spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($filename);

        $worksheet = $this->spreadsheet->getSheet(0);
        $this->processSheetCells($worksheet);
        
    }


    /**
     * @param $worksheet
     * @return array
     */
    private function processSheetCells($worksheet)
    {
        $cells = array();
        $maxRows = $worksheet->getHighestRow();

        for($row=1;$row<=$maxRows;$row++)
        {
            $name = $worksheet->getCell("A".$row);
            $snomed = $worksheet->getCell("B".$row);
            $dmd_name = $worksheet->getCell("C".$row);
            $this->updateDrug($name, $snomed, $dmd_name);
        }
        // run medication_merge!
        MedicationMerge::model()->mergeAll();
    }

    /**
     * @param $drug_name
     * @param $national_code
     */
    private function updateDrug($drug_name, $national_code, $dmd_name)
    {
        // search for existing drug
        $current_drug = Drug::model()->find("name = :drug_name AND (national_code='' OR national_code IS NULL)", array(':drug_name' => $drug_name));
        if($current_drug)
        {
            // set values for the drug
            $current_drug->national_code=$national_code;

            // check for medication ID
            $current_medication = Medication::model()->find("source_old_id = :old_id AND source_type='LEGACY' AND source_subtype='drug'", array(":old_id"=>$current_drug->id));
            $target_medication = Medication::model()->find("preferred_code = :national_code AND source_type='DM+D'", array(":national_code"=>$national_code));

            $new_merge = new MedicationMerge();
            $new_merge->source_drug_id = $current_drug->id;
            if($current_medication)
            {
                $new_merge->source_medication_id = $current_medication->id;
            }
            $new_merge->source_name = $current_drug->name;
            $new_merge->target_code = $national_code;
            $new_merge->target_name = $dmd_name;
            if($target_medication)
            {
                $new_merge->target_id = $target_medication->id;
            }

            $trans = Yii::app()->db->beginTransaction();

            if(!$current_drug->validate() || !$current_drug->save(false) || !$new_merge->save(false))
            {
                $trans->rollback();
                echo "ERROR: unable to save drug ".$drug_name."!\n";
            }
            else
            {
                $trans->commit();
            }
        }
    }
}