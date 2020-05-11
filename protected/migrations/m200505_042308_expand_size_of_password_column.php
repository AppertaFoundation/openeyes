<?php

/*
 * Though bcrypt only requires 60 characters for its hashes, expanding the password
 * column to 255 characters allows future changes to the hashing algorithm used without
 * requiring another migration to further expand column length.
 */

class m200505_042308_expand_size_of_password_column extends CDbMigration
{
    public function up()
    {
        $this->alterColumn('user', 'password', 'varchar(255)');
        $this->alterColumn('user_version', 'password', 'varchar(255)');
    }

    public function down()
    {
        echo "m200505_042308_expand_size_of_password_column does not support migration down.\n";
        return false;
    }
}
