<?php

class m170113_133457_move_PCR_order extends CDbMigration
{
    public function up()
    {
        $this->update('element_type', array('display_order' => 94), 'name = :nm', array(':nm' => 'PCR Risk'));
    }

    public function down()
    {
        echo "m170113_133457_move_PCR_order does not support migration down.\n";
        return false;
    }


    // Use safeUp/safeDown to do migration with transaction
    public function safeUp()
    {
        $this->update('element_type', array('display_order' => 94), 'name = :nm', array(':nm' => 'PCR Risk'));
    }

    public function safeDown()
    {
        echo "m170113_133457_move_PCR_order does not support migration down.\n";
        return false;
    }

}