<?php

class m140619_145356_colourvision_option_change extends OEMigration
{
    public function up()
    {
        $this->update('ophciexamination_colourvision_method', array('name' => 'Isihara /15'), 'id = 1');
        $this->update('ophciexamination_colourvision_method', array('display_order' => 3), 'id = 2');
        $migrations_path = dirname(__FILE__);
        $this->initialiseData($migrations_path);
    }

    public function down()
    {
        //echo "m140619_145356_colourvision_option_change does not support migration down.\n";
        //return false;
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
