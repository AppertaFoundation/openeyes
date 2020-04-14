<?php

class m191211_124602_set_biometry_target_refraction_default_to_null extends CDbMigration
{
    public function up()
    {
        $this->alterColumn('et_ophinbiometry_calculation', 'target_refraction_left', 'DECIMAL(6,2) NULL DEFAULT NULL');
        $this->alterColumn('et_ophinbiometry_calculation', 'target_refraction_right', 'DECIMAL(6,2) NULL DEFAULT NULL');
        $this->alterColumn('et_ophinbiometry_calculation_version', 'target_refraction_left', 'DECIMAL(6,2) NULL DEFAULT NULL');
        $this->alterColumn('et_ophinbiometry_calculation_version', 'target_refraction_right', 'DECIMAL(6,2) NULL DEFAULT NULL');
    }

    public function down()
    {
        $this->alterColumn('et_ophinbiometry_calculation', 'target_refraction_left', 'DECIMAL(6,2) NOT NULL DEFAULT 0.00');
        $this->alterColumn('et_ophinbiometry_calculation', 'target_refraction_right', 'DECIMAL(6,2) NOT NULL DEFAULT 0.00');
        $this->alterColumn('et_ophinbiometry_calculation_version', 'target_refraction_left', 'DECIMAL(6,2) NOT NULL DEFAULT 0.00');
        $this->alterColumn('et_ophinbiometry_calculation_version', 'target_refraction_right', 'DECIMAL(6,2) NOT NULL DEFAULT 0.00');
    }
}
