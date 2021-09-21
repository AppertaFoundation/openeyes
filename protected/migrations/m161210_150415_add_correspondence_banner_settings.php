<?php

class m161210_150415_add_correspondence_banner_settings extends CDbMigration
{
    public function up()
    {
            $this->alterColumn('setting_installation', 'value', 'VARCHAR(355) NOT NULL');
            $this->alterColumn('setting_installation_version', 'value', 'VARCHAR(355) NOT NULL');

            $this->insert('setting_metadata', array(
                'field_type_id' => 4,
                'key' => 'correspondence_create_banner',
                'name' => 'Correspondence create banner',
            ));
    }

    public function down()
    {
            $this->delete('setting_metadata', '`key`="correspondence_create_banner"');
            $this->delete('setting_installation', '`key`="correspondence_create_banner"');

            $this->alterColumn('setting_installation', 'value', 'VARCHAR(255) NOT NULL');
            $this->alterColumn('setting_installation_version', 'value', 'VARCHAR(255) NOT NULL');
    }
}
