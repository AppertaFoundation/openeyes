<?php

class m191006_145719_add_fileds_cvi_patient_factors extends OEMigration
{
    public function up()
    {
        $this->addOEColumn('ophcocvi_clericinfo_patient_factor', 'comments_only', "tinyint(1) unsigned NOT NULL DEFAULT '0'", true);
        $this->addOEColumn('ophcocvi_clericinfo_patient_factor', 'yes_no_only', "tinyint(1) unsigned NOT NULL DEFAULT '0'", true);
        $this->addOEColumn('ophcocvi_clericinfo_patient_factor', 'event_type_version', "int(4) unsigned NOT NULL DEFAULT '0'", true);
    }

    public function down()
    {
        $this->dropOEColumn('ophcocvi_clericinfo_patient_factor', 'comments_only', true);
        $this->dropOEColumn('ophcocvi_clericinfo_patient_factor_version', 'comments_only', true);
        $this->dropOEColumn('ophcocvi_clericinfo_patient_factor', 'yes_no_only', true);
        $this->dropOEColumn('ophcocvi_clericinfo_patient_factor_version', 'yes_no_only', true);
        $this->dropOEColumn('ophcocvi_clericinfo_patient_factor', 'event_type_version', true);
        $this->dropOEColumn('ophcocvi_clericinfo_patient_factor_version', 'event_type_version', true);
    }
}
