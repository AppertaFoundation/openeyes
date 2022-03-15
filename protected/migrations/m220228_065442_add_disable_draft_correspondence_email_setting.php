<?php

class m220228_065442_add_disable_draft_correspondence_email_setting extends CDbMigration
{
	public function safeUp()
	{
		$this->insert('setting_metadata', array(
            'display_order' => 0,
            'field_type_id' => 3,
            'key' => 'disable_draft_correspondence_email',
            'name' => 'Disable Emailing of Draft Correspondence',
            'data' => serialize(array('on'=>'On', 'off'=>'Off')),
            'default_value' => 'off'
        ));

        $this->insert('setting_installation', array('key' => 'disable_draft_correspondence_email', 'value' => 'off'));
	}

	public function safeDown()
	{
        $this->delete('setting_metadata', "`key` = 'disable_draft_correspondence_email'");
        $this->delete('setting_installation', "`key` = 'disable_draft_correspondence_email'");
	}
}
