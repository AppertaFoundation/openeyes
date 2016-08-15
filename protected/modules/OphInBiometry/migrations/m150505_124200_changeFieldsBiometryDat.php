<?php

class m150505_124200_changeFieldsBiometryDat extends CDbMigration
{
    public function up()
    {
        $this->addColumn('et_ophinbiometry_lenstype', 'k1_left', 'decimal(6,2) not null default 0');
        $this->addColumn('et_ophinbiometry_lenstype', 'k1_right', 'decimal(6,2) not null default 0');
        $this->addColumn('et_ophinbiometry_lenstype', 'k2_left', 'decimal(6,2) not null default 0');
        $this->addColumn('et_ophinbiometry_lenstype', 'k2_right', 'decimal(6,2) not null default 0');
        $this->addColumn('et_ophinbiometry_lenstype', 'axis_k1_left', 'decimal(5,1) not null default 0');
        $this->addColumn('et_ophinbiometry_lenstype', 'axis_k1_right', 'decimal(5,1) not null default 0');
        $this->addColumn('et_ophinbiometry_lenstype', 'axial_length_left', 'decimal(6,2) not null default 0');
        $this->addColumn('et_ophinbiometry_lenstype', 'axial_length_right', 'decimal(6,2) not null default 0');
        $this->addColumn('et_ophinbiometry_lenstype', 'snr_left', 'int(10) not null default 0');
        $this->addColumn('et_ophinbiometry_lenstype', 'snr_right', 'int(10) not null default 0');
        $this->addColumn('et_ophinbiometry_lenstype_version', 'k1_left', 'decimal(6,2) not null default 0');
        $this->addColumn('et_ophinbiometry_lenstype_version', 'k1_right', 'decimal(6,2) not null default 0');
        $this->addColumn('et_ophinbiometry_lenstype_version', 'k2_left', 'decimal(6,2) not null default 0');
        $this->addColumn('et_ophinbiometry_lenstype_version', 'k2_right', 'decimal(6,2) not null default 0');
        $this->addColumn('et_ophinbiometry_lenstype_version', 'axis_k1_left', 'decimal(5,1) not null default 0');
        $this->addColumn('et_ophinbiometry_lenstype_version', 'axis_k1_right', 'decimal(5,1) not null default 0');
        $this->addColumn('et_ophinbiometry_lenstype_version', 'axial_length_left', 'decimal(6,2) not null default 0');
        $this->addColumn('et_ophinbiometry_lenstype_version', 'axial_length_right', 'decimal(6,2) not null default 0');
        $this->addColumn('et_ophinbiometry_lenstype_version', 'snr_left', 'int(10) not null default 0');
        $this->addColumn('et_ophinbiometry_lenstype_version', 'snr_right', 'int(10) not null default 0');
        $this->alterColumn('ophinbiometry_lenstype_lens', 'acon', 'decimal(8,4) not null default 0');
        $this->alterColumn('ophinbiometry_lenstype_lens_version', 'acon', 'decimal(8,4) not null default 0');
        $this->alterColumn('et_ophinbiometry_selection', 'iol_power_left', 'decimal(6,2) not null default 0');
        $this->alterColumn('et_ophinbiometry_selection', 'iol_power_right', 'decimal(6,2) not null default 0');
        $this->alterColumn('et_ophinbiometry_selection', 'predicted_refraction_left', 'decimal(6,2) not null default 0');
        $this->alterColumn('et_ophinbiometry_selection', 'predicted_refraction_right', 'decimal(6,2) not null default 0');
        $this->alterColumn('et_ophinbiometry_selection_version', 'iol_power_left', 'decimal(6,2) not null default 0');
        $this->alterColumn('et_ophinbiometry_selection_version', 'iol_power_right', 'decimal(6,2) not null default 0');
        $this->alterColumn('et_ophinbiometry_selection_version', 'predicted_refraction_left', 'decimal(6,2) not null default 0');
        $this->alterColumn('et_ophinbiometry_selection_version', 'predicted_refraction_right', 'decimal(6,2) not null default 0');
        $this->alterColumn('et_ophinbiometry_calculation', 'target_refraction_left', 'decimal(6,2) not null default 0');
        $this->alterColumn('et_ophinbiometry_calculation', 'target_refraction_right', 'decimal(6,2) not null default 0');
        $this->alterColumn('et_ophinbiometry_calculation_version', 'target_refraction_left', 'decimal(6,2) not null default 0');
        $this->alterColumn('et_ophinbiometry_calculation_version', 'target_refraction_right', 'decimal(6,2) not null default 0');

        $this->update('element_type', array('name' => '[-Calculation-]'), "class_name = 'Element_OphInBiometry_Calculation'");
    }

