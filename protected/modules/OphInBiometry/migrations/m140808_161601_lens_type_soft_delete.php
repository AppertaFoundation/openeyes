<?php

class m140808_161601_lens_type_soft_delete extends CDbMigration
{
    public function up()
    {
        $this->addColumn('ophinbiometry_lenstype_lens', 'active', 'tinyint(1) unsigned default 1');
        $this->addColumn('ophinbiometry_lenstype_lens_version', 'active', 'tinyint(1) unsigned default 1');
    }

    public function down()
    {
        $this->dropColumn('ophinbiometry_lenstype_lens', 'active');
        $this->dropColumn('ophinbiometry_lenstype_lens_version', 'active');
    }
}
