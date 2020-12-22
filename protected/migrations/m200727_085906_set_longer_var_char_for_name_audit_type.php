<?php

class m200727_085906_set_longer_var_char_for_name_audit_type extends OEMigration
{

    public function safeUp()
    {
        $this->alterOEColumn('audit_type', 'name', 'varchar(255) NOT NULL');
    }

    public function safeDown()
    {
        echo "m200727_085906_set_longer_var_char_for_name_audit_type does not support migration down.\n";
    }
}
