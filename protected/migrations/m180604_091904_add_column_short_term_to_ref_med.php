<?php

class m180604_091904_add_column_short_term_to_ref_med extends CDbMigration
{
	public function up()
	{
	    $this->addColumn('ref_medication', 'short_term', 'string');
	    $this->addColumn('ref_medication_version', 'short_term', 'string');
	    $this->execute("UPDATE ref_medication SET short_term = preferred_term");
	}

	public function down()
	{
		$this->dropColumn('ref_medication', 'short_term');
		$this->dropColumn('ref_medication_version', 'short_term');
	}
}