<?php

class m201105_223728_Add_uuid_for_ammonite extends OEMigration
{
    public function up()
    {
        $this->createOETable('ammonite', array(
            'uuid'=>'VARCHAR(40)',
            'last_version_check_date'=>'datetime',
            'first_install_date'=>'datetime'
        ), true);
    }

    public function down()
    {
        $this->dropOETable('ammonite', true);
    }
}
