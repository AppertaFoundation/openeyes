<?php

class m150723_153151_surgeon_display_order extends CDbMigration
{
    public function up()
    {
        $this->update('element_type', array('display_order' => 2), 'name = "Surgeon" and event_type_id = 4');
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
