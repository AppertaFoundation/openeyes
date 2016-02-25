<?php

class m150602_155541_update_visual_function_order extends CDbMigration
{
    public function up()
    {
        $this->update('element_type', array('display_order' => 10), 'name = "Visual Acuity"');
        $this->update('element_type', array('display_order' => 30), 'name = "Colour Vision"');
    }

    public function down()
    {
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
