<?php

class m220903_062525_update_pgspsd_assignment_active_field extends OEMigration
{
    public function up()
    {
        $this->update('ophdrpgdpsd_assignment', ['active' => 0], "active IS NULL");
        $this->update('ophdrpgdpsd_assignment_version', ['active' => 0], "active IS NULL");
        $this->alterOEColumn('ophdrpgdpsd_assignment', 'active', 'tinyint(1) DEFAULT 1 NOT NULL', true);
    }

    public function down()
    {
        $this->alterOEColumn('ophdrpgdpsd_assignment', 'active', 'tinyint(1) DEFAULT 1', true);
    }
}
