<?php

class m210305_114800_prevent_new_creation extends OEMigration
{
    public function safeUp()
    {
        $this->update('event_type', ['can_be_created_manually' => 0], "`class_name`='OphInVisualfields'");
    }

    public function down()
    {
        $this->update('event_type', ['can_be_created_manually' => 1], "`class_name`='OphInVisualfields'");
    }
}
