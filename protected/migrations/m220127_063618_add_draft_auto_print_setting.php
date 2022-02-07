<?php

class m220127_063618_add_draft_auto_print_setting extends OEMigration
{
    public function safeUp()
    {
        $this->insert('setting_metadata', array(
            'display_order' => 0,
            'field_type_id' => 3,
            'key' => 'disable_draft_auto_print',
            'name' => 'Disable Auto Printing for Draft Correspondence',
            'data' => serialize(array('on'=>'On', 'off'=>'Off')),
            'default_value' => 'off'
        ));
        $this->insert('setting_installation', array('key' => 'disable_draft_auto_print', 'value' => 'off'));
    }

    public function safeDown()
    {
        $this->delete('setting_metadata', "`key` = 'disable_draft_auto_print'");
        $this->delete('setting_installation', "`key` = 'disable_draft_auto_print'");
    }
}
