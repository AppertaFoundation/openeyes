<?php

class m140703_142323_central_threshold_patterns extends OEMigration
{
    // Use safeUp/safeDown to do migration with transaction
    public function safeUp()
    {
        $this->initialiseData(__DIR__);
    }

    public function safeDown()
    {
    }
}
