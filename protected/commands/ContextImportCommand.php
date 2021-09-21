<?php

/**
 * Class ContextImportCommand
 */
class ContextImportCommand extends CConsoleCommand
{
    private $spreadsheet;
    /**
     * @return string
     */
    public function getName()
    {
        return 'Context Import Command.';
    }

    /**
     * @return string
     */
    public function getHelp()
    {
        return <<<EOH
        
Context Import
        
This command is able to import XLSX files

The file should have the following headers:
|PAS Code|Context Name|Institution|Subspecialty|Consultant|Cost Code|Service Enabled|Context Enabled|Active|
|Code    |String      |String     |String      |String    |Code     |Yes/No         |Yes/No         |Yes/No|

If you want a value to be Null please include 'Blank' in the cell
Make sure all the values you put in the excel document exist in the database,
check the Firm model to check for dependencies

USAGE
  php yiic contextimport --filename=[filename.xlsx]
         
EOH;
    }

    /**
     * @param $filename
     */
    public function actionIndex($filename)
    {
        $t = microtime(true);
        echo "\n[" . (date("Y-m-d H:i:s")) . "] ContextImport started ... \n";
        $this->spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($filename);

        foreach ($this->spreadsheet->getActiveSheet()->toArray() as $index => $row) {
            if ($index != 0) {
                $this->addToDB($row);
            }
        }
        echo "\n[" . (date("Y-m-d H:i:s")) . "] ContextImport finished ... OK - took: " . (microtime(true) - $t) . "s\n";
    }

    /**
     * @param $row
     */
    public function addToDB($row)
    {
        $pas_code = $row[0];
        $context_name = $row[1];
        $institution = $row[2];
        $subspecialty = $row[3];
        $consultant = $row[4];
        $cost_code = $row[5];
        $service_enabled = $row[6];
        $context_enabled = $row[7];
        $active = $row[8];

        $institution_id = Institution::model()->findByAttributes(array('name' => $institution))->id;

        $subspecialty_id = Subspecialty::model()->findByAttributes(array('name' => $subspecialty))->id;
        $service_subspecialty_assignment_id = ServiceSubspecialtyAssignment::model()->findByAttributes(array('subspecialty_id' => $subspecialty_id))->id;

        if ($consultant != "Blank") {
            $consultant_id = User::model()->findByAttributes(array('first_name' => $consultant))->id;
        }

        $firm = Null;

        (!$this->alreadyExists($context_name)) ? $firm = new \Firm : $firm = \Firm::model()->findByAttributes(array('name'=>$context_name));
        OELog::log($context_enabled);
        $firm->pas_code = ($pas_code == "Blank") ? Null : $pas_code;
        $firm->name = ($context_name == "Blank") ? Null : $context_name;
        $firm->institution_id = $institution_id;
        $firm->service_subspecialty_assignment_id = $service_subspecialty_assignment_id;
        $firm->consultant_id = ($consultant == "Blank") ? Null : $consultant_id;
        $firm->cost_code = ($cost_code == "Blank") ? Null : $cost_code;
        $firm->can_own_an_episode = ($service_enabled == "No") ? 0 : 1;
        $firm->runtime_selectable = ($context_enabled == "No") ? 0 : 1;
        $firm->active = ($active == "No") ? 0 : 1;

        (!$this->alreadyExists($context_name)) ? $firm->insert() : $firm->save(false);
    }

    public function alreadyExists($name)
    {
        $model = \Firm::model()->findByAttributes(array('name'=>$name));
        if ($model==Null) {
            return false;
        }
        else {
            return true;
        }
    }
}
