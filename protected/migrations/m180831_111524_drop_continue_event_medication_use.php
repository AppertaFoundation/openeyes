<?php

class m180831_111524_drop_continue_event_medication_use extends CDbMigration
{
    public function up()
    {
        $this->dropColumn('event_medication_use', 'continue');
        $this->dropColumn('event_medication_use_version', 'continue');
    }

    public function down()
    {
        $this->addColumn('event_medication_use', 'continue', 'BOOLEAN NOT NULL DEFAULT 0');
        $this->addColumn('event_medication_use_version', 'continue', 'BOOLEAN NOT NULL DEFAULT 0');
    }
}
