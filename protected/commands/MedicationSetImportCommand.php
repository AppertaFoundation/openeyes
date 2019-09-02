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

        // Add usage code to the management set
        $rule = new MedicationSetRule();
        $rule->medication_set_id = MedicationSet::model()->find("name = 'medication_management'")->id;
        $rule->usage_code = 'Management';
        $rule->save();
    }

    /**
     * @param $setName
     * @param $setRecords
     */
    private function createAutomaticSet($setName, $setRecords)
    {
        // search for existing set in this name, create if not exists
        $current_set = MedicationSet::model()->find('name = :set_name and automatic=1', array(':set_name' => $setName));
        if (!$current_set) {
            $current_set = new MedicationSet();
        } else {
            $set_m = MedicationSetAutoRuleSetMembership::model()->findByPk($current_set->id);
            if ($set_m) {
                // this should be a command line parameter to delete all entries or just update
                $set_m->delete();
            }
        }
        $current_set->name = $setName;

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
                        echo "Missing " . $row["type"] . ": " . $row["snomed"] . " || " . $row["name"] . "\n";
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
                        echo "Missing " . $row["type"] . ": " . $row["snomed"] . " || " . $row["name"] . "\n";
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
                        echo "Missing " . $row["type"] . ": " . $row["snomed"] . " || " . $row["name"] . "\n";
                    }
                    break;
                //var_dump($row);
            }

        }
        $current_set->automatic = 1;
        $current_set->hidden = 1;

        $trans = Yii::app()->db->beginTransaction();

        if (!$current_set->validate() || !$current_set->save(false)) {
            $trans->rollback();
            echo "ERROR: unable to save set " . $setName . "!\n";
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
