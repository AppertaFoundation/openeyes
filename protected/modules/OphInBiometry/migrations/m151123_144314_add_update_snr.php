<?php

class m151123_144314_add_update_snr extends OEMigration
{
    public function up()
    {
        $this->alterColumn('et_ophinbiometry_measurement', 'snr_left', 'decimal(6,1) not null DEFAULT 0.0');
        $this->alterColumn('et_ophinbiometry_measurement', 'snr_right', 'decimal(6,1) not null DEFAULT 0.0');
        $this->alterColumn('et_ophinbiometry_measurement_version', 'snr_left', 'decimal(6,1) not null DEFAULT 0.0');
        $this->alterColumn('et_ophinbiometry_measurement_version', 'snr_right', 'decimal(6,1) not null DEFAULT 0.0');

        $this->addColumn('et_ophinbiometry_measurement', 'snr_min_left', 'decimal(6,1) not null DEFAULT 0.0');
        $this->addColumn('et_ophinbiometry_measurement_version', 'snr_min_left', 'decimal(6,1) not null DEFAULT 0.0');
        $this->addColumn('et_ophinbiometry_measurement', 'snr_min_right', 'decimal(6,1) not null DEFAULT 0.0');
        $this->addColumn('et_ophinbiometry_measurement_version', 'snr_min_right', 'decimal(6,1) not null DEFAULT 0.0');
    }

    public function down()
    {
        $this->alterColumn('et_ophinbiometry_measurement', 'snr_left', 'int(10) not null DEFAULT 0');
        $this->alterColumn('et_ophinbiometry_measurement', 'snr_right', 'int(10) not null DEFAULT 0');
        $this->alterColumn('et_ophinbiometry_measurement_version', 'snr_left', 'int(10) not null DEFAULT 0');
        $this->alterColumn('et_ophinbiometry_measurement_version', 'snr_right', 'int(10) not null DEFAULT 0');

        $this->dropColumn('et_ophinbiometry_measurement', 'snr_min_left');
        $this->dropColumn('et_ophinbiometry_measurement_version', 'snr_min_left');
        $this->dropColumn('et_ophinbiometry_measurement', 'snr_min_right');
        $this->dropColumn('et_ophinbiometry_measurement_version', 'snr_min_right');
    }
}
