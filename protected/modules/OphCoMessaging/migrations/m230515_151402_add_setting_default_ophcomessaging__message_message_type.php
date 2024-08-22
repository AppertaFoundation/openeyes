<?php

class m230515_151402_add_setting_default_ophcomessaging__message_message_type extends OEMigration
{
    public function safeUp()
    {
        $this->execute("INSERT IGNORE INTO setting_group (`name`) VALUES ('Message')");
        $this->addSetting(
            'deafult_ophcomessaging_message_message_type',
            "Default message type",
            'If you have configured a message type (in Admin->Message->Message Sub type settings) with the given name, then it will be selected by deafault when creating a new message. If you have not configured a message type with the given name, then the first message type will be selected by default. when creating a new message.<br><br>Leave blank to make no default selection and force the user to make a choice.',
            'Message',
            'Text Field',
            '',
            null,
        );
    }

    public function safeDown()
    {
        $this->deleteSetting('deafult_ophcomessaging_message_message_type');
        $this->execute("DELETE FROM setting_group WHERE `name` = 'Message'");
    }
}
