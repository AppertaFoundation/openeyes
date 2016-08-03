<?php

class m140116_110606_event_pending extends CDbMigration
{
    public function up()
    {
        $this->addColumn('event', 'delete_pending', 'tinyint(1) unsigned not null');
    }

    public function down()
    {
        $this->dropColumn('event', 'delete_pending');
    }
}
