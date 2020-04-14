<?php

class m190705_124316_biometry_selection_add_manualy_overriden extends CDbMigration
{
    public function up()
    {
        foreach(['left', 'right'] as $side) {
            $this->addColumn('et_ophinbiometry_selection', 'manually_overriden_' . $side, 'tinyint(1) not null default 0');
            $this->addColumn('et_ophinbiometry_selection_version', 'manually_overriden_' . $side, 'tinyint(1) not null default 0');
        }
    }

    public function down()
    {
        foreach(['left', 'right'] as $side) {
            $this->dropColumn('et_ophinbiometry_selection', 'manually_overriden_' . $side);
            $this->dropColumn('et_ophinbiometry_selection_version', 'manually_overriden_' . $side);
        }
    }
}
