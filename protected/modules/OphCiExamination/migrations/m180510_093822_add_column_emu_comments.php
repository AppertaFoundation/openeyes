<?php

class m180510_093822_add_column_emu_comments extends CDbMigration
{
	public function up()
	{
	    $this->addColumn('event_medication_uses', 'comments', 'TINYTEXT NULL');
	    $this->addColumn('event_medication_uses_version', 'comments', 'TINYTEXT NULL');
	}

	public function down()
	{
		$this->dropColumn('event_medication_uses', 'comments');
		$this->dropColumn('event_medication_uses_version', 'comments');
	}
}