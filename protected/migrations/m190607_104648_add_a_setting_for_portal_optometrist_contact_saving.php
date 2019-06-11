<?php

class m190607_104648_add_a_setting_for_portal_optometrist_contact_saving extends CDbMigration
{
    public function up()
    {
        $this->insert('setting_metadata', array(
            'field_type_id' => 3,
            'key' => 'disable_auto_import_optoms_from_portal',
            'name' => 'Disable Auto Import Of Optometrists From Portal',
            'default_value' => 'on',
            'data' => serialize(array('on'=>'On', 'off'=>'Off'))
        ));

        $this->insert('setting_installation', array(
            'key' => 'disable_auto_import_optoms_from_portal',
            'value' => 'on',
        ));
    }

    public function down()
    {
        $this->delete('setting_metadata', array('key = \'disable_auto_import_optoms_from_portal\''));
        $this->delete('setting_installation', array('key=\'disable_auto_import_optoms_from_portal\''));
    }
}