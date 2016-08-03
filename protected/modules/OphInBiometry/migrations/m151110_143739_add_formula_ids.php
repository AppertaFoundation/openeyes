<?php

class m151110_143739_add_formula_ids extends OEMigration
{
    public function up()
    {
        //Add Column
        $this->addColumn('et_ophinbiometry_selection', 'formula_id_left', 'int(10) unsigned NOT NULL DEFAULT 0');
        $this->addColumn('et_ophinbiometry_selection', 'formula_id_right', 'int(10) unsigned NOT NULL DEFAULT 0');
        $this->addColumn('et_ophinbiometry_selection_version', 'formula_id_left', 'int(10) unsigned NOT NULL DEFAULT 0');
        $this->addColumn('et_ophinbiometry_selection_version', 'formula_id_right', 'int(10) unsigned NOT NULL DEFAULT 0');
    }

    public function down()
    {
        //Drop Column
        $this->dropColumn('et_ophinbiometry_selection', 'formula_id_left');
        $this->dropColumn('et_ophinbiometry_selection', 'formula_id_right');
        $this->dropColumn('et_ophinbiometry_selection_version', 'formula_id_left');
        $this->dropColumn('et_ophinbiometry_selection_version', 'formula_id_right');
    }
}