    public function down()
    {
        $this->dropColumn('et_ophinbiometry_lenstype', 'k1_left');
        $this->dropColumn('et_ophinbiometry_lenstype', 'k1_right');
        $this->dropColumn('et_ophinbiometry_lenstype', 'k2_left');
        $this->dropColumn('et_ophinbiometry_lenstype', 'k2_right');
        $this->dropColumn('et_ophinbiometry_lenstype', 'axis_k1_left');
        $this->dropColumn('et_ophinbiometry_lenstype', 'axis_k1_right');
        $this->dropColumn('et_ophinbiometry_lenstype', 'axial_length_left');
        $this->dropColumn('et_ophinbiometry_lenstype', 'axial_length_right');
        $this->dropColumn('et_ophinbiometry_lenstype', 'snr_left');
        $this->dropColumn('et_ophinbiometry_lenstype', 'snr_right');
        $this->dropColumn('et_ophinbiometry_lenstype_version', 'k1_left');
        $this->dropColumn('et_ophinbiometry_lenstype_version', 'k1_right');
        $this->dropColumn('et_ophinbiometry_lenstype_version', 'k2_left');
        $this->dropColumn('et_ophinbiometry_lenstype_version', 'k2_right');
        $this->dropColumn('et_ophinbiometry_lenstype_version', 'axis_k1_left');
        $this->dropColumn('et_ophinbiometry_lenstype_version', 'axis_k1_right');
        $this->dropColumn('et_ophinbiometry_lenstype_version', 'axial_length_left');
        $this->dropColumn('et_ophinbiometry_lenstype_version', 'axial_length_right');
        $this->dropColumn('et_ophinbiometry_lenstype_version', 'snr_left');
        $this->dropColumn('et_ophinbiometry_lenstype_version', 'snr_right');
        $this->alterColumn('ophinbiometry_lenstype_lens', 'acon', 'float not null default 0');
        $this->alterColumn('ophinbiometry_lenstype_lens_version', 'acon', 'float not null default 0');
        $this->alterColumn('et_ophinbiometry_biometrydat', 'axis_k1_left', 'int(10) not null default 134');
        $this->alterColumn('et_ophinbiometry_biometrydat', 'axis_k1_right', 'int(10) not null default 134');
        $this->renameColumn('et_ophinbiometry_biometrydat', 'k1_left', 'r1_left');
        $this->renameColumn('et_ophinbiometry_biometrydat', 'k1_right', 'r1_right');
        $this->renameColumn('et_ophinbiometry_biometrydat', 'k2_left', 'r2_left');
        $this->renameColumn('et_ophinbiometry_biometrydat', 'k2_right', 'r2_right');
        $this->renameColumn('et_ophinbiometry_biometrydat', 'axis_k1_left', 'r1_axis_left');
        $this->renameColumn('et_ophinbiometry_biometrydat', 'axis_k1_right', 'r1_axis_right');
        $this->alterColumn('et_ophinbiometry_biometrydat_version', 'axis_k1_left', 'int(10) not null default 134');
        $this->alterColumn('et_ophinbiometry_biometrydat_version', 'axis_k1_right', 'int(10) not null default 134');
        $this->renameColumn('et_ophinbiometry_biometrydat_version', 'k1_left', 'r1_left');
        $this->renameColumn('et_ophinbiometry_biometrydat_version', 'k1_right', 'r1_right');
        $this->renameColumn('et_ophinbiometry_biometrydat_version', 'k2_left', 'r2_left');
        $this->renameColumn('et_ophinbiometry_biometrydat_version', 'k2_right', 'r2_right');
        $this->renameColumn('et_ophinbiometry_biometrydat_version', 'axis_k1_left', 'r1_axis_left');
        $this->renameColumn('et_ophinbiometry_biometrydat_version', 'axis_k1_right', 'r1_axis_right');
    }

    /*
    // Use safeUp/safeDown to do migration with transaction
    public function safeUp()
    {
    }

    public function safeDown()
    {
    }
    */
}
