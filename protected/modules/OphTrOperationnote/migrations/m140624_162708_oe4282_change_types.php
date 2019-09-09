<?php

/**

 */
class m140624_162708_oe4282_change_types extends OEMigration
{
    // This is a hash-of-list of table to affected columns which are
    // currently VARCHAR(4096) and should be TEXT. It's mainly here so
    // down() can revert the change.
    private static $CHANGES = array(
        'et_ophtroperationnote_buckle' => array('eyedraw'),
        'et_ophtroperationnote_buckle_version' => array('eyedraw'),
        # these two tables already have an eyedraw TEXT, but there's also an eyedraw2 VARCHAR(4096)!
        'et_ophtroperationnote_cataract' => array('eyedraw2'),
        'et_ophtroperationnote_cataract_version' => array('eyedraw2'),
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
