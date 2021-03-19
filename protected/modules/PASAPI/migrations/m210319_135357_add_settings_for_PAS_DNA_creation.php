<?php

class m210319_135357_add_settings_for_PAS_DNA_creation extends CDbMigration
{
    public function up()
    {
        $checkbox_field_type_id = $this->dbConnection
            ->createCommand('SELECT `id` FROM setting_field_type WHERE `name`="Radio buttons"')
            ->queryScalar();

        $this->insert('setting_metadata', [
            'field_type_id' => $checkbox_field_type_id,
            'key' => 'DNA_autogen_enabled',
            'name' => 'Enable automatic creation of Did Not Attend events from PAS',
            'data' => serialize(['on' => 'On', 'off' => 'Off']),
            'default_value' => 'off'
        ]);

        $text_field_type_id = $this->dbConnection
            ->createCommand('SELECT `id` FROM setting_field_type WHERE `name`="Text Field"')
            ->queryScalar();

        $this->insert('setting_metadata', [
            'field_type_id' => $text_field_type_id,
            'key' => 'DNA_autogen_message',
            'name' => 'Source message for automatically generated Did Not Attend events',
            'data' => null,
            'default_value' => ''
        ]);
    }

    public function down()
    {
        $this->delete('setting_metadata', '`key` = "DNA_autogen_enabled"');
        $this->delete('setting_metadata', '`key` = "DNA_autogen_message"');
        return false;
    }
}
