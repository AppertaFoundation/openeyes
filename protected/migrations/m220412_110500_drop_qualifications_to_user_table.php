<?php

class m220412_110500_drop_qualifications_to_user_table extends OEMigration
{
	public function up()
	{
        $this->dropOEColumn('user', 'qualifications', true);
	}

	public function down()
	{
        $this->addOEColumn('user', 'qualifications', 'varchar(200) DEFAULT NULL', true);
	}
}
