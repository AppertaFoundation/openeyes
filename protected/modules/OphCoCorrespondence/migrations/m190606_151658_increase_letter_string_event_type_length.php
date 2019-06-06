<?php

class m190606_151658_increase_letter_string_event_type_length extends CDbMigration
{
    public function up()
    {
        $this->alterColumn('ophcocorrespondence_letter_string', 'event_type', 'varchar(200)');
    }

    public function down()
    {
        $this->alterColumn('ophcocorrespondence_letter_string', 'event_type', 'varchar(64)');
    }
}