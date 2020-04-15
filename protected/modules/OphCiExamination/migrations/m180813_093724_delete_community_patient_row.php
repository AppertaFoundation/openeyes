<?php

class m180813_093724_delete_community_patient_row extends CDbMigration
{
    public function up()
    {
        $this->dropColumn('et_ophciexamination_clinicoutcome', 'community_patient');
        $this->dropColumn('et_ophciexamination_clinicoutcome_version', 'community_patient');
    }

    public function down()
    {
        $this->addColumn('et_ophciexamination_clinicoutcome', 'community_patient', 'tinyint(1) unsigned NOT NULL DEFAULT 0');
        $this->addColumn('et_ophciexamination_clinicoutcome_version', 'community_patient', 'tinyint(1) unsigned NOT NULL DEFAULT 0');
    }
}
