<?php

class m180815_071733_add_ref_set_common_subspecialty_medications extends CDbMigration
{
	public function up()
	{
        $q = $this->getDbConnection()->createCommand("SELECT DISTINCT site_id, subspecialty_id, GROUP_CONCAT(drug_id) AS drug_ids
                                                      FROM site_subspecialty_drug GROUP BY site_id, subspecialty_id");
        foreach ($q->queryAll() as $ssd) {
            $this->execute("INSERT INTO ref_set (`name`) VALUES ('Common subspecialty medications')");
            $ref_set_id = $this->getDbConnection()->getLastInsertID();
            $drug_ids = explode(",", $ssd['drug_ids']);
            $postfixed = array_map(function($e){ return "'".$e."_drug'"; }, $drug_ids);
            $this->execute("INSERT INTO ref_medication_set (`ref_set_id`, `ref_medication_id`)
                                SELECT $ref_set_id, ref_medication.id
                                FROM ref_medication
                                WHERE preferred_code IN (".implode(',', $postfixed).")
                                ");
            $this->execute("INSERT INTO ref_set_rules (ref_set_id, subspecialty_id, site_id, usage_code)
                                VALUES (
                                $ref_set_id,
                                {$ssd['subspecialty_id']},
                                {$ssd['site_id']},
                                'Common subspecialty medications'
                                )");
        }
	}

	public function down()
	{
		$this->execute("DELETE FROM ref_set_rules WHERE usage_code = 'Common subspecialty medications'");
		$this->execute("DELETE FROM ref_medication_set WHERE ref_set_id IN (SELECT id FROM ref_set WHERE `name` = 'Common subspecialty medications')");
		$this->execute("DELETE FROM ref_set WHERE `name` = 'Common subspecialty medications'");
	}
}