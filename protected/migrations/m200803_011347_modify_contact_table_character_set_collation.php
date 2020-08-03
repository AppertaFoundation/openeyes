<?php

class m200803_011347_modify_contact_table_character_set_collation extends OEMigration
{
    public function up()
    {
        // fixes issues introduced by m190403_233858_increase_contact_name_size_for_practice_name and m200716_060218_realign_contact_table_with_versioned

        $this->alterOEColumn('contact', 'first_name', 'VARCHAR(300) CHARACTER SET utf8 COLLATE utf8_general_ci', true);
        $this->alterOEColumn('contact', 'last_name', 'VARCHAR(100) CHARACTER SET utf8 COLLATE utf8_general_ci', true);
        $this->alterOEColumn('contact', 'maiden_name', 'VARCHAR(100) CHARACTER SET utf8 COLLATE utf8_general_ci', true);
    }

    public function down()
    {
        $this->alterOEColumn('contact', 'first_name', 'VARCHAR(300) CHARACTER SET utf8 COLLATE utf8_unicode_ci', true);
        $this->alterOEColumn('contact', 'last_name', 'VARCHAR(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci', true);
        $this->alterOEColumn('contact', 'maiden_name', 'VARCHAR(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci', true);
    }
}
