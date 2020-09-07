<?php

class m180801_053856_add_column_hidden_to_event_med_use extends OEMigration
{
    public function up()
    {
        $this->addOEColumn('event_medication_use', 'hidden', 'BOOLEAN NOT NULL DEFAULT 0', true);
    }

    public function down()
    {
        $this->dropOEColumn('event_medication_use', 'hidden', true);
    }

    /*
    // Use safeUp/safeDown to do migration with transaction
    public function safeUp()
    {
    }

    public function safeDown()
    {
    }
    */
}
