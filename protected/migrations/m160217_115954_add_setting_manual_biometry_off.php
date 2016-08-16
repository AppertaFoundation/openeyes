<?php

class m160217_115954_add_setting_manual_biometry_off extends CDbMigration
{
    public function up()
    {
        $this->insert('setting_installation', array('key' => 'disable_manual_biometry', 'value' => 'off'));
    }

    public function down()
    {
        $this->delete('setting_installation', "`key` = 'disable_manual_biometry'");
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
