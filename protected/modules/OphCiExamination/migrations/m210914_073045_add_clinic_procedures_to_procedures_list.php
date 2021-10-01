<?php

class m210914_073045_add_clinic_procedures_to_procedures_list extends OEMigration
{
    public function safeUp()
    {
        // CSV File with following order- term, short_format, default_duration, snomed_code, snomed_term, aliases
        $procedures_file = fopen(Yii::app()->basePath."/modules/OphCiExamination/migrations/data/m210914_073045_add_clinic_procedures_to_procedures_list/clinic_procedures_list.csv", "r");
        while (($data = fgetcsv($procedures_file, 1000, ',')) !== false) {
            $procedure = $this->dbConnection->createCommand()->select('*')->from('proc')->where('term = ?', [$data[0]])->queryRow();
            // The procedure already exists in the table
            if ($procedure) {
                if ((string)$procedure['snomed_code'] === $data[3]) {
                    // The entry is as expected, add it to list of clinical procedures
                    $this->update('proc', ['is_clinic_proc' => 1], "id = :id", [':id' => $procedure['id']]);
                } else {
                    // Add ECDS Code to Procedure
                    $this->update('proc', ['ecds_code' => $data[3], 'ecds_term' => $data[4], 'is_clinic_proc' => 1], "id = :id", [':id' => $procedure['id']]);
                }
            } else {
                // Procedure does not exist, add it to the table
                $this->insert('proc', [
                    'term' => $data[0],
                    'short_format' => $data[1],
                    'default_duration' => $data[2],
                    'snomed_code' => $data[3],
                    'snomed_term' => $data[4],
                    'ecds_code' => $data[3],
                    'ecds_term' => $data[4],
                    'aliases' => $data[5],
                    'active' => 1,
                    'is_clinic_proc' => 1
                ]);
            }
        }
    }

    public function safeDown()
    {
        echo "m210914_073045_add_clinic_procedures_to_procedures_list does not support migration down.\n";
        return true;
    }
}
