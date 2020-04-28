<?php

class m190313_103152_add_column_for_event_type_to_hide_from_manual_creation extends CDbMigration
{
    public function safeUp()
    {
        $this->addColumn('event_type', 'can_be_created_manually', 'tinyint(1) not null default 1');
        $this->addColumn('event_type_version', 'can_be_created_manually', 'tinyint(1) not null default 1');
    }

    public function safeDown()
    {
        $this->dropColumn('event_type', 'can_be_created_manually');
        $this->dropColumn('event_type_version', 'can_be_created_manually');
    }
}
