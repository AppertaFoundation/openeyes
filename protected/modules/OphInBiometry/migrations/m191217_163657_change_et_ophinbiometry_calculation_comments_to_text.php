<?php

class m191217_163657_change_et_ophinbiometry_calculation_comments_to_text extends CDbMigration
{
    public function up()
    {
        $this->alterColumn('et_ophinbiometry_calculation', 'comments', 'text');
        $this->alterColumn('et_ophinbiometry_calculation_version', 'comments', 'text');
    }

    public function down()
    {
        $this->alterColumn('et_ophinbiometry_calculation', 'comments', 'varchar(1000)');
        $this->alterColumn('et_ophinbiometry_calculation_version', 'comments', 'varchar(1000)');
    }
}
