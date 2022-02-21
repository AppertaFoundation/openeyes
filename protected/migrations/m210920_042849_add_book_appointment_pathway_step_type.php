<?php

class m210920_042849_add_book_appointment_pathway_step_type extends OEMigration
{
    public function safeUp()
    {

        $this->insert('pathway_step_type', [
            'group' => 'standard',
            'widget_view' => 'appointment_booking',
            'type' => 'process',
            'long_name' => 'Book Follow-up Appointment',
            'short_name' => 'Book Apt.',
            'active' => 1,
        ]);
    }

    public function safeDown()
    {
        $this->delete('pathway_step_type', 'short_name = "Book Apt."');
    }
}
