<?php

class m140821_153421_additional_fields extends CDbMigration
{
    public function up()
    {
        $this->addColumn('et_ophinbiometry_biometrydat', 'snr_left', 'decimal(4,1) not null default 193');
        $this->addColumn('et_ophinbiometry_biometrydat_version', 'snr_left', 'decimal(4,1) not null default 193');

        $this->addColumn('et_ophinbiometry_biometrydat', 'snr_right', 'decimal(4,1) not null default 193');
        $this->addColumn('et_ophinbiometry_biometrydat_version', 'snr_right', 'decimal(4,1) not null default 193');

        $this->alterColumn('et_ophinbiometry_biometrydat', 'r1_axis_left', 'int(10) unsigned null default 134');
        $this->alterColumn('et_ophinbiometry_biometrydat_version', 'r1_axis_left', 'int(10) unsigned null default 134');
        $this->alterColumn('et_ophinbiometry_biometrydat', 'r1_axis_right', 'int(10) unsigned null default 134');
        $this->alterColumn('et_ophinbiometry_biometrydat_version', 'r1_axis_right', 'int(10) unsigned null default 134');

        $this->alterColumn('et_ophinbiometry_biometrydat', 'r2_axis_left', 'int(10) unsigned null default 44');
        $this->alterColumn('et_ophinbiometry_biometrydat_version', 'r2_axis_left', 'int(10) unsigned null default 44');
        $this->alterColumn('et_ophinbiometry_biometrydat', 'r2_axis_right', 'int(10) unsigned null default 44');
        $this->alterColumn('et_ophinbiometry_biometrydat_version', 'r2_axis_right', 'int(10) unsigned null default 44');
    }

    public function down()
    {
        $this->alterColumn('et_ophinbiometry_biometrydat', 'r1_axis_left', 'int(10) unsigned null');
        $this->alterColumn('et_ophinbiometry_biometrydat_version', 'r1_axis_left', 'int(10) unsigned null');
        $this->alterColumn('et_ophinbiometry_biometrydat', 'r1_axis_right', 'int(10) unsigned null');
        $this->alterColumn('et_ophinbiometry_biometrydat_version', 'r1_axis_right', 'int(10) unsigned null');

        $this->alterColumn('et_ophinbiometry_biometrydat', 'r2_axis_left', 'int(10) unsigned null');
        $this->alterColumn('et_ophinbiometry_biometrydat_version', 'r2_axis_left', 'int(10) unsigned null');
        $this->alterColumn('et_ophinbiometry_biometrydat', 'r2_axis_right', 'int(10) unsigned null');
        $this->alterColumn('et_ophinbiometry_biometrydat_version', 'r2_axis_right', 'int(10) unsigned null');

        $this->dropColumn('et_ophinbiometry_biometrydat', 'snr_left');
        $this->dropColumn('et_ophinbiometry_biometrydat_version', 'snr_left');

        $this->dropColumn('et_ophinbiometry_biometrydat', 'snr_right');
        $this->dropColumn('et_ophinbiometry_biometrydat_version', 'snr_right');
    }
}
