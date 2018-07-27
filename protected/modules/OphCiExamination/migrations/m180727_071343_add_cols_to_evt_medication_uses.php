<?php

class m180727_071343_add_cols_to_evt_medication_uses extends CDbMigration
{
	public function up()
	{
	    $this->addColumn('event_medication_uses', 'continue', 'BOOLEAN NOT NULL DEFAULT 0');
	    $this->addColumn('event_medication_uses', 'prescribe', 'BOOLEAN NOT NULL DEFAULT 0');
	}

	public function down()
	{
		$this->dropColumn('event_medication_uses', 'continue');
		$this->dropColumn('event_medication_uses', 'prescribe');
	}
}