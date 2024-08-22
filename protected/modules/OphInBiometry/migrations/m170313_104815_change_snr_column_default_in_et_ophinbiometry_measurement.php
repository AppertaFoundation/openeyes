<?php

class m170313_104815_change_snr_column_default_in_et_ophinbiometry_measurement extends CDbMigration
{
    public function up()
    {
        $this->alterColumn('et_ophinbiometry_measurement', 'snr_left', 'DECIMAL(6,1) NULL');
        $this->alterColumn('et_ophinbiometry_measurement', 'snr_right', 'DECIMAL(6,1) NULL');

        $this->alterColumn('et_ophinbiometry_measurement_version', 'snr_left', 'DECIMAL(6,1) NULL');
        $this->alterColumn('et_ophinbiometry_measurement_version', 'snr_right', 'DECIMAL(6,1) NULL');
    }

    public function down()
    {
        $this->alterColumn("et_ophinbiometry_measurement', 'snr_left', 'DECIMAL(6,1) NOT NULL DEFAULT '0.0'");
        $this->alterColumn("et_ophinbiometry_measurement', 'snr_right', 'DECIMAL(6,1) NOT NULL DEFAULT '0.0'");

        $this->alterColumn("et_ophinbiometry_measurement_version', 'snr_left', 'DECIMAL(6,1) NOT NULL DEFAULT '0.0'");
        $this->alterColumn("et_ophinbiometry_measurement_version', 'snr_right', 'DECIMAL(6,1) NOT NULL DEFAULT '0.0'");
    }
}
