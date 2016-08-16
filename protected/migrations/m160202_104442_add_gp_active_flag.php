<?php

class m160202_104442_add_gp_active_flag extends CDbMigration
{
    public function up()
    {
        $this->addColumn('gp', 'is_active', 'TINYINT(1) DEFAULT 1 AFTER nat_id');
        $this->addColumn('gp_version', 'is_active', 'TINYINT(1) DEFAULT 1 AFTER nat_id');
    }

    public function down()
    {
        $this->dropColumn('gp', 'is_active');
        $this->dropColumn('gp_version', 'is_active');
    }
}
