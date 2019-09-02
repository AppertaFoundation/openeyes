<?php

class m180815_071733_add_set_common_subspecialty_medications extends CDbMigration
{
	public function up()
	{
        $common_oph_id = \Yii::app()->db->createCommand()->select('id')->from('medication_usage_code')->where('usage_code = :usage_code', [':usage_code' => 'COMMON_OPH'])->queryScalar();
        $q = $this->getDbConnection()->createCommand("SELECT DISTINCT site_id, subspecialty_id, GROUP_CONCAT(drug_id) AS drug_ids
                                                      FROM site_subspecialty_drug GROUP BY site_id, subspecialty_id");
        foreach ($q->queryAll() as $ssd) {
            $this->execute("INSERT INTO medication_set (`name`) VALUES ('Common subspecialty medications')");
            $ref_set_id = $this->getDbConnection()->getLastInsertID();
            $drug_ids = $ssd['drug_ids'];
            $this->execute("INSERT INTO medication_set_item (`medication_set_id`, `medication_id`, default_form_id, default_dose, default_route_id, default_frequency_id, default_dose_unit_term, default_duration_id)
                                SELECT $ref_set_id, medication.id,
                                drug.form_id,
                                drug.default_dose,
                                drug.default_route_id,
                                drug.default_frequency_id,
                                drug.dose_unit,
                                drug.default_duration_id
                                FROM medication
                                LEFT JOIN drug ON drug.id = medication.source_old_id
                                WHERE source_old_id IN (".$drug_ids.")
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

	public function down()
	{
        $common_oph_id = \Yii::app()->db->createCommand()->select('id')->from('medication_usage_code')->where('usage_code = :usage_code', [':usage_code' => 'COMMON_OPH'])->queryScalar();
		$this->execute("DELETE FROM medication_set_rule WHERE usage_code_id = {$common_oph_id}");
		$this->execute("DELETE FROM medication_set_item WHERE medication_set_id IN (SELECT id FROM medication_set WHERE `name` = 'Common subspecialty medications')");
		$this->execute("DELETE FROM medication_set WHERE `name` = 'Common subspecialty medications'");
	}
}