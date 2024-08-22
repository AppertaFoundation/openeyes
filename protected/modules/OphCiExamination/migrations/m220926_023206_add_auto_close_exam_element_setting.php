<?php

class m220926_023206_add_auto_close_exam_element_setting extends OEMigration
{
    public function safeUp()
    {
        $this->insert('setting_metadata', array(
            'display_order' => 0,
            'field_type_id' => 3,
            'key' => 'close_incomplete_exam_elements',
            'name' => 'Offer to automatically close incomplete examination elements',
            'lowest_setting_level' => 'INSTALLATION',
            'data' => serialize(array('on' => 'On', 'off' => 'Off')),
            'default_value' => 'off',
            'description' => '',
            'group_id' => 1
        ));
    }

    public function safeDown()
    {
        $this->delete('setting_institution', '`key` = "close_incomplete_exam_elements"');
        $this->delete('setting_installation', '`key` = "close_incomplete_exam_elements"');
        $this->delete('setting_metadata', '`key` = "close_incomplete_exam_elements"');
    }
}
