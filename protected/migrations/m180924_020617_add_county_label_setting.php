<?php

class m180924_020617_add_county_label_setting extends OEMigration
{

	public function safeUp()
	{
        $this->insert('setting_metadata', array('element_type_id' => null,
                'field_type_id' => 4,
                'key' => 'county_label',
                'name' => 'County label',
                'default_value' => 'County'
            )
        );

        $this->insert('setting_installation', array('key' => 'county_label', 'value' => 'County'));
	}

	public function safeDown()
	{
        $this->delete('setting_metadata', '`key` = \'county_label\'');
	}

}