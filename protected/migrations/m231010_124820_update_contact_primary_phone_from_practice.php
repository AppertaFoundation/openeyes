<?php

class m231010_124820_update_contact_primary_phone_from_practice extends CDbMigration
{
    public function safeUp()
    {
        $sql = 'UPDATE contact
                JOIN practice ON contact.id = practice.contact_id
                SET contact.primary_phone = practice.phone;';
        $this->execute($sql);
    }

    public function safeDown()
    {
        echo "m231010_124820_update_contact_primary_phone_from_practice does not support migration down.\n";
        return false;
    }
}
