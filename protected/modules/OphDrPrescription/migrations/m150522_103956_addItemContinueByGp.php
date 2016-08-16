<?php

class m150522_103956_addItemContinueByGp extends CDbMigration
{
    public function up()
    {
        $this->addColumn('ophdrprescription_item', 'continue_by_gp', 'tinyint(1) not null default 0');
        $this->addColumn('ophdrprescription_item_version', 'continue_by_gp', 'tinyint(1) not null default 0');
    }

    public function down()
    {
        $this->dropColumn('ophdrprescription_item', 'continue_by_gp');
        $this->dropColumn('ophdrprescription_item_version', 'continue_by_gp');
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
