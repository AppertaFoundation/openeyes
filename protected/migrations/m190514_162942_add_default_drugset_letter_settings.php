<?php

class m190514_162942_add_default_drugset_letter_settings extends CDbMigration
{
	public function safeUp()
	{
	    $field_type_id = \SettingFieldType::model()->findByAttributes(['name' => 'Text Field'])->id;
        $this->insert('setting_metadata', [
                'element_type_id' => null,
                'field_type_id' => $field_type_id,
                'key' => 'default_post_op_drug_set',
                'name' => 'Default Post-op Drug Set name',
                'default_value' => 'Post-op',
                'data' => ''
            ]
        );
        $this->insert('setting_installation', array('key' => 'default_post_op_drug_set', 'value' => 'Post-op'));
	}

	public function safeDown()
	{
		$this->delete('setting_metadata', ['key' => 'default_post_op_drug_set']);
	}
}