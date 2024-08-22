<?php

class m200115_001547_add_nhs_hos_num_label_short extends CDbMigration
{
    public function safeUp()
    {
        $this->insert('setting_metadata', array('element_type_id' => null,
            'field_type_id' => 4,
            'key' => 'nhs_num_label_short',
            'name' => 'NHS Number short label',
            'default_value' => 'NHS'
            ));

        $this->insert('setting_metadata', array('element_type_id' => null,
            'field_type_id' => 4,
            'key' => 'hos_num_label_short',
            'name' => 'Hospital Number short label',
            'default_value' => 'ID'
            ));

        $this->insert('setting_installation', array('key' => 'nhs_num_label_short', 'value' => 'NHS'));
        $this->insert('setting_installation', array('key' => 'hos_num_label_short', 'value' => 'ID'));
    }

    public function safeDown()
    {
        $this->delete('setting_metadata', '`key` = \'nhs_num_label_short\'');
        $this->delete('setting_metadata', '`key` = \'hos_num_label_short\'');
    }
}
