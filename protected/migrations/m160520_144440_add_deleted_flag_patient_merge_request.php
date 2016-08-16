<?php

class m160520_144440_add_deleted_flag_patient_merge_request extends CDbMigration
{
    public function up()
    {
        $this->addColumn('patient_merge_request', 'deleted', 'tinyint(1) unsigned not null default 0');
        $this->addColumn('patient_merge_request_version', 'deleted', 'tinyint(1) unsigned not null default 0');
    }

    public function down()
    {
        $this->dropColumn('patient_merge_request', 'deleted');
        $this->dropColumn('patient_merge_request_version', 'deleted');
    }
}
