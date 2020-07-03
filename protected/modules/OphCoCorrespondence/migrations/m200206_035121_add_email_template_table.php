<?php

class m200206_035121_add_email_template_table extends OEMigration
{
    public function safeUp()
    {
        $this->createOETable(
            'ophcocorrespondence_email_template',
            array(
                'id' => 'pk',
                'institution_id' => 'int(10) unsigned NULL',
                'site_id' => 'int(10) unsigned NULL',
                'recipient_type' => 'varchar(20) NOT NULL',
                'title' => 'varchar(255)',
                'subject' => 'varchar(255)',
                'body' => 'text'
            ),
            true
        );

        $this->addForeignKey(
            'ophcocorrespondence_email_template_institution_fk',
            'ophcocorrespondence_email_template',
            'institution_id',
            'institution',
            'id'
        );

        $this->addForeignKey(
            'ophcocorrespondence_email_template_site_fk',
            'ophcocorrespondence_email_template',
            'site_id',
            'site',
            'id'
        );
    }

    public function safeDown()
    {
        $this->dropForeignKey(
            'ophcocorrespondence_email_template_institution_fk',
            'ophcocorrespondence_email_template'
        );

        $this->dropForeignKey(
            'ophcocorrespondence_email_template_site_fk',
            'ophcocorrespondence_email_template'
        );

        $this->dropOETable('ophcocorrespondence_email_template', true);
    }
}
