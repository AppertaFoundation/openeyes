<?php

class m200221_000912_correspondence_email_parameters extends OEMigration
{
    public function safeUp()
    {
        $radio_button_field_type_id = $this->dbConnection->createCommand()
            ->select('id')
            ->from('setting_field_type')
            ->where('name = :name', array(':name' => 'Radio buttons'))
            ->queryScalar();

        $this->insert('setting_metadata', array(
            'display_order' => 0,
            'field_type_id' => $radio_button_field_type_id,
            'key' => 'manually_add_emails_correspondence',
            'name' => 'Correspondence Allow users to manually add recipients emails',
            'data' => serialize(['on'=>'On', 'off'=>'Off']),
            'default_value' => 'off'
        ));

        $this->insert('setting_installation', array(
            'key' => 'manually_add_emails_correspondence',
            'value' => 'off'
        ));

        $this->insert('setting_metadata', array(
            'display_order' => 0,
            'field_type_id' => $radio_button_field_type_id,
            'key' => 'send_email_immediately',
            'name' => 'Send Emails Immediately',
            'data' => serialize(['on'=>'On', 'off'=>'Off']),
            'default_value' => 'off'
        ));

        $this->insert('setting_installation', array(
            'key' => 'send_email_immediately',
            'value' => 'off'
        ));

        $this->insert('setting_metadata', array(
            'display_order' => 0,
            'field_type_id' => $radio_button_field_type_id,
            'key' => 'send_email_delayed',
            'name' => 'Send Emails Delayed',
            'data' => serialize(['on'=>'On', 'off'=>'Off']),
            'default_value' => 'off'
        ));

        $this->insert('setting_installation', array(
            'key' => 'send_email_delayed',
            'value' => 'off'
        ));

        $text_field_type_id = $this->dbConnection->createCommand()
            ->select('id')
            ->from('setting_field_type')
            ->where('name = :name', array(':name' => 'Text Field'))
            ->queryScalar();

        $this->insert('setting_metadata', array(
            'element_type_id' => null,
            'field_type_id' => $text_field_type_id,
            'key' => 'correspondence_delayed_email_processing',
            'name' => 'Correspondence delayed email processing (minutes)',
            'default_value' => '10',
            'data' => ''
        ));

        $this->insert('setting_installation', array(
            'key' => 'correspondence_delayed_email_processing',
            'value' => '10'
        ));
    }

    public function safeDown()
    {
        $this->delete('setting_installation', '`key`="correspondence_delayed_email_processing"');
        $this->delete('setting_metadata', '`key`="correspondence_delayed_email_processing"');
        $this->delete('setting_installation', '`key`="send_email_delayed"');
        $this->delete('setting_metadata', '`key`="send_email_delayed"');
        $this->delete('setting_installation', '`key`="send_email_immediately"');
        $this->delete('setting_metadata', '`key`="send_email_immediately"');
        $this->delete('setting_installation', '`key`="manually_add_emails_correspondence"');
        $this->delete('setting_metadata', '`key`="manually_add_emails_correspondence"');
    }
}
