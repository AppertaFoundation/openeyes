<?php

class m190315_072355_allergies_to_sets extends OEMigration
{
	public function up()
	{
		$transaction=$this->getDbConnection()->beginTransaction();
		try {
			// Make a link to sets in allergies table
			$this->addColumn("ophciexamination_allergy", "medication_set_id", "INT(11) null");
			$this->addColumn("ophciexamination_allergy_version", "medication_set_id", "INT(11) null");
			$this->addForeignKey("fk_allergy_to_set", "ophciexamination_allergy", "medication_set_id", "medication_set", "id");

			// Create an auto-set for each allergy
			$this->execute("INSERT INTO medication_set (`name`, `automatic`) SELECT CONCAT('Allergy_', ophciexamination_allergy.`name`), 1 FROM ophciexamination_allergy");

			// Link auto-sets with allergies
			$this->execute("UPDATE ophciexamination_allergy SET medication_set_id = (SELECT id FROM medication_set WHERE `name` = CONCAT('Allergy_', ophciexamination_allergy.`name`))");

			// Fill auto-set rules with existing medications
			$this->execute("INSERT INTO medication_set_auto_rule_medication (medication_set_id, medication_id, include_children)
								SELECT 
									allergy.medication_set_id,
									maa.medication_id,
									1
								FROM 
									medication_allergy_assignment maa
								LEFT JOIN ophciexamination_allergy allergy ON allergy.id = maa.allergy_id
								");

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
		$this->execute("UPDATE ophciexamination_allergy SET medication_set_id = NULL ");
		$this->execute("DELETE medication_set_item FROM medication_set_item, medication_set WHERE medication_set_item.medication_set_id = medication_set.id AND  medication_set.`name` LIKE 'Allergy\_%'");
		$this->execute("DELETE tbl FROM medication_set_auto_rule_medication AS tbl, medication_set WHERE tbl.medication_set_id = medication_set.id AND  medication_set.`name` LIKE 'Allergy\_%'");
		$this->execute("DELETE tbl FROM medication_set_auto_rule_attribute AS tbl, medication_set WHERE tbl.medication_set_id = medication_set.id AND  medication_set.`name` LIKE 'Allergy\_%'");
		$this->execute("DELETE tbl FROM medication_set_auto_rule_set_membership AS tbl, medication_set WHERE tbl.target_medication_set_id = medication_set.id AND  medication_set.`name` LIKE 'Allergy\_%'");
		$this->execute("DELETE FROM medication_set WHERE `name` LIKE 'Allergy\_%'");

		$this->dropForeignKey("fk_allergy_to_set", "ophciexamination_allergy");
		$this->dropColumn("ophciexamination_allergy", "medication_set_id");
		$this->dropColumn("ophciexamination_allergy_version", "medication_set_id");
	}
}