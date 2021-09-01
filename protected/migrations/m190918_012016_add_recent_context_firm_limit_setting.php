<?php

class m190918_012016_add_recent_context_firm_limit_setting extends OEMigration
{

    // Use safeUp/safeDown to do migration with transaction
    public function safeUp()
    {
        //Add the metadata row so that the setting can be adjusted on the front end
            $this->insert('setting_metadata', ['element_type_id' => null,
                    'field_type_id' => 4,
                    'key' => 'recent_context_firm_limit',
                    'name' => 'Limit top Recent Context results',
                    'default_value' => '6',
                    'data' => ''
                ]);

        //Add the setting row itself that contains the live value data
            $this->insert('setting_installation', ['key' => 'recent_context_firm_limit', 'value' => '6']);
    }

    public function safeDown()
    {
        //delete the metadata row
        $this->delete('setting_metadata', '`key` = ?', ["recent_context_firm_limit"]);
        //delete the value row
        $this->delete('setting_installation', '`key` = ?', ["recent_context_firm_limit"]);
    }
}
