<?php

class m210824_051606_make_laser_eyedraw_nullable extends OEMigration
{
    public function up()
    {
        $this->alterOEColumn('et_ophtrlaser_fundus', 'right_eyedraw', 'TEXT NULL', true);
        $this->alterOEColumn('et_ophtrlaser_fundus', 'left_eyedraw', 'TEXT NULL', true);
        $this->alterOEColumn('et_ophtrlaser_posteriorpo', 'right_eyedraw', 'TEXT NULL', true);
        $this->alterOEColumn('et_ophtrlaser_posteriorpo', 'left_eyedraw', 'TEXT NULL', true);
    }

    public function down()
    {
        echo "m210824_051606_make_laser_eyedraw_nullable is not revertable";
        return false;
    }
}
