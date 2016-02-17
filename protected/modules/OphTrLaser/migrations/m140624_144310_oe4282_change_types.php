<?php

/**
   This patch fixes OE-4282: "Adding over 45 items to a doodle causes
   it to disappear upon saving". The bug is caused by the data being
   serialised into a VARCHAR(4096) column, and ~45 items pushes it
   beyond the 4096-character boundary. The fix is to widen the
   affected columns. TEXT (with its implicit 65,535 character limit)
   has been chosen over MEDIUMTEXT for symmetry with similar fixes for
   this class of problem elsewhere. This effectively increases the
   limit to ~400 items.

   Note that the OphTrOperationnote repository has a corresponding
   migration called m140624_162708_oe4282_change_types.php.

*/

class m140624_144310_oe4282_change_types extends OEMigration {

    // This is a hash-of-list of table to affected columns which are
    // currently VARCHAR(4096) and should be TEXT. It's mainly here so
    // down() can revert the change.
    static private $CHANGES = array(
        "et_ophtrlaser_anteriorseg"         => array("left_eyedraw", "right_eyedraw"),
        "et_ophtrlaser_anteriorseg_version" => array("left_eyedraw", "right_eyedraw"),
        "et_ophtrlaser_fundus"              => array("left_eyedraw", "right_eyedraw"),
        "et_ophtrlaser_fundus_version"      => array("left_eyedraw", "right_eyedraw"),
        "et_ophtrlaser_posteriorpo"         => array("left_eyedraw", "right_eyedraw"),
        "et_ophtrlaser_posteriorpo_version" => array("left_eyedraw", "right_eyedraw"),
    );

    private function changeColumns($newType) {
        foreach (self::$CHANGES as $table => $columns) {
            foreach($columns as $column) {
                $this->alterColumn($table, $column, $newType);
            }
        }
    }

    public function up() {
        $this->changeColumns("TEXT NOT NULL");
    }

    public function down() {
        $this->changeColumns("VARCHAR(4096) NOT NULL");
    }

}