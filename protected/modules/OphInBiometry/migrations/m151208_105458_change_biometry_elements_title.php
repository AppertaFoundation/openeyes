<?php

class m151208_105458_change_biometry_elements_title extends CDbMigration
{
    public function up()
    {
        $this->Update('element_type', array('name' => 'Biometry'), "class_name='Element_OphInBiometry_Measurement'");
        $this->Update('element_type', array('name' => '[-Selection-]'), "class_name='Element_OphInBiometry_Selection'");
    }

    public function down()
    {
        $this->Update('element_type', array('name' => 'Measurement'), "class_name='Element_OphInBiometry_Measurement'");
        $this->Update('element_type', array('name' => 'Selection'), "class_name='Element_OphInBiometry_Selection'");
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
