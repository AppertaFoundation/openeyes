<?php

class m140723_102608_data_changes extends CDbMigration
{
    public function up()
    {
        $this->alterColumn('et_ophinbiometry_lenstype', 'lens_id_left', 'int(10) unsigned not null');
        $this->alterColumn('et_ophinbiometry_lenstype', 'lens_id_right', 'int(10) unsigned not null');
    }

    public function down()
    {
        $this->alterColumn('et_ophinbiometry_lenstype', 'lens_id_left', 'int(10) unsigned not null default 1');
        $this->alterColumn('et_ophinbiometry_lenstype', 'lens_id_right', 'int(10) unsigned not null default 1');
    }
}
