<?php

class m200716_060218_realign_contact_table_with_versioned extends OEMigration
{
    public function up()
    {
        // realigning and re-enabling contact table collation after previous migrations caused misalignment.
        // fixes issues introduced by m190403_233858_increase_contact_name_size_for_practice_name and m170331_085338_change_contact_table_collation

        $this->alterOEColumn('contact', 'first_name', 'VARCHAR(300) CHARACTER SET utf8 COLLATE utf8_unicode_ci', true);
        $this->alterOEColumn('contact', 'last_name', 'VARCHAR(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci', true);
    }

    public function down()
    {
        echo "m200716_060218_realign_contact_table_with_versioned does not support migration down.\n";
        return false;
    }
}
