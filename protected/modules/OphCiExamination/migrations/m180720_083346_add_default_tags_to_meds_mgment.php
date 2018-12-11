<?php

class m180720_083346_add_default_tags_to_meds_mgment extends CDbMigration
{
	public function up()
	{
        $transaction=$this->getDbConnection()->beginTransaction();
        try {
            $this->execute("INSERT INTO medication_set (`name`) VALUES ('Antibiotic')");
            $ref_set_id1 = Yii::app()->db->getLastInsertID();
            $this->execute("INSERT INTO medication_set (`name`) VALUES ('Ophthalmic')");
            $ref_set_id2 = Yii::app()->db->getLastInsertID();
            $this->execute("INSERT INTO medication_set_item (medication_id, medication_set_id, default_form_id, default_dose, default_route_id, default_frequency_id, default_dose_unit_term)
                            SELECT medication_id, 
                            $ref_set_id2, 
                            default_form_id, default_dose, default_route_id, default_frequency_id, default_dose_unit_term
                            FROM medication_set_item WHERE default_route_id IN (SELECT id FROM medication_route WHERE term IN ('Eye', 'Ocular', 'Interocular'));
                            ");

            $this->execute("INSERT INTO medication_set_rule (medication_set_id, usage_code) 
                            SELECT id, 'Management' FROM medication_set WHERE `name` IN ('Cytotoxic', 'Antiviral', 'Tear Film Substitute', 'Glaucoma', 'Antibiotic', 'Ophthalmic')");

            $transaction->commit();
        }
        catch(Exception $e) {
            echo "Exception: ".$e->getMessage()."\n";
            $transaction->rollback();
            return false;
        }

        return true;
	}

	public function down()
	{
        $this->execute("DELETE FROM medication_set WHERE `name` IN ('Antibiotic', 'Ophtalmic')");
        $this->execute("DELETE FROM medication_set_rule WHERE usage_code = 'Management';");

	}

	/*
	// Use safeUp/safeDown to do migration with transaction
	public function safeUp()
	{
	}

	public function safeDown()
	{
	}
	*/
}