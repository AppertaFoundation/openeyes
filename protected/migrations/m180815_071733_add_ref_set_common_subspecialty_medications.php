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
            $drug_ids = $ssd['drug_ids'];
            $this->execute("INSERT INTO ref_medication_set (`ref_set_id`, `ref_medication_id`, default_form, default_dose, default_route, default_frequency, default_dose_unit_term, default_duration)
                                SELECT $ref_set_id, ref_medication.id,
                                drug.form_id,
                                drug.default_dose,
                                drug.default_route_id,
                                drug.default_frequency_id,
                                drug.dose_unit,
                                drug.default_duration_id
                                FROM ref_medication
                                LEFT JOIN drug ON drug.id = ref_medication.source_old_id
                                WHERE source_old_id IN (".$drug_ids.")
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