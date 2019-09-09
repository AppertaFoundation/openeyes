<?php

class m170228_190108_ophindnasample_volume_null extends CDbMigration
{
    public function up()
    {
        $this->execute("alter table et_ophindnasample_sample modify column volume float null default null;");
        return true;

    }

    public function down()
    {
        $this->execute("alter table et_ophindnasample_sample modify column volume float not null default 0;");
        return true;
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