<?php

class m161219_195513_add_user_is_technician_field extends CDbMigration
{
    public function up()
    {
        $this->addColumn('user', 'is_technician', 'tinyint(1) unsigned AFTER is_surgeon');
        $this->addColumn('user_version', 'is_technician', 'tinyint(1) unsigned AFTER is_surgeon');
    }

    public function down()
    {
        $this->dropColumn('user', 'is_technician');
        $this->dropColumn('user_version', 'is_technician');
    }
}
