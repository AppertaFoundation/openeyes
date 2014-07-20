<?php

class m140516_130427_version_table_discrepencies extends CDbMigration
{
	public function up()
	{
		$this->dropColumn('referral_version','clock_start');
	}

	public function down()
	{
		$this->addColumn('referral_version','clock_start','datetime null');
	}
}
