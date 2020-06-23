<?php

class m180605_115100_add_NHS_num_label_setting extends OEMigration
{
    public function safeUp()
    {
        $this->insert('setting_metadata', array('element_type_id' => null,
            'field_type_id' => 4,
            'key' => 'nhs_num_label',
            'name' => 'NHS Number label',
            'default_value' => 'NHS'
            ));

        $this->insert('setting_installation', array('key' => 'nhs_num_label', 'value' => 'NHS'));
    }

    public function safeDown()
    {
        $this->delete('setting_metadata', '`key` = \'nhs_num_label\'');
    }
}
