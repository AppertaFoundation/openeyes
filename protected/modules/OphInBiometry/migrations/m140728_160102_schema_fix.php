<?php

class m140728_160102_schema_fix extends CDbMigration
{
    public function up()
    {
        $this->alterColumn('et_ophinbiometry_lenstype', 'lens_id_left', 'int(10) unsigned null');
        $this->alterColumn('et_ophinbiometry_lenstype', 'lens_id_right', 'int(10) unsigned null');
        $this->alterColumn('et_ophinbiometry_lenstype_version', 'lens_id_left', 'int(10) unsigned null');
        $this->alterColumn('et_ophinbiometry_lenstype_version', 'lens_id_right', 'int(10) unsigned null');
    }

    public function down()
    {
        $this->alterColumn('et_ophinbiometry_lenstype', 'lens_id_left', 'int(10) unsigned not null');
        $this->alterColumn('et_ophinbiometry_lenstype', 'lens_id_right', 'int(10) unsigned not null');
        $this->alterColumn('et_ophinbiometry_lenstype_version', 'lens_id_left', 'int(10) unsigned not null');
        $this->alterColumn('et_ophinbiometry_lenstype_version', 'lens_id_right', 'int(10) unsigned not null');
    }
}
