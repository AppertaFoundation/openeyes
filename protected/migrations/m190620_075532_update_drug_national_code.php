<?php

class m190620_075532_update_drug_national_code extends CDbMigration
{
    public function up()
    {
        $handle = fopen(Yii::app()->basePath."/migrations/data/m190620_075532_update_drug_national_code/01_Old2NewDrugmapping.csv", "r");
        if ($handle !== false) {
            while (($data = fgetcsv($handle, 1000, ",")) !== false) {
                $this->update('drug', ['national_code' => $data[1]], 'name = :name AND (national_code = "" OR national_code IS NULL)', [':name' => $data[0]]);
            }
            fclose($handle);
        }
    }

    public function down()
    {
        echo "m190708_075532_update_drug_national_code does not support migration down.\n";
        return true;
    }
}
