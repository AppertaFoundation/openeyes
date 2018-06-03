<?php

class m180603_225310_add_enable_notattip_warning_setting extends OEMigration
{
    public function safeUp()
    {
        $this->insert('setting_metadata', array('element_type_id' => null,
            'field_type_id' => 3,
            'key' => 'show_notattip_warning',
            'name' => 'Show "Not at Tip" element warning messages',
            'default_value' => 'off',
            'data' => serialize(array('on'=>'On', 'off'=>'Off'))
        ));
    }

    public function safeDown()
    {
        $this->delete('setting_metadata', '`key` = \'show_notattip_warning\'');
    }
}
