<?php

class m200424_131331_replace_address_details extends OEMigration
{
    public function safeUp()
    {
        foreach (['address1','address2','city'] as $address_field) {
            $this->execute("UPDATE address SET ".$address_field." = REPLACE(".$address_field.", '\\\T\\\', '&') WHERE (".$address_field." LIKE '%\\\T\\\%' ESCAPE '|');");
        }
    }

    public function safeDown()
    {
        echo "m200424_131331_replace_address_details does not support migration down.\n";
    }
}
