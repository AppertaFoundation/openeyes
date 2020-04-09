<?php

class m180510_093822_add_column_emu_comments extends CDbMigration
{
	public function up()
	{
	    $this->addColumn('event_medication_use', 'comments', 'TINYTEXT NULL');
	    $this->addColumn('event_medication_use_version', 'comments', 'TINYTEXT NULL');
	}

	public function down()
	{
		$this->dropColumn('event_medication_use', 'comments');
		$this->dropColumn('event_medication_use_version', 'comments');
	}
}
