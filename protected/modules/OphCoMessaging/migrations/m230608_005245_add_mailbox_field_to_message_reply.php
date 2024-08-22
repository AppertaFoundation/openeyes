<?php

class m230608_005245_add_mailbox_field_to_message_reply extends OEMigration
{
    // Use safeUp/safeDown to do migration with transaction
    public function safeUp()
    {
        $this->addOEColumn('ophcomessaging_message_comment', 'mailbox_id', 'INT(11) DEFAULT NULL', true);
        $this->addForeignKey('ophcomessaging_message_comment_mid', 'ophcomessaging_message_comment', 'mailbox_id', 'mailbox', 'id');
    }

    public function safeDown()
    {
        $this->dropForeignKey('ophcomessaging_message_comment_mid', 'ophcomessaging_message_comment');
        $this->dropOEColumn('ophcomessaging_message_comment', 'mailbox_id', true);
    }
}
