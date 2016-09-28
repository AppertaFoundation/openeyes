<?php

class m140327_140957_ward_display_order extends CDbMigration
{
    public function up()
    {
        $this->addColumn('ophtroperationbooking_operation_ward', 'display_order', 'integer');
        // simple default setting for us
        $this->dbConnection->createCommand('update ophtroperationbooking_operation_ward set display_order = id')->execute();
    }

    public function down()
    {
        $this->dropColumn('ophtroperationbooking_operation_ward', 'display_order');
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
