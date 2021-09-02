<?php

class m200417_104344_add_password_date_and_tries_to_user extends OEMigration
{
    public function up()
    {
        $date = date("Y-m-d H:i:s");
        $this->addOEColumn('user', 'password_last_changed_date', 'datetime DEFAULT "'.$date.'"', true);
        $this->addOEColumn('user', 'password_failed_tries', 'INT(10) DEFAULT 0', true);
        $this->addOEColumn('user', 'password_status', 'VARCHAR(10) DEFAULT "current"', true);
    }

    public function down()
    {
        $this->dropOEColumn('user', 'password_status', true);
        $this->dropOEColumn('user', 'password_last_changed_date', true);
        $this->dropOEColumn('user', 'password_failed_tries', true);
    }
}
