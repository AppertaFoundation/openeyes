<?php

class m211007_113628_modify_version_anaesthetist_id_to_match_original_table extends CDbMigration
{

    public function safeUp()
    {
        $this->alterColumn('et_ophtroperationnote_anaesthetic_version', 'anaesthetist_id', 'int(10) unsigned DEFAULT NULL');
    }

    public function safeDown()
    {
        $this->alterColumn('et_ophtroperationnote_anaesthetic_version', 'anaesthetist_id', 'int(10) unsigned NOT NULL DEFAULT 4');
    }
}
