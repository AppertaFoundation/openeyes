<?php

class m200706_041346_add_password_softlocked_time_to_user extends OEMigration
{
	public function up()
	{        
        $date = date("Y-m-d H:i:s");
        $this->addOEColumn('user', 'password_softlocked_until', 'datetime DEFAULT "'.$date.'"', true);
	}

	public function down()
	{
        $this->dropOEColumn('user', 'password_softlocked_until', true);
	}
}