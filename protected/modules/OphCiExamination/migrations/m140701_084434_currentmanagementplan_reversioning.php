<?php

class m140701_084434_currentmanagementplan_reversioning extends OEMigration
{
    public function up()
    {
        $this->dropTable('et_ophciexamination_currentmanagementplan_version');
        $this->versionExistingTable('et_ophciexamination_currentmanagementplan');
    }

    public function down()
    {
        echo 'Down method not supported';
    }
}
