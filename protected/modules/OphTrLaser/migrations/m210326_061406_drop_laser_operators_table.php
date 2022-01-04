<?php

class m210326_061406_drop_laser_operators_table extends OEMigration
{
    public function up()
    {
        $this->dropOETable('ophtrlaser_laser_operator', true);
    }

    public function down()
    {
        echo "m210326_061406_drop_laser_operators_table does not support migrate down";
        return false;
    }
}
