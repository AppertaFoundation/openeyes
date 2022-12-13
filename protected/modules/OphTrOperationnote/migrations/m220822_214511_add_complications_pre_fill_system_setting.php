<?php

class m220822_214511_add_complications_pre_fill_system_setting extends OEMigration
{
    // Use safeUp/safeDown to do migration with transaction
    public function safeUp()
    {
        $group_id = $this->dbConnection->createCommand("SELECT id FROM setting_group WHERE `name` = :group_name")->queryScalar([':group_name' => 'Operation Note']);

        $this->insert('setting_metadata', array(
            'element_type_id' => null,
            'display_order' => 0,
            'key' => 'allow_complications_in_pre_fill_templates',
            'name' => 'Allow complications to be stored in pre-fill templates',
            'field_type_id' => $this->dbConnection->createCommand('SELECT id FROM setting_field_type WHERE name = "Radio buttons"')->queryScalar(),
            'data' => 'a:2:{s:2:"on";s:2:"On";s:3:"off";s:3:"Off";}',
            'default_value' => 'off',
            'group_id' => $group_id,
            'description' => 'When enabled: For user defined op note templates, this allows the value of the complications selector to be stored as part of the template (e.g, always default to "None").
            
 When disabled, complications will not be part of any template and must be manually completed each time. For better data quiality it is recommended to keep this setting off, otherwise users may "forget" to record any complications that occurred during the surgery'
        ));

        $this->insert('setting_installation', array(
            'key' => 'allow_complications_in_pre_fill_templates',
            'value' => 'off'
        ));
    }

    public function safeDown()
    {
        $this->delete('setting_installation', '`key`="allow_complications_in_pre_fill_templates"');
        $this->delete('setting_metadata', '`key`="allow_complications_in_pre_fill_templates"');
    }
}
