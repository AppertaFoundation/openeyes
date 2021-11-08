<?php

class m211101_053438_Add_setting_for_correspondence_gp_address_as_practice extends CDbMigration
{
    public function up()
    {
        $this->insert('setting_metadata', array(
            'field_type_id' => 3,
            'key' => 'correspondence_gp_address',
            'name' => 'GP address to use in correspondence',
            'default_value' => 'practice_add',
            'data' => serialize(array('GP_add' => 'GP address', 'practice_add' => 'Practice address', 'practice_name' => 'Practice name + address'))
        ));

        $this->insert('setting_installation', array(
            'key' => 'correspondence_gp_address',
            'value' => 'practice_add',
        ));

        $this->insert('setting_metadata', array(
            'field_type_id' => 4,
            'key' => 'correspondence_address_max_lines',
            'name' => 'Max number of lines an address can use in a correspondence',
            'default_value' => '-1',
        ));

        $this->insert('setting_installation', array(
            'key' => 'correspondence_address_max_lines',
            'value' => '-1',
        ));
    }

    public function down()
    {
        $this->delete('setting_metadata', '`key` = "correspondence_gp_address"');
        $this->delete('setting_installation', '`key` = "correspondence_gp_address"');
        $this->delete('setting_metadata', '`key` = "correspondence_address_max_lines"');
        $this->delete('setting_installation', '`key` = "correspondence_address_max_lines"');
    }
}
