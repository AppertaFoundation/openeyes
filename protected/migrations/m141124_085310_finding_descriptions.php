<?php

class m141124_085310_finding_descriptions extends CDbMigration
{
	public function up()
	{
		$this->addColumn('finding','requires_description','tinyint(1) unsigned not null');
		$this->addColumn('finding_version','requires_description','tinyint(1) unsigned not null');
	}

	public function down()
	{
		$this->dropColumn('finding','requires_description');
		$this->dropColumn('finding_version','requires_description');
	}
}
