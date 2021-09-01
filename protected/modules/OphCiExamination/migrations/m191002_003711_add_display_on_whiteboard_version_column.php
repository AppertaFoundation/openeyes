<?php

class m191002_003711_add_display_on_whiteboard_version_column extends OEMigration
{
    public function up()
    {
        $result = $this->dbConnection->createCommand("SHOW COLUMNS FROM `ophciexamination_risk_version` LIKE 'display_on_whiteboard'")->query();
        if (count($result) == 0) {
            $this->addColumn('ophciexamination_risk_version', 'display_on_whiteboard', 'tinyint(1) DEFAULT 1 after version_id');
        }
    }

    public function down()
    {
        echo 'this migration does not support down';
    }
}
