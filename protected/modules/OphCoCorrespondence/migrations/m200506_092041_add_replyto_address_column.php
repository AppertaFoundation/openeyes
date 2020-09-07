<?php

class m200506_092041_add_replyto_address_column extends OEMigration
{
    public function up()
    {
        $this->addOEColumn('ophcocorrespondence_sender_email_addresses', 'reply_to_address', 'text', true);
    }

    public function down()
    {
        $this->dropOEColumn('ophcocorrespondence_sender_email_addresses', 'reply_to_address', true);
    }
}
