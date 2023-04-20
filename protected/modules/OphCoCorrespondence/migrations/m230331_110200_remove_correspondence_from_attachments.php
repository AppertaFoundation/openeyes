<?php

class m230331_110200_remove_correspondence_from_attachments extends OEMigration
{
    public function safeUp()
    {
        $this->update(
            'event_type',
            ['is_printable' => 0],
            'class_name = \'OphCoCorrespondence\''
        );
    }

    public function safeDown()
    {
        $this->update(
            'event_type',
            ['is_printable' => 1],
            'class_name = \'OphCoCorrespondence\''
        );
    }
}
