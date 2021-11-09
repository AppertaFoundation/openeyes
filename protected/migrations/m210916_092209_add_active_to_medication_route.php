<?php

class m210916_092209_add_active_to_medication_route extends OEMigration
{

	public function safeUp()
	{
        $this->addOEColumn('medication_route', 'is_active', 'TINYINT(1) DEFAULT 1', true);
	}

	public function safeDown()
	{
        $this->dropOEColumn('medication_route', 'is_active', true);
	}
}
