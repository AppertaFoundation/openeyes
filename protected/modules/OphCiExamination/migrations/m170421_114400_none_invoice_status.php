<?php

class m170421_114400_none_invoice_status extends CDbMigration
{
    public function up()
    {
        $this->insert('ophciexamination_invoice_status', array('name' => 'No status'));
    }

    public function down()
    {

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
