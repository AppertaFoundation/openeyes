<?php

class m131107_115127_rename_field extends CDbMigration
{
    public function up()
    {
        $this->dropForeignKey('et_ophindnaextraction_dnaextraction_sev_fk', 'et_ophindnaextraction_dnaextraction');
        $this->dropIndex('et_ophindnaextraction_dnaextraction_sev_fk', 'et_ophindnaextraction_dnaextraction');
        $this->dropColumn('et_ophindnaextraction_dnaextraction', 'sample_event_id');
    }

    public function down()
    {
        $this->addColumn('et_ophindnaextraction_dnaextraction', 'sample_event_id', 'int(10) unsigned NOT NULL');
        $this->createIndex('et_ophindnaextraction_dnaextraction_sev_fk', 'et_ophindnaextraction_dnaextraction', 'sample_event_id');
        $this->addForeignKey('et_ophindnaextraction_dnaextraction_sev_fk', 'et_ophindnaextraction_dnaextraction', 'sample_event_id', 'event', 'id');
    }
}
