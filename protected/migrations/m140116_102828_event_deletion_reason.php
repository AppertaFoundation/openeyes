<?php

class m140116_102828_event_deletion_reason extends CDbMigration
{
    public function up()
    {
        $this->addColumn('event', 'delete_reason', 'varchar(4096) null');
    }

    public function down()
    {
        $this->dropColumn('event', 'delete_reason');
    }
}
