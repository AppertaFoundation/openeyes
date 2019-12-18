<?php

class m190403_233858_increase_contact_name_size_for_practice_name extends OEMigration
{
    // Use safeUp/safeDown to do migration with transaction
    public function safeUp()
    {
        $this->alterColumn('contact', 'first_name', 'varchar(300)');
    }

    public function safeDown()
    {
        $this->alterColumn('contact', 'first_name', 'varchar(100)');
    }
}