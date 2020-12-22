<?php

class m180815_071733_add_set_common_subspecialty_medications extends CDbMigration
{
    public function safeUp()
    {
            $common_oph_id = $this->dbConnection->createCommand()->select('id')->from('medication_usage_code')->where('usage_code = :usage_code', [':usage_code' => 'COMMON_OPH'])->queryScalar();
            $q = $this->getDbConnection()->createCommand("SELECT DISTINCT sd.site_id, 
                                                            `site`.`name` AS site_name, 
                                                            sd.subspecialty_id, 
                                                            subs.`name`AS subs_name, 
                                                            GROUP_CONCAT(drug_id) AS drug_ids
                                                            FROM site_subspecialty_drug sd 
                                                                    INNER JOIN `site` ON `site`.id = sd.site_id 
                                                                    INNER JOIN subspecialty subs on subs.id = sd.subspecialty_id 
                                                            GROUP BY sd.site_id, sd.subspecialty_id");
        foreach ($q->queryAll() as $ssd) {
            // use a variable, so that apostrophes get properly escaped during execute
            $setname = "Common " . $ssd['site_name'] . " " . $ssd['subs_name'] . " medications";
            $this->execute("INSERT INTO medication_set (`name`) VALUES (:setname)", array(":setname" => $setname));
            $ref_set_id = $this->getDbConnection()->getLastInsertID();
            $drug_ids = $ssd['drug_ids'];
            $this->execute("INSERT INTO medication_set_item (`medication_set_id`, `medication_id`, default_form_id, default_dose, default_route_id, default_frequency_id, default_dose_unit_term, default_duration_id)
                            SELECT $ref_set_id, medication.id,
                            drug.form_id,
                            NULLIF(TRIM(REGEXP_REPLACE(drug.default_dose, '[a-z -\]', '' )), '') AS 'default_dose', -- strip non numerics
                            drug.default_route_id,
                            drug.default_frequency_id,
                            drug.dose_unit,
                            drug.default_duration_id
                            FROM medication
                            LEFT JOIN drug ON drug.id = medication.source_old_id
                            WHERE source_old_id IN (".$drug_ids.")
                                    AND medication.source_subtype = 'drug'
                            ");
            $this->execute("INSERT INTO medication_set_rule (medication_set_id, subspecialty_id, site_id, usage_code_id)
                            VALUES (
                            $ref_set_id,
                            {$ssd['subspecialty_id']},
                            {$ssd['site_id']},
                            {$common_oph_id}
                            )");
        }
    }

    public function safeDown()
    {
        $common_oph_id = $this->dbConnection->createCommand()->select('id')->from('medication_usage_code')->where('usage_code = :usage_code', [':usage_code' => 'COMMON_OPH'])->queryScalar();
        $this->execute("DELETE i FROM medication_set_item i INNER JOIN medication_set s ON s.id = i.medication_set_id WHERE s.usage_code_id = {$common_oph_id}");
        $this->execute("DELETE FROM medication_set s WHERE s.usage_code_id = {$common_oph_id}");
        $this->execute("DELETE FROM medication_set_rule WHERE usage_code_id = {$common_oph_id}");
    }
}
