<?php

class m160603_140012_display_institution_name extends OEMigration
{
    public function up()
    {
        $this->insert('setting_metadata', array('display_order' => 0, 'field_type_id' => 3, 'key' => 'display_institution_name', 'name' => 'Enable institution name in address', 'data' => 'a:2:{s:2:"on";s:2:"On";s:3:"off";s:3:"Off";}', 'default_value' => 'on'));
    }

    public function down()
    {
        $this->delete('setting_metadata', '`key`="display_institution_name"');
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
