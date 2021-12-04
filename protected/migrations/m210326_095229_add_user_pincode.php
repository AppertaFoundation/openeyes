<?php

class m210326_095229_add_user_pincode extends OEMigration
{
    // Use safeUp/safeDown to do migration with transaction
    public function safeUp()
    {
        $this->addOEColumn(
            'user_authentication',
            'pincode',
            'varchar(255) AFTER username',
            true
        );
    }

    public function safeDown()
    {
        $this->dropOEColumn('user', 'pincode', true);
    }
}
