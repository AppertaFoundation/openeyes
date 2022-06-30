<?php

class m220627_052230_remove_redundant_medication_set_auto_rule_medication_rows extends OEMigration
{
	// Use safeUp/safeDown to do migration with transaction
	public function safeUp()
	{
		// Find the medication set ids used in the medication_set_auto_rule_medication table that are associated with redundant rows
		$medication_set_ids_with_redundancy = $this->dbConnection->createCommand(
			'SELECT DISTINCT medication_set_id FROM ' .
			'(SELECT COUNT(id) AS count, medication_set_id FROM medication_set_auto_rule_medication GROUP BY medication_set_id, medication_id) AS t WHERE t.count > 1;'
		)->queryColumn();

		foreach ($medication_set_ids_with_redundancy as $medication_set_id_with_redundancy) {
			// For the medication_set_id and for each medication_id, find one row to keep for that pair and remove all the other redundant copies
			$this->delete(
				'medication_set_auto_rule_medication',
				'medication_set_id = :medication_set_id AND id NOT IN (SELECT MIN(id) FROM medication_set_auto_rule_medication WHERE medication_set_id = :medication_set_id GROUP BY medication_id)',
				[':medication_set_id' => $medication_set_id_with_redundancy]
			);
		}
	}

	public function safeDown()
	{
		echo "m220627_052230_remove_redundant_medication_set_auto_rule_medication_rows does not support migration down.\n";
		return false;
	}
}
