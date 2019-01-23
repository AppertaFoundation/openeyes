<?php

class m180927_050129_add_general_practitioner_label_setting extends OEMigration
{
    public function safeUp()
    {
        $this->insert('setting_metadata', array('element_type_id' => null,
                'field_type_id' => 4,
                'key' => 'general_practitioner_label',
                'name' => 'General Practitioner label',
                'default_value' => 'General Practitioner'
            )
        );
        $this->insert('setting_installation', array('key' => 'general_practitioner_label', 'value' => 'General Practitioner'));
    }

    public function safeDown()
    {
        $this->delete('setting_metadata', '`key` = \'general_practitioner_label\'');
    }

}