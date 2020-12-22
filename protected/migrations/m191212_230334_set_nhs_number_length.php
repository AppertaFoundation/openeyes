<?php

class m191212_230334_set_nhs_number_length extends OEMigration
{
    public function safeUp()
    {
        $this->insert(
            'setting_metadata',
            array(
                'field_type_id' => 4,
                'key' => 'nhs_num_length',
                'name' => 'NHS Number Length',
                'default_value' => 10,
            )
        );
    }

    public function safeDown()
    {
        $this->delete('setting_metadata', '`key` = "nhs_num_length"');
    }

}
