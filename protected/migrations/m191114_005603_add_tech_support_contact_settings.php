<?php

class m191114_005603_add_tech_support_contact_settings extends OEMigration
{
    public function safeUp()
    {
        $this->insert(
            'setting_metadata',
            array(
                'field_type_id' => 4,
                'key' => 'tech_support_url',
                'name' => 'Technical Support URL',
                'default_value' => '',
            )
        );

        $this->insert(
            'setting_metadata',
            array(
                'field_type_id' => 4,
                'key' => 'tech_support_provider',
                'name' => 'Technical Support provider',
                'default_value' => '',
            )
        );
    }

    public function safeDown()
    {
        $this->delete('setting_metadata', '`key` = "tech_support_provider"');
        $this->delete('setting_metadata', '`key` = "tech_support_url"');
    }
}
