<?php

class m141013_133518_remove_unneeded_column extends CDbMigration
{
    public function up()
    {
        $this->dropColumn('patientticketing_queueset_filter', 'patient_list');
        $this->dropColumn('patientticketing_queueset_filter_version', 'patient_list');
    }

    public function down()
    {
        $this->addColumn('patientticketing_queueset_filter', 'patient_list', 'tinyint(1) unsigned not null default 1');
        $this->addColumn('patientticketing_queueset_filter_version', 'patient_list', 'tinyint(1) unsigned not null default 1');
    }
}
