<?php

class m180614_052521_add_new_settings_presc_header_footer extends CDbMigration
{
    public function up()
    {
        $this->insert('setting_metadata', array(
            'field_type_id' => 5,
            'key' => 'prescription_boilerplate_header',
            'name' => "Prescription: Boilerplate text for header",
        ));

        $this->insert('setting_metadata', array(
            'field_type_id' => 5,
            'key' => 'prescription_boilerplate_footer',
            'name' => "Prescription: Boilerplate text for footer",
        ));
    }

    public function down()
    {
        $this->delete('setting_metadata', "`key`='prescription_boilerplate_header'");
        $this->delete('setting_metadata', "`key`='prescription_boilerplate_footer'");
    }
}
