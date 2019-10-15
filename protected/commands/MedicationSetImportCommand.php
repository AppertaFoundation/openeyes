<?php

use OEModule\OphCiExamination\models\OphCiExaminationAllergy;

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
    }

    /**
     * @param $setName
     * @param $set_records
     */
    private function createAutomaticSet($set_name, $set_records)
    {
        // search for existing set in this name, create if not exists
        $current_set = MedicationSet::model()->find('name = :set_name', [':set_name' => $set_name]);
        $risk_tags = [];

        if ($current_set) {
            $risk_tags = \OphCiExaminationRiskTag::model()->findAllByAttributes(['medication_set_id' => $current_set->id]);
        }

        $new_set = new MedicationSet();

        // the 'name' attribute has a isUnique validation rule on insert and update, so we set the scenario null to
        // allow duplicate names for this command
        $new_set->scenario = null;

        $new_set->name = $set_name;

        foreach ($set_records as $key => $row) {
            switch ($row["type"]) {
                case "VTM":
                case "VMP":
                    $medication = Medication::model()->find('source_subtype = :source_subtype and ' . strtolower($row["type"]) . '_code = :code', array('source_subtype' => $row["type"], 'code' => $row["snomed"]));

                    if ($medication) {
                        $new_set->tmp_meds[] = array(
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
                        $new_set->tmp_sets[] = array(
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
                        $new_set->tmp_attrs[] = array(
                            'id' => '-1',
                            'medication_attribute_option_id' => $route_option->id
                        );
                    } else {
                        echo "Missing " . $row["type"] . ": " . $row["snomed"] . " || " . $row["name"] . " || from medication_attribute_option table\n";
                    }
                    break;
            }
        }
        $new_set->automatic = 1;
        $new_set->hidden = 1;

        $trans = Yii::app()->db->beginTransaction();

        try {
            if ($new_set->save()) {
                if ($current_set) {
                    \MedicationSetAutoRuleSetMembership::model()->updateAll(['source_medication_set_id' => $new_set->id], 'source_medication_set_id = :set_id', [':set_id' => $current_set->id]);
                    \MedicationSetAutoRuleSetMembership::model()->updateAll(['target_medication_set_id' => $new_set->id], 'target_medication_set_id = :set_id', [':set_id' => $current_set->id]);
                    \MedicationSetAutoRuleAttribute::model()->updateAll(['medication_set_id' => $new_set->id], 'medication_set_id = :set_id', [':set_id' => $current_set->id]);
                    \MedicationSetAutoRuleMedication::model()->updateAll(['medication_set_id' => $new_set->id], 'medication_set_id = :set_id', [':set_id' => $current_set->id]);
                    \MedicationSetItem::model()->updateAll(['medication_set_id' => $new_set->id], 'medication_set_id = :set_id', [':set_id' => $current_set->id]);
                    \MedicationSetRule::model()->updateAll(['medication_set_id' => $new_set->id], 'medication_set_id = :set_id', [':set_id' => $current_set->id]);
                    OphCiExaminationAllergy::model()->updateAll(['medication_set_id' => $new_set->id], 'medication_set_id = :set_id', [':set_id' => $current_set->id]);

                    foreach ($risk_tags as $risk_tag) {
                        $risk_tag->medication_set_id = $new_set->id;
                        $risk_tag->update(['medication_set_id']);
                    }

                    $current_set->delete();
                }

                $trans->commit();
            } else {
                echo '<pre>' . print_r($new_set->getErrors(), true) . '</pre>';
                $trans->rollback();
                $msg = "ERROR: unable to save set " . $set_name . "!";
                echo $msg . "\n";
                \OELog::log($msg);
            }
        } catch(\Exception $exception) {
            $trans->rollback();
            echo $exception->getMessage();
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
