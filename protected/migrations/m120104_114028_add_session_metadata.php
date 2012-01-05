<?php

class m120104_114028_add_session_metadata extends CDbMigration
{
	public function up()
	{
		$this->addColumn('sequence','consultant','tinyint(1) unsigned NOT NULL DEFAULT 1');
		$this->addColumn('sequence','paediatric','tinyint(1) unsigned NOT NULL DEFAULT 0');
		$this->addColumn('sequence','anaesthetist','tinyint(1) unsigned NOT NULL DEFAULT 0');
		$this->addColumn('session','consultant','tinyint(1) unsigned NOT NULL DEFAULT 1');
		$this->addColumn('session','paediatric','tinyint(1) unsigned NOT NULL DEFAULT 0');
		$this->addColumn('session','anaesthetist','tinyint(1) unsigned NOT NULL DEFAULT 0');
	}

	public function down()
	{
		$this->dropColumn('sequence','consultant');
		$this->dropColumn('sequence','paediatric');
		$this->dropColumn('sequence','anaesthetist');
		$this->dropColumn('session','consultant');
		$this->dropColumn('session','paediatric');
		$this->dropColumn('session','anaesthetist');
	}

}