<?php

class m140522_131014_missing_version_table extends OEMigration
{
    public function up()
    {
        $this->versionExistingTable('ophtrlaser_laser_operator');
    }

    public function down()
    {
        $this->dropTable('ophtrlaser_laser_operator_version');
    }
}
