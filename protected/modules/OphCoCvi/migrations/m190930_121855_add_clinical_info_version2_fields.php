<?php

class m190930_121855_add_clinical_info_version2_fields extends OEMigration
{
    public function up()
    {
        $this->addColumn('et_ophcocvi_clinicinfo', 'information_booklet', 'tinyint(1) unsigned');
        $this->addColumn('et_ophcocvi_clinicinfo_version', 'information_booklet', 'tinyint(1) unsigned');
        $this->addColumn('et_ophcocvi_clinicinfo', 'eclo', 'int(1) unsigned');
        $this->addColumn('et_ophcocvi_clinicinfo_version', 'eclo', 'int(1) unsigned');
        $this->addColumn('et_ophcocvi_clinicinfo', 'field_of_vision', 'int(1) unsigned');
        $this->addColumn('et_ophcocvi_clinicinfo_version', 'field_of_vision', 'int(1) unsigned');
        $this->addColumn('et_ophcocvi_clinicinfo', 'low_vision_service', 'int(1) unsigned');
        $this->addColumn('et_ophcocvi_clinicinfo_version', 'low_vision_service', 'int(1) unsigned');

        $this->addColumn('et_ophcocvi_clinicinfo', 'best_recorded_right_va', 'tinyint(1) unsigned');
        $this->addColumn('et_ophcocvi_clinicinfo_version', 'best_recorded_right_va', 'tinyint(1) unsigned');
        $this->addColumn('et_ophcocvi_clinicinfo', 'best_corrected_right_va_list', 'int(1) unsigned');
        $this->addColumn('et_ophcocvi_clinicinfo_version', 'best_corrected_right_va_list', 'int(1) unsigned');

        $this->addColumn('et_ophcocvi_clinicinfo', 'best_recorded_left_va', 'tinyint(1) unsigned');
        $this->addColumn('et_ophcocvi_clinicinfo_version', 'best_recorded_left_va', 'tinyint(1) unsigned');
        $this->addColumn('et_ophcocvi_clinicinfo', 'best_corrected_left_va_list', 'int(1) unsigned');
        $this->addColumn('et_ophcocvi_clinicinfo_version', 'best_corrected_left_va_list', 'int(1) unsigned');

        $this->addColumn('et_ophcocvi_clinicinfo', 'best_recorded_binocular_va', 'tinyint(1) unsigned');
        $this->addColumn('et_ophcocvi_clinicinfo_version', 'best_recorded_binocular_va', 'tinyint(1) unsigned');
        $this->addColumn('et_ophcocvi_clinicinfo', 'best_corrected_binocular_va_list', 'int(1) unsigned');
        $this->addColumn('et_ophcocvi_clinicinfo_version', 'best_corrected_binocular_va_list', 'int(1) unsigned');
    }

    public function down()
    {
        $this->dropColumn('et_ophcocvi_clinicinfo', 'information_booklet');
        $this->dropColumn('et_ophcocvi_clinicinfo_version', 'information_booklet');
        $this->dropColumn('et_ophcocvi_clinicinfo', 'eclo');
        $this->dropColumn('et_ophcocvi_clinicinfo_version', 'eclo');

        $this->dropColumn('et_ophcocvi_clinicinfo', 'best_recorded_right_va');
        $this->dropColumn('et_ophcocvi_clinicinfo_version', 'best_recorded_right_va');
        $this->dropColumn('et_ophcocvi_clinicinfo', 'best_corrected_right_va_list');
        $this->dropColumn('et_ophcocvi_clinicinfo_version', 'best_corrected_right_va_list');

        $this->dropColumn('et_ophcocvi_clinicinfo', 'best_recorded_left_va');
        $this->dropColumn('et_ophcocvi_clinicinfo_version', 'best_recorded_left_va');
        $this->dropColumn('et_ophcocvi_clinicinfo', 'best_corrected_left_va_list');
        $this->dropColumn('et_ophcocvi_clinicinfo_version', 'best_corrected_left_va_list');

        $this->dropColumn('et_ophcocvi_clinicinfo', 'best_recorded_binocular_va');
        $this->dropColumn('et_ophcocvi_clinicinfo_version', 'best_recorded_binocular_va');
        $this->dropColumn('et_ophcocvi_clinicinfo', 'best_corrected_binocular_va_list');
        $this->dropColumn('et_ophcocvi_clinicinfo_version', 'best_corrected_binocular_va_list');
    }
}
