<?php

class m170831_141425_event_associated_table_fix extends CDbMigration
{
    public function up()
    {
        $this->addColumn('event_associated_content', 'init_associated_content_id', 'int(10) AFTER parent_event_id');
    }

    public function down()
    {
        $this->dropColumn('event_associated_content', 'init_associated_content_id');
    }
}