<?php

class m150526_091657_allergyAddDisplayOrder extends CDbMigration
{
    public function up()
    {
        $this->addColumn('allergy', 'display_order', 'tinyint(3) unsigned NOT NULL default 0');
        $this->addColumn('allergy_version', 'display_order', 'tinyint(3) unsigned NOT NULL default 0');
    }

    public function down()
    {
        $this->dropColumn('allergy', 'display_order');
        $this->dropColumn('allergy_version', 'display_order');
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
