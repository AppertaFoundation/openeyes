<?php

class m191105_134501_update_clinicinfo_columns extends CDbMigration
{
    public function up()
    {
        $this->alterColumn("et_ophcocvi_clinicinfo", "eclo", "int(1) DEFAULT NULL");
        $this->alterColumn("et_ophcocvi_clinicinfo", "field_of_vision", "int(1) DEFAULT NULL");
        $this->alterColumn("et_ophcocvi_clinicinfo", "low_vision_service", "int(1) DEFAULT NULL");
        $this->alterColumn("et_ophcocvi_clinicinfo", "best_corrected_right_va_list", "int(1) DEFAULT NULL");
        $this->alterColumn("et_ophcocvi_clinicinfo", "best_recorded_left_va", "int(1) DEFAULT NULL");
        $this->alterColumn("et_ophcocvi_clinicinfo", "best_recorded_right_va", "int(1) DEFAULT NULL");
        $this->alterColumn("et_ophcocvi_clinicinfo", "best_recorded_binocular_va", "int(1) DEFAULT NULL");
    }

    public function down()
    {
        return true;
    }
}
