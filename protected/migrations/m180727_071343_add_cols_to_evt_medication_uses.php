<?php

class m180727_071343_add_cols_to_evt_medication_uses extends CDbMigration
{
	public function up()
	{
	    $this->addColumn('event_medication_use', 'continue', 'BOOLEAN NOT NULL DEFAULT 0');
	    $this->addColumn('event_medication_use', 'prescribe', 'BOOLEAN NOT NULL DEFAULT 0');
        $this->addColumn('event_medication_use_version', 'continue', 'BOOLEAN NOT NULL DEFAULT 0');
        $this->addColumn('event_medication_use_version', 'prescribe', 'BOOLEAN NOT NULL DEFAULT 0');
	}

	public function down()
	{
		$this->dropColumn('event_medication_use', 'continue');
		$this->dropColumn('event_medication_use', 'prescribe');
        $this->dropColumn('event_medication_use_version', 'continue');
        $this->dropColumn('event_medication_use_version', 'prescribe');
	}
}
