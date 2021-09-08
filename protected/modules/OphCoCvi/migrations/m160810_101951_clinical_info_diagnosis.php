<?php

class m160810_101951_clinical_info_diagnosis extends OEMigration
{
    public function up()
    {
        $this->addColumn('et_ophcocvi_clinicinfo_disorder_assignment', 'eye_id', 'int(10) unsigned NOT NULL');
        $this->addForeignKey('et_ophcocvi_clinicinfo_disorder_assignment_eye_fk',
            'et_ophcocvi_clinicinfo_disorder_assignment', 'eye_id',
            'eye', 'id');

        $this->addColumn('et_ophcocvi_clinicinfo_disorder_assignment', 'affected', 'tinyint(1) unsigned NOT NULL DEFAULT 0');
    }

    public function down()
    {
        $this->dropForeignKey('et_ophcocvi_clinicinfo_disorder_assignment_eye_fk', 'et_ophcocvi_clinicinfo_disorder_assignment');
        $this->dropColumn('et_ophcocvi_clinicinfo_disorder_assignment', 'eye_id');
        $this->dropColumn('et_ophcocvi_clinicinfo_disorder_assignment', 'affected');
    }
}
