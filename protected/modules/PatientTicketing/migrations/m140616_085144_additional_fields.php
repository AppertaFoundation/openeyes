<?php

class m140616_085144_additional_fields extends CDbMigration
{
    public function up()
    {
        $this->addColumn('patientticketing_queue', 'assignment_fields', 'text');
        $this->addColumn('patientticketing_queue_version', 'assignment_fields', 'text');
        $this->addColumn('patientticketing_queue', 'summary_link', 'boolean DEFAULT false');
        $this->addColumn('patientticketing_queue_version', 'summary_link', 'boolean DEFAULT false');
    }

    public function down()
    {
        $this->dropColumn('patientticketing_queue_version', 'summary_link');
        $this->dropColumn('patientticketing_queue', 'summary_link');
        $this->dropColumn('patientticketing_queue_version', 'assignment_fields');
        $this->dropColumn('patientticketing_queue', 'assignment_fields');
    }
}
