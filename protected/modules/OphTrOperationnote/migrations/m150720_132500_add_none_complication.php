<?php

class m150720_132500_add_none_complication extends CDbMigration
{
    public function up()
    {
        $this->insert('ophtroperationnote_anaesthetic_anaesthetic_complications', array(
            'name' => 'None',
            'display_order' => 11,
            'active' => 1,
        ));
    }

    public function down()
    {
        $this->delete('ophtroperationnote_anaesthetic_anaesthetic_complications', '`name` = "None"');
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
