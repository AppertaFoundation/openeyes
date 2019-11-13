<?php

/**

 */
class m140624_144310_oe4282_change_types extends OEMigration
{
    // This is a hash-of-list of table to affected columns which are
    // currently VARCHAR(4096) and should be TEXT. It's mainly here so
    // down() can revert the change.
    private static $CHANGES = array(
        'et_ophtrlaser_anteriorseg' => array('left_eyedraw', 'right_eyedraw'),
        'et_ophtrlaser_anteriorseg_version' => array('left_eyedraw', 'right_eyedraw'),
        'et_ophtrlaser_fundus' => array('left_eyedraw', 'right_eyedraw'),
        'et_ophtrlaser_fundus_version' => array('left_eyedraw', 'right_eyedraw'),
        'et_ophtrlaser_posteriorpo' => array('left_eyedraw', 'right_eyedraw'),
        'et_ophtrlaser_posteriorpo_version' => array('left_eyedraw', 'right_eyedraw'),
    );

    private function changeColumns($newType)
    {
        foreach (self::$CHANGES as $table => $columns) {
            foreach ($columns as $column) {
                $this->alterColumn($table, $column, $newType);
            }
        }
    }

    public function up()
    {
        $this->changeColumns('TEXT NOT NULL');
    }

    public function down()
    {
        $this->changeColumns('VARCHAR(4096) NOT NULL');
    }
}
