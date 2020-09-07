<?php

use OEModule\OphCiExamination\models\OphCiExaminationAllergy;

/**
 * Class MedicationSetImportCommand
 */
class MedicationSetImportCommand extends CConsoleCommand
{
    private $spreadsheet;
    private $validTypes = array("VTM", "VMP", "SET", "ROUTE");
    private $hiddenSets = array(
        "medication_management",
        "Allergy_Acetazolamide",
        "Allergy_Atropine",
        "Allergy_Brimonidine",
        "Allergy_Carbamezapine",
        "Allergy_Cephalosporins",
        "Allergy_Fluorescein",
        "Allergy_Iodine",
        "Allergy_NSAIDs",
        "Allergy_Opiates",
        "Allergy_Penicillin",
        "Allergy_Phenytoin",
        "Allergy_Sulphonamides",
        "Allergy_Suxamethonium",
        "Allergy_Tetracycline"
    );

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
        $t = microtime(true);
        echo "\n[" . (date("Y-m-d H:i:s")) . "] MedicationSetImport started ... \n";
        $this->spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($filename);
        $this->addMedicationAttributeToMedicationSet('Preservative free', 'PRESERVATIVE_FREE', '0001');

        for ($i = 0; $i < $this->spreadsheet->getSheetCount(); $i++) {
            $worksheet = $this->spreadsheet->getSheet($i);
            $this->createAutomaticSet($worksheet->getTitle(), $this->processSheetCells($worksheet));
        }
        echo "\n[" . (date("Y-m-d H:i:s")) . "] MedicationSetImport finished ... OK - took: " . (microtime(true) - $t) . "s\n";
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
                        $medicationSetAutoRuleMedications[$key] = new MedicationSetAutoRuleMedication();
                        $medicationSetAutoRuleMedications[$key]->medication_id = $medication->id;
                        $medicationSetAutoRuleMedications[$key]->include_parent = 1;
                        $medicationSetAutoRuleMedications[$key]->include_children = 1;
                        $new_set->medicationSetAutoRuleMedications = $medicationSetAutoRuleMedications;
                    } else {
                        echo "Missing " . $row["type"] . ": " . $row["snomed"] . " || " . $row["name"] . " || from medication table\n";
                    }
                    break;
                case "SET":
                    $set = MedicationSet::model()->find('name = :set_name', array(':set_name' => $row["name"]));
                    if ($set) {
                        $medicationSetAutoRuleSetMemberships[$key] = new MedicationSetAutoRuleSetMembership();
                        $medicationSetAutoRuleSetMemberships[$key]->source_medication_set_id = $set->id;
                        $new_set->medicationSetAutoRuleSetMemberships = $medicationSetAutoRuleSetMemberships;
                    } else {
                        echo "Missing " . $row["type"] . ": " . $row["snomed"] . " || " . $row["name"] . " || from medication_set table\n";
                    }
                    break;
                case "ROUTE":
                    $route_option = MedicationAttributeOption::model()->find('description = :description', array('description' => $row["name"]));
                    if ($route_option) {
                        $medicationAutoRuleAttributes[$key] = new MedicationSetAutoRuleAttribute();
                        $medicationAutoRuleAttributes[$key]->medication_attribute_option_id = $route_option->id;
                        $new_set->medicationAutoRuleAttributes = $medicationAutoRuleAttributes;
                    } else {
                        echo "Missing " . $row["type"] . ": " . $row["snomed"] . " || " . $row["name"] . " || from medication_attribute_option table\n";
                    }
                    break;
            }
        }
        $new_set->automatic = 1;

        // Determine if the set should be hidden or visible
        (in_array($new_set->name, $this->hiddenSets)) ? $new_set->hidden = 1 : $new_set->hidden = 0;

        $trans = Yii::app()->db->beginTransaction();

        try {
            if ($new_set->save()) {
                $new_set->saveAutoMeds();
                if ($current_set) {
                    \MedicationSetAutoRuleSetMembership::model()->updateAll(['source_medication_set_id' => $new_set->id], 'source_medication_set_id = :set_id', [':set_id' => $current_set->id]);
                    \MedicationSetAutoRuleSetMembership::model()->updateAll(['target_medication_set_id' => $new_set->id], 'target_medication_set_id = :set_id', [':set_id' => $current_set->id]);
                    \MedicationSetAutoRuleAttribute::model()->updateAll(['medication_set_id' => $new_set->id], 'medication_set_id = :set_id', [':set_id' => $current_set->id]);
                    // \MedicationSetAutoRuleMedication::model()->updateAll(['medication_set_id' => $new_set->id], 'medication_set_id = :set_id', [':set_id' => $current_set->id]);
                    \MedicationSetItem::model()->updateAll(['medication_set_id' => $new_set->id], 'medication_set_id = :set_id', [':set_id' => $current_set->id]);
                    \MedicationSetRule::model()->updateAll(['medication_set_id' => $new_set->id], 'medication_set_id = :set_id', [':set_id' => $current_set->id]);
                    OphCiExaminationAllergy::model()->updateAll(['medication_set_id' => $new_set->id], 'medication_set_id = :set_id', [':set_id' => $current_set->id]);



                    foreach ($risk_tags as $risk_tag) {
                        $risk_tag->medication_set_id = $new_set->id;
                        $risk_tag->update(['medication_set_id']);
                    }

                    $current_set->delete();
                }

                if ($new_set->name === 'Glaucoma') {
                    $oescape_usage_code = MedicationUsageCode::model()->find('usage_code=?', array('OEScape'));
                    $new_set->addUsageCode($oescape_usage_code, 'Glaucoma');
                }

                $trans->commit();
            } else {
                echo '<pre>' . print_r($new_set->getErrors(), true) . '</pre>';
                $trans->rollback();
                $msg = "ERROR: unable to save set " . $set_name . "!";
                echo $msg . "\n";
                \OELog::log($msg);
            }
        } catch (\Exception $exception) {
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

    private function addMedicationAttributeToMedicationSet($medication_set_name, $medication_attr_name, $value)
    {
        $medication_set = MedicationSet::model()->find('name=?', array($medication_set_name));
        $medication_attribute = MedicationAttribute::model()->find('name=?', array($medication_attr_name));
        if ($medication_set && $medication_attribute) {
            $medication_set->hidden = 1;
            $medication_set->save();
            $medication_set->addMedicationAttribute($medication_attribute, $value);
        }
    }
}
