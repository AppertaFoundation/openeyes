<?php

class m160819_171457_add_user_signature extends CDbMigration
{
    public function up()
    {
        $this->addColumn('user', 'signature_file_id', 'int(10) unsigned');
        $this->addColumn('user_version', 'signature_file_id', 'int(10) unsigned');

        $this->addForeignKey('signature_file_fk', 'user', 'signature_file_id', 'protected_file', 'id');
    }

    public function down()
    {
        $this->dropForeignKey('signature_file_fk', 'user');
        $this->dropColumn('user_version', 'signature_file_id');
    }

    /*
    // Use safeUp/safeDown to do migration with transaction
    public function safeUp()
    {
    }

    public function safeDown()
    {
    }
    */
}
