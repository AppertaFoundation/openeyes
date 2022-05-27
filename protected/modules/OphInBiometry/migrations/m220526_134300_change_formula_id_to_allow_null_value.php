<?php

class m220526_134300_change_formula_id_to_allow_null_value extends OEMigration
{
    public function up()
    {
        $this->dbConnection->createCommand("alter table et_ophinbiometry_selection modify column `formula_id_left` int(10) unsigned NULL, modify column `formula_id_right` int(10) unsigned NULL;")->query();
        $this->dbConnection->createCommand("alter table et_ophinbiometry_selection_version modify column `formula_id_left` int(10) unsigned NULL, modify column `formula_id_right` int(10) unsigned NULL;")->query();
    }

    public function down()
    {
        $this->dbConnection->createCommand("alter table et_ophinbiometry_selection modify column `formula_id_left` int(10) unsigned NOT NULL default 0, modify column `formula_id_right` int(10) unsigned NOT NULL default 0;")->query();
        $this->dbConnection->createCommand("alter table et_ophinbiometry_selection_version modify column `formula_id_left` int(10) unsigned NOT NULL default 0, modify column `formula_id_right` int(10) unsigned NOT NULL default 0;")->query();
    }
}
