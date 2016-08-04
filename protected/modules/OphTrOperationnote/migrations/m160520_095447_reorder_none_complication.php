<?php

class m160520_095447_reorder_none_complication extends CDbMigration
{
    public function up()
    {
        $this->update('ophtroperationnote_anaesthetic_anaesthetic_complications', array('display_order' => 0), 'name = "None"');
    }

    public function down()
    {
        echo "m160520_095447_reorder_none_complication migration still in place, previous display_order unknown.\n";
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
