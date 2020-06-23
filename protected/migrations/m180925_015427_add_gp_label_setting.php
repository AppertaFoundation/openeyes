<?php

class m180925_015427_add_gp_label_setting extends OEMigration
{
    public function safeUp()
    {
        $this->insert('setting_metadata', array('element_type_id' => null,
                'field_type_id' => 4,
                'key' => 'gp_label',
                'name' => 'GP label',
                'default_value' => 'GP',
                'data' => ''
            ));
        $this->insert('setting_installation', array('key' => 'gp_label', 'value' => 'GP'));
    }

    public function safeDown()
    {
        $this->delete('setting_metadata', '`key` = \'gp_label\'');
    }

}
