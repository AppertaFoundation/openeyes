<?php

class m170118_145106_remove_is_tech extends CDbMigration
{
    public function up()
    {
        $this->dropColumn('user', 'is_technician');
        $this->dropColumn('user_version', 'is_technician');
    }

    public function down()
    {
        $this->addColumn('user', 'is_technician', 'tinyint(1) unsigned AFTER is_surgeon');
        $this->addColumn('user_version', 'is_technician', 'tinyint(1) unsigned AFTER is_surgeon');
    }
}
