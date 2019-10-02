<?php

class m191002_003710_add_display_on_whiteboard_column extends OEMigration
{
    public function up()
    {
        $this->addOEColumn('ophciexamination_risk', 'display_on_whiteboard', 'tinyint(1) DEFAULT 1');
    }

    public function down()
    {
        $this->dropOEColumn('ophciexamination_risk', 'display_on_whiteboard');
    }
}