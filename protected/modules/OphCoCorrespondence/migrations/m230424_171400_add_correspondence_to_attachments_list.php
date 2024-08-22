<?php

class m230424_171400_add_correspondence_to_attachments_list extends OEMigration
{
    public function safeUp()
    {
        $this->update(
            'event_type',
            ['is_printable' => 1],
            'class_name = \'OphCoCorrespondence\''
        );
    }

    public function safeDown()
    {
        $this->update(
            'event_type',
            ['is_printable' => 0],
            'class_name = \'OphCoCorrespondence\''
        );
    }
}
