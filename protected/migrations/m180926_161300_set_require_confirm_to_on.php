<?php

class m180926_161300_set_require_confirm_to_on extends CDbMigration
{
    public function up()
    {
        $this->update('setting_metadata', array( 'default_value' => 'on' ), '`key`="element_close_warning_enabled"');
        $this->update('setting_installation', array( 'value' => 'on' ), '`key`="element_close_warning_enabled"');
    }


    public function down()
    {
        echo 'Down method not supported';

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
