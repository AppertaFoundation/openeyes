<?php

class m191029_182003_add_lvc_lvc_mode_fields extends OEMigration
{
    public function up()
    {
        $this->addColumn('et_ophinbiometry_measurement', 'lvc_left', 'varchar(30)');
        $this->addColumn('et_ophinbiometry_measurement', 'lvc_mode_left', 'varchar(30)');
        $this->addColumn('et_ophinbiometry_measurement', 'lvc_right', 'varchar(30)');
        $this->addColumn('et_ophinbiometry_measurement', 'lvc_mode_right', 'varchar(30)');

        $this->addColumn('et_ophinbiometry_measurement_version', 'lvc_left', 'varchar(30)');
        $this->addColumn('et_ophinbiometry_measurement_version', 'lvc_mode_left', 'varchar(30)');
        $this->addColumn('et_ophinbiometry_measurement_version', 'lvc_right', 'varchar(30)');
        $this->addColumn('et_ophinbiometry_measurement_version', 'lvc_mode_right', 'varchar(30)');
    }

    public function down()
    {
        $this->dropColumn('et_ophinbiometry_measurement', 'lvc_left');
        $this->dropColumn('et_ophinbiometry_measurement', 'lvc_mode_left');
        $this->dropColumn('et_ophinbiometry_measurement', 'lvc_right');
        $this->dropColumn('et_ophinbiometry_measurement', 'lvc_mode_right');

        $this->dropColumn('et_ophinbiometry_measurement_version', 'lvc_left');
        $this->dropColumn('et_ophinbiometry_measurement_version', 'lvc_mode_left');
        $this->dropColumn('et_ophinbiometry_measurement_version', 'lvc_right');
        $this->dropColumn('et_ophinbiometry_measurement_version', 'lvc_mode_right');
    }
}
