<?php

class m180828_063658_add_ref_set_common_systemic_medications extends CDbMigration
{
	public function up()
	{
	    $this->execute("INSERT INTO ref_set (`name`) VALUES ('Common systemic medications')");
	    $ref_set_id = $this->getDbConnection()->getLastInsertID();
	    $this->execute("INSERT INTO ref_set_rules (ref_set_id, usage_code) VALUES ($ref_set_id, 'Common systemic medications')");

	    foreach (CommonMedications::model()->findAll() as $med) {
	        $this->execute("INSERT INTO ref_medication_set (ref_set_id, ref_medication_id)
                                VALUES (
                                  $ref_set_id, 
                                  (SELECT id FROM ref_medicaiton WHERE source_old_id = ".$med->medication_id.")
                                )");
        }
	}

	public function down()
	{
		$this->execute("DELETE FROM ref_set_rules WHERE usage_code = 'Common systemic medications'");
		$this->execute("DELETE FROM ref_medication_set WHERE ref_set_id IN (SELECT id FROM ref_set WHERE `name` = 'Common systemic medications')");
		$this->execute("DELETE FROM ref_set WHERE `name` = 'Common systemic medications'");
	}
}