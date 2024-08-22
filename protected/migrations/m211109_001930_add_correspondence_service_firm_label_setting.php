<?php

class m211109_001930_add_correspondence_service_firm_label_setting extends CDbMigration
{
	public function safeUp()
	{
		$text_field_id = $this->getDbConnection()->createCommand('select id from setting_field_type where name ="Text Field"')->queryScalar();

        $this->insert('setting_metadata', array(
            'element_type_id' => null,
            'field_type_id' => $text_field_id,
            'key' => 'correspondence_service_firm_label',
            'name' => 'Firm consultant label for Correspondence footer',
            'data' => '',
            'default_value' => 'Consultant',
        ));

	}

	public function safeDown()
	{
		$this->delete('setting_metadata', '`key` = "correspondence_service_firm_label"');
	}
}
