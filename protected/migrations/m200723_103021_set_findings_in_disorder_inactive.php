<?php

class m200723_103021_set_findings_in_disorder_inactive extends CDbMigration
{
    public function safeUp()
    {
        $this->execute("update disorder set active=0 where term like '%O/E%'");
    }

    public function safeDown()
    {
        $this->execute("update disorder set active=1 where term like '%O/E%'");
    }
}
