<?php

class m221124_234946_ethnic_group_soft_deletion extends OEMigration
{
	// Use safeUp/safeDown to do migration with transaction
	public function safeUp()
	{
		$this->addOEColumn('ethnic_group', 'deleted', 'TINYINT(1) UNSIGNED DEFAULT 0 NOT NULL', true);
	}

	public function safeDown()
	{
		$this->dropOEColumn('ethnic_group', 'deleted', true);
	}
}
