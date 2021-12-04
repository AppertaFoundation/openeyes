<?php

class m210820_012921_add_for_deleted_column extends OEMigration
{
    public function up()
    {
        $this->addOEColumn('ophdrpgdpsd_assignment', 'active', 'tinyint(1) DEFAULT 1', true);
    }

    public function down()
    {
        $this->dropOEColumn('ophdrpgdpsd_assignment', 'active', true);
    }
}
