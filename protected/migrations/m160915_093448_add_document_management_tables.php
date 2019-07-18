<?php

class m160915_093448_add_document_management_tables extends OEMigration
{
    public function up()
    {
        $this->createOETable('document_set', array(
            'id' => 'pk',
            'event_id' => 'int(10) unsigned NOT NULL',
            'allowMultipleDocs' => 'tinyint(4) NOT NULL DEFAULT 1',
            'allowChangeTemplates' => 'tinyint(4) NOT NULL DEFAULT 1',
            'allowChangeRecipients' => 'tinyint(4) NOT NULL DEFAULT 1',
            'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
            'created_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\''
        ),true);

        $this->createOETable('document_instance', array(
            'id' => 'pk',
            'document_set_id' => 'int(11) NOT NULL',
            'correspondence_event_id' => 'int(10) unsigned NOT NULL',
            'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
            'created_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\''
        ),true);

        $this->createOETable('document_instance_data', array(
            'id' => 'pk',
            'document_instance_id' => 'int(11) NOT NULL',
            'macro_id' => 'int(10) unsigned',
            'start_datetime' => 'datetime NOT NULL',
            'end_datetime' => 'datetime DEFAULT NULL',
            'use_nickname' => 'tinyint(1) unsigned NOT NULL DEFAULT 0',
            'date' => 'datetime NOT NULL',
            'address' => 'varchar(1024) COLLATE utf8_unicode_ci DEFAULT NULL',
            'introduction' => 'varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL',
            're' => 'varchar(1024) COLLATE utf8_unicode_ci DEFAULT NULL',
            'body' => 'text COLLATE utf8_unicode_ci',
            'footer' => 'varchar(2048) COLLATE utf8_unicode_ci DEFAULT NULL',
            'cc' => 'text COLLATE utf8_unicode_ci',
            'draft' => 'tinyint(1) unsigned NOT NULL DEFAULT 0',
            'macro_modified' => 'tinyint(1) unsigned NOT NULL DEFAULT 0',
            'mime_type' => 'varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL',
            'data_blob' => 'mediumblob',
            'html_blob' => 'mediumblob',
            'version_number' => 'tinyint(3) NOT NULL DEFAULT 0',
            'created_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
            'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1'
        ), true);

        $this->createOETable('document_output', array(
            'id' => 'pk',
            'document_target_id' => 'int(11) NOT NULL',
            'ToCc' => 'varchar(2) COLLATE utf8_unicode_ci NOT NULL',
            'output_type' => 'varchar(10) COLLATE utf8_unicode_ci NOT NULL',
            'output_status' => "enum('DRAFT','PENDING','PENDING_RETRY','FAILED','COMPLETE') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'DRAFT'",
            'document_instance_data_id' => 'int(11) NOT NULL',
            'requestor_id' => 'int(10) unsigned NOT NULL',
            'request_datetime' => 'datetime DEFAULT NULL',
            'success_datetime' => 'datetime DEFAULT NULL',
            'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
            'created_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\''
        ), true);

        $this->createOETable('document_target', array(
            'id' => 'pk',
            'document_instance_id' => 'int(11) NOT NULL',
            'contact_type' => "enum('PATIENT','GP','DRSS','LEGACY','OTHER') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'OTHER'",
            'contact_id' => 'int(10) unsigned DEFAULT NULL',
            'contact_name' => 'varchar(255) COLLATE utf8_unicode_ci NOT NULL',
            'contact_modified' => 'tinyint(1) unsigned NOT NULL DEFAULT 0',
            'address' => 'varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL',
            'email' => 'varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL',
            'created_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
            'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1'
        ), true);

        $this->addForeignKey('fk_document_set_event_id', 'document_set', 'event_id', 'event', 'id');
        $this->addForeignKey('fk_document_set_created_user_id', 'document_set', 'created_user_id', 'user', 'id');

        $this->addForeignKey('fk_document_instance_document_set_id', 'document_instance', 'document_set_id', 'document_set', 'id');
        $this->addForeignKey('fk_document_instance_correspondence_event_id', 'document_instance', 'correspondence_event_id', 'event', 'id');
        $this->addForeignKey('fk_document_instance_created_user_id', 'document_instance', 'created_user_id', 'user', 'id');

        $this->addForeignKey('fk_document_instance_data_document_instance_id', 'document_instance_data', 'document_instance_id', 'document_instance', 'id');
        $this->addForeignKey('fk_document_instance_data_created_user_id', 'document_instance_data', 'created_user_id', 'user', 'id');

        $this->addForeignKey('fk_document_output_document_target_id', 'document_output', 'document_target_id', 'document_target', 'id');
        $this->addForeignKey('fk_document_output_document_instance_data_id', 'document_output', 'document_instance_data_id', 'document_instance_data', 'id');
        $this->addForeignKey('fk_document_output_created_user_id', 'document_output', 'created_user_id', 'user', 'id');


        $this->addForeignKey('fk_document_target_document_instance_id', 'document_target', 'document_instance_id', 'document_instance', 'id');
        $this->addForeignKey('fk_document_target_created_user_id', 'document_target', 'created_user_id', 'user', 'id');

    }

    public function down()
    {
        $this->dropForeignKey('fk_document_target_created_user_id', 'document_target');
        $this->dropForeignKey('fk_document_target_document_instance_id', 'document_target');

        $this->dropForeignKey('fk_document_output_created_user_id', 'document_output');
        $this->dropForeignKey('fk_document_output_document_instance_data_id', 'document_output');
        $this->dropForeignKey('fk_document_output_document_target_id', 'document_output');

        $this->dropForeignKey('fk_document_instance_data_created_user_id', 'document_instance_data');
        $this->dropForeignKey('fk_document_instance_data_macro_id', 'document_instance_data');
        $this->dropForeignKey('fk_document_instance_data_document_instance_id', 'document_instance_data');

        $this->dropForeignKey('fk_document_instance_created_user_id', 'document_instance');
        $this->dropForeignKey('fk_document_instance_correspondence_event_id', 'document_instance');
        $this->dropForeignKey('fk_document_instance_document_set_id', 'document_instance');

        $this->dropForeignKey('fk_document_set_created_user_id', 'document_set');
        $this->dropForeignKey('fk_document_set_event_id', 'document_set');

        $this->dropTable('document_target');
        $this->dropTable('document_target_version');
        $this->dropTable('document_output');
        $this->dropTable('document_output_version');
        $this->dropTable('document_instance_data');
        $this->dropTable('document_instance_data_version');
        $this->dropTable('document_instance');
        $this->dropTable('document_instance_version');
        $this->dropTable('document_set');
        $this->dropTable('document_set_version');
    }

    /*
    // Use safeUp/safeDown to do migration with transaction
    public function safeUp()
    {
    }

    public function safeDown()
    {
    }
    */
}
