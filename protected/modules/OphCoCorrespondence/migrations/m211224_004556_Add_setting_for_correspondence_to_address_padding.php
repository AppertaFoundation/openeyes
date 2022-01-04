<?php

class m211224_004556_Add_setting_for_correspondence_to_address_padding extends OEMigration
{
    public function up()
    {
        $this->insert('setting_metadata', array(
            'field_type_id' => 4,
            'key' => 'correspondence_to_address_x_padding',
            'name' => 'Number of spaces to pad the left side of to address (default 0)',
            'default_value' => '0',
        ));

        $this->insert('setting_installation', array(
            'key' => 'correspondence_to_address_x_padding',
            'value' => '0',
        ));

        $this->insert('setting_metadata', array(
            'field_type_id' => 4,
            'key' => 'correspondence_to_address_y_padding',
            'name' => 'Number of empty lines to pad above to address (default 2)',
            'default_value' => '2',
        ));

        $this->insert('setting_installation', array(
            'key' => 'correspondence_to_address_y_padding',
            'value' => '2',
        ));
    }

    public function down()
    {
        $this->delete('setting_metadata', '`key` = "correspondence_to_address_x_padding"');
        $this->delete('setting_installation', '`key` = "correspondence_to_address_x_padding"');
        $this->delete('setting_metadata', '`key` = "correspondence_to_address_y_padding"');
        $this->delete('setting_installation', '`key` = "correspondence_to_address_y_padding"');
    }
}
