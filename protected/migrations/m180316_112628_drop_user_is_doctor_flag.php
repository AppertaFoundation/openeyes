<?php

class m180316_112628_drop_user_is_doctor_flag extends \OEMigration
{
    public function up()
    {
        $this->dropColumn('user', 'is_doctor');
        $this->dropColumn('user_version', 'is_doctor');
    }

    public function down()
    {
        $this->addColumn('user', 'is_doctor', 'tinyint(1) unsigned NOT NULL DEFAULT "0"');
        $this->addColumn('user_version', 'is_doctor', 'tinyint(1) unsigned NOT NULL DEFAULT "0"');

        $this->execute("UPDATE user SET is_doctor = 1 WHERE is_surgeon = 1;");
    }
}
