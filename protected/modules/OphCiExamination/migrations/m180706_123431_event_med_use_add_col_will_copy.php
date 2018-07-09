<?php

class m180706_123431_event_med_use_add_col_will_copy extends CDbMigration
{
	public function up()
	{
	    $this->addColumn('ref_medication', 'will_copy', 'BOOLEAN NOT null DEFAULT 0');
	    $this->execute("UPDATE ref_medication SET will_copy = 1 WHERE vmp_term IS NOT NULL AND vmp_term != ''");
	}

	public function down()
	{
		$this->dropColumn('ref_medication', 'will_copy');
	}
}