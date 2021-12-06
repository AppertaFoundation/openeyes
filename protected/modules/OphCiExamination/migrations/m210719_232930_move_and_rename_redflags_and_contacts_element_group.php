<?php

class m210719_232930_move_and_rename_redflags_and_contacts_element_group extends OEMigration
{
    public function safeUp()
    {
        $this->update('element_group', array('name' => 'Red Flags', 'display_order' => 25), "name = 'A & E'");

        $this->update('element_group', array('display_order' => 15), "name = 'Contacts'");
    }

    public function safeDown()
    {

        $this->update('element_group', array('display_order' => 20), "name = 'Contacts'");

        $this->update('element_group', array('name' => 'A & E', 'display_order' => 220), "name = 'Red Flags'");
    }
}
