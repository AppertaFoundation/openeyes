<?php

class m170315_102839_ask_correspondence_approval extends OEMigration
{
    public function up()
    {
        $this->insert('setting_metadata', array(
            'field_type_id' => '3',
            'key' => 'ask_correspondence_approval',
            'name' => 'Ask Correspondence Approval',
            'data' => 'a:2:{s:2:"on";s:2:"On";s:3:"off";s:3:"Off";}',
            'default_value' => 'on',
        ));

        $this->insert('setting_installation', array(
            'key' => 'ask_correspondence_approval',
            'value' => 'on',
        ));
    }

    public function down()
    {
        $this->delete('setting_metadata', '`key` = "ask_correspondence_approval"');
        $this->delete('setting_installation', '`key` = "ask_correspondence_approval"');
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