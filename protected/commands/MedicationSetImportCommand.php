<?php

use PhpOffice\PhpSpreadsheet\Spreadsheet;

/**
 * Class MedicationSetImportCommand
 */
class MedicationSetImportCommand extends CConsoleCommand
{
    private $spreadsheet;
    private $validTypes = array("VTM", "VMP", "SET", "ROUTE");

    /**
     * @return string
     */
    public function getName()
    {
        return 'Medication Set Import Command.';
    }

    /**
     * @return string
     */
    public function getHelp()
    {
        return <<<EOH
        
Medication Set Import
        
This command is able to import XLSX files where the set names are the names of the Sheets and there are 3 columns of data on each sheet:
name
SNOMED code
type [VTM,VMP,SET,ROUTE]

USAGE
  php yiic medicationsetimport --filename=[filename.xlsx]
         
EOH;

    }

    /**
     * @param $filename
     */
    public function actionIndex($filename)
    {
        $this->spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($filename);

        for ($i = 0; $i < $this->spreadsheet->getSheetCount(); $i++) {
            $worksheet = $this->spreadsheet->getSheet($i);
            $this->createAutomaticSet($worksheet->getTitle(), $this->processSheetCells($worksheet));
        }

        $management_code_id = \Yii::app()->db->createCommand()->select('id')->from('medication_usage_code')->where('usage_code = :usage_code', [':usage_code' => 'Management'])->queryScalar();

        // Add usage code to the management set
        $rule = new MedicationSetRule();
        $rule->medication_set_id = MedicationSet::model()->find("name = 'medication_management'")->id;
        $rule->usage_code_id = $management_code_id;

        $rule->save();
    }

    /**
     * @param $setName
     * @param $setRecords
     */
    private function createAutomaticSet($set_name, $setRecords)
    {
        // search for existing set in this name, create if not exists
        $current_set = MedicationSet::model()->find('name = :set_name', [':set_name' => $set_name]);

        //delete any existing sets with the same name as the new sets
        if ($current_set) {

            try {
                \MedicationSetAutoRuleAttribute::model()->deleteAllByAttributes(['medication_set_id' => $current_set->id]);
                \MedicationSetAutoRuleMedication::model()->deleteAllByAttributes(['medication_set_id' => $current_set->id]);
                \MedicationSetAutoRuleSetMembership::model()->deleteAllByAttributes(['source_medication_set_id' => $current_set->id]);
                \MedicationSetAutoRuleSetMembership::model()->deleteAllByAttributes(['target_medication_set_id' => $current_set->id]);
                \MedicationSetItem::model()->deleteAllByAttributes(['medication_set_id' => $current_set->id]);
                \MedicationSetRule::model()->deleteAllByAttributes(['medication_set_id' => $current_set->id]);
                OEModule\OphCiExamination\models\OphCiExaminationAllergy::model()->updateAll(['medication_set_id' => null], 'medication_set_id = :set_id', [':set_id' => $current_set->id]);

                // ophciexamination_risk_tag has no model
                \Yii::app()->db->createCommand()
                    ->update('ophciexamination_risk_tag',
                        ['medication_set_id' => null],
                        'medication_set_id = :set_id',
                        [':set_id' => $current_set->id]
                    );

                $current_set->delete();

            } catch (\Exception $exception) {
                \OELog::log($exception->getMessage());
            }
        }

        $current_set = new MedicationSet();

        $current_set->name = $set_name;

        foreach ($setRecords as $key => $row) {

            switch ($row["type"]) {
                case "VTM":
                case "VMP":
                    $medication = Medication::model()->find('source_subtype = :source_subtype and ' . strtolower($row["type"]) . '_code = :code', array('source_subtype' => $row["type"], 'code' => $row["snomed"]));

                    if ($medication) {
                        $current_set->tmp_meds[] = array(
                            'id' => '-1',
                            'medication_id' => $medication->id,
                            'include_parent' => 1,
                            'include_children' => 1,
                        );
                    } else {
                        echo "Missing " . $row["type"] . ": " . $row["snomed"] . " || " . $row["name"] . " || from medication table\n";
                    }
                    break;
                case "SET":
                    $set = MedicationSet::model()->find('name = :set_name', array(':set_name' => $row["name"]));
                    if ($set) {
                        $current_set->tmp_sets[] = array(
                            'id' => '-1',
                            'medication_set_id' => $set->id
                        );
                    } else {
                        echo "Missing " . $row["type"] . ": " . $row["snomed"] . " || " . $row["name"] . " || from medication_set table\n";
                    }
                    break;
                case "ROUTE":
                    $route_option = MedicationAttributeOption::model()->find('description = :description', array('description' => $row["name"]));
                    if ($route_option) {
                        $current_set->tmp_attrs[] = array(
                            'id' => '-1',
                            'medication_attribute_option_id' => $route_option->id
                        );
                    } else {
                        echo "Missing " . $row["type"] . ": " . $row["snomed"] . " || " . $row["name"] . " || from medication_attribute_option table\n";
                    }
                    break;
            }

        }
        $current_set->automatic = 1;
        $current_set->hidden = 1;

        $trans = Yii::app()->db->beginTransaction();

        if (!$current_set->validate() || !$current_set->save(false)) {
            $trans->rollback();
            echo "ERROR: unable to save set " . $set_name . "!\n";
        } else {
            $trans->commit();
        }
    }

    /**
     * @param $worksheet
     * @return array
     */
    private function processSheetCells($worksheet)
    {
        $cells = array();
        $maxRows = $worksheet->getHighestRow();

        for ($row = 1; $row <= $maxRows; $row++) {
            $nameCell = $worksheet->getCell("A" . $row);
            $snomedCell = $worksheet->getCell("B" . $row);
            $typeCell = $worksheet->getCell("C" . $row);
            if (in_array($typeCell->getValue(), $this->validTypes)) {
                $cells[] = array("type" => $typeCell->getValue(), "name" => $nameCell->getValue(), "snomed" => $snomedCell->getValue());
            }
        }
        return $cells;
    }
}
