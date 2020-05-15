<?php

class m190122_102950_add_show_attachments_column_to_event_type_table extends CDbMigration
{
    public function safeUp()
    {
        $this->addColumn('event_type', 'show_attachments', 'boolean default false');
        $this->addColumn('event_type_version', 'show_attachments', 'boolean default false');

        $this->update('event_type', array('show_attachments' => true), "class_name = 'OphCiExamination'");
        $this->update('event_type', array('show_attachments' => true), "class_name = 'OphTrOperationbooking'");
        $this->update('event_type', array('show_attachments' => true), "class_name = 'OphTrOperationnote'");
        $this->update('event_type', array('show_attachments' => true), "class_name = 'OphTrLaser'");
        $this->update('event_type', array('show_attachments' => true), "class_name = 'OphTrIntravitrealinjection'");
        $this->update('event_type', array('show_attachments' => true), "class_name = 'OphGeneric'");
    }

    public function safeDown()
    {
        $this->dropColumn('event_type', 'show_attachments');
        $this->dropColumn('event_type_version', 'show_attachments');
    }
}
