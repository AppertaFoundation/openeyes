<?php

class m180924_020617_add_state_label_setting extends CDbMigration
{

	public function safeUp()
	{
        $this->insert('setting_metadata', array('element_type_id' => null,
                'field_type_id' => 4,
                'key' => 'state_label',
                'name' => 'State label',
                'default_value' => 'State'
            )
        );

        $this->insert('setting_installation', array('key' => 'state_label', 'value' => ''));
	}

	public function safeDown()
	{
        $this->delete('setting_metadata', '`key` = \'state_label\'');
	}

}