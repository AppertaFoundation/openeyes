<?php

class m190212_084033_add_indices_to_medication extends CDbMigration
{
	public function up()
	{
		$this->createIndex("idx_medication_pterm", "medication", "preferred_term");
		$this->alterColumn('medication_search_index', 'alternative_term', 'string not null');
		$this->createIndex("idx_med_si_alternterm", "medication_search_index", "alternative_term");
	}

	public function down()
	{
		$this->dropIndex("idx_medication_pterm", "medication");
		$this->dropIndex("idx_med_si_alternterm", "medication_search_index");
		$this->alterColumn('medication_search_index', 'alternative_term', 'TEXT');
	}
}