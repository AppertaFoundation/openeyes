<?php

class m160426_105008_add_deleted_flag_to_patient extends CDbMigration
{
    public function up()
    {
        $this->addColumn('patient', 'deleted', 'tinyint(1) unsigned not null default 0');
        $this->addColumn('patient_version', 'deleted', 'tinyint(1) unsigned not null default 0');
    }

    public function down()
    {
        $this->dropColumn('patient', 'deleted');
        $this->dropColumn('patient_version', 'deleted');
    }
}
