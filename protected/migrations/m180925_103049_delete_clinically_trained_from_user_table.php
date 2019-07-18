<?php

class m180925_103049_delete_clinically_trained_from_user_table extends CDbMigration
{
    public function up()
    {
        $this->dropColumn('user', 'is_clinical');
        $this->dropColumn('user_version', 'is_clinical');
    }
    public function down()
    {
        $this->addColumn('user' , 'is_clinical' , 'tinyint(1) unsigned NOT NULL DEFAULT 0');
        $this->addColumn('user_version' , 'is_clinical' , 'tinyint(1) unsigned NOT NULL DEFAULT 0');
    }
}