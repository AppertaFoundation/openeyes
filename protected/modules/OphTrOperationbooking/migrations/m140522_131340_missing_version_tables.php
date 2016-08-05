<?php

class m140522_131340_missing_version_tables extends OEMigration
{
    public function up()
    {
        $this->versionExistingTable('ophtroperationbooking_scheduleope_patientunavail');
        $this->versionExistingTable('ophtroperationbooking_scheduleope_patientunavailreason');
    }

    public function down()
    {
        $this->dropTable('ophtroperationbooking_scheduleope_patientunavail_version');
        $this->dropTable('ophtroperationbooking_scheduleope_patientunavailreason_version');
    }
}
