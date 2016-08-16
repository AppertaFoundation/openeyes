<?php

class m160202_130039_add_practice_active_flag extends CDbMigration
{
    public function up()
    {
        $this->addColumn('practice', 'is_active', 'TINYINT(1) DEFAULT 1 AFTER phone');
        $this->addColumn('practice_version', 'is_active', 'TINYINT(1) DEFAULT 1 AFTER phone');
    }

    public function down()
    {
        $this->dropColumn('practice', 'is_active');
        $this->dropColumn('practice_version', 'is_active');
    }
}
