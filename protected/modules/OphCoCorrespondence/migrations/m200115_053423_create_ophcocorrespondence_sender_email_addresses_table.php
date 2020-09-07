<?php

class m200115_053423_create_ophcocorrespondence_sender_email_addresses_table extends OEMigration
{
    public function safeUp()
    {
        $this->createOETable('ophcocorrespondence_sender_email_addresses', array(
            'id' => 'pk',
            'host' => 'text NOT NULL',
            'username' => 'text NOT NULL',
            'password' => 'text NOT NULL',
            'port' => 'int(10) unsigned NOT NULL',
            'security' => 'text NOT NULL',
            'institution_id' => 'int(10) unsigned NULL',
            'site_id' => 'int(10) unsigned NULL',
            'domain' => 'text NOT NULL'
        ), true);

        $this->addForeignKey(
            'ophcocorrespondence_sender_email_addresses_institution_fk',
            'ophcocorrespondence_sender_email_addresses',
            'institution_id',
            'institution',
            'id'
        );

        $this->addForeignKey(
            'ophcocorrespondence_sender_email_addresses_site_fk',
            'ophcocorrespondence_sender_email_addresses',
            'site_id',
            'site',
            'id'
        );
    }

    public function safeDown()
    {
        $this->dropForeignKey(
            'ophcocorrespondence_sender_email_addresses_institution_fk',
            'ophcocorrespondence_sender_email_addresses'
        );

        $this->dropForeignKey(
            'ophcocorrespondence_sender_email_addresses_site_fk',
            'ophcocorrespondence_sender_email_addresses'
        );

        $this->dropOETable('ophcocorrespondence_sender_email_addresses', true);
    }
}
