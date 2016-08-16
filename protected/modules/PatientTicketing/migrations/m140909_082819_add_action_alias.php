<?php

class m140909_082819_add_action_alias extends CDbMigration
{
    public function up()
    {
        $this->addColumn('patientticketing_queue', 'action_label', 'string');
        $this->addColumn('patientticketing_queue_version', 'action_label', 'string');
    }

    public function down()
    {
        $this->dropColumn('patientticketing_queue_version', 'action_label', 'string');
        $this->dropColumn('patientticketing_queue', 'action_label', 'string');
    }
}
