<?php

class m180828_063658_add_ref_set_common_systemic_medications extends CDbMigration
{
	public function up()
	{
        $common_sys_id = \Yii::app()->db->createCommand()->select('id')->from('medication_usage_code')->where('usage_code = :usage_code', [':usage_code' => 'COMMON_SYSTEMIC'])->queryScalar();
	    $this->execute("INSERT INTO medication_set (`name`) VALUES ('Common systemic medications')");
	    $ref_set_id = $this->getDbConnection()->getLastInsertID();
	    $this->execute("INSERT INTO medication_set_rule (medication_set_id, usage_code_id) VALUES ($ref_set_id, $common_sys_id)");

	    foreach (CommonMedications::model()->findAll() as $med) {
	        $this->execute("INSERT INTO medication_set_item (medication_set_id, medication_id)
                                VALUES (
                                  $ref_set_id, 
                                  (SELECT id FROM medication WHERE source_old_id = ".$med->medication_id." AND source_subtype = 'medication_drug')
                                )");
        }
	}

	public function down()
	{
        $common_sys_id = \Yii::app()->db->createCommand()->select('id')->from('medication_usage_code')->where('usage_code = :usage_code', [':usage_code' => 'COMMON_SYSTEMIC'])->queryScalar();
		$this->execute("DELETE FROM medication_set_rule WHERE usage_code_id = $common_sys_id");
		$this->execute("DELETE FROM medication_set_item WHERE medication_set_id IN (SELECT id FROM medication_set WHERE `name` = 'Common systemic medications')");
		$this->execute("DELETE FROM medication_set WHERE `name` = 'Common systemic medications'");
	}
}
