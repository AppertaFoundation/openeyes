<?php

class m230418_044003_added_auto_save_flag_to_drafts extends OEMigration
{
    public function safeUp()
    {
        $this->addColumn('event_draft', 'is_auto_save', 'BOOLEAN NOT NULL');
    }

    public function safeDown()
    {
        $this->dropColumn('event_draft', 'is_auto_save');
    }
}
