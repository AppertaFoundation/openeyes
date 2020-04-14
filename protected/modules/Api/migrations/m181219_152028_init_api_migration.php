<?php

class m181219_152028_init_api_migration extends OEMigration
{
    public function safeUp()
    {
        $this->createOETable('request', [
            'id' => 'pk',
            'request_type' => 'VARCHAR(45) NOT NULL',
            'system_message' => 'VARCHAR(255) NOT NULL',
            'request_override_default_queue' => 'VARCHAR(45) DEFAULT NULL'
        ], false);

        $this->createTable('request_queue', [
            'request_queue' => 'VARCHAR(45) NOT NULL PRIMARY KEY',
            'maximum_active_threads' => 'INT(2) NOT NULL',
            'total_active_thread_count' => 'INT(10) DEFAULT 0',
            'total_execute_count' => 'INT(10) NOT NULL DEFAULT 0',
            'total_success_count' => 'INT(10) NOT NULL DEFAULT 0',
            'total_fail_count' => 'INT(10) NOT NULL DEFAULT 0',
            'busy_yield_ms' => 'INT(6) NOT NULL',
            'idle_yield_ms' => 'INT(6) NOT NULL',
            'last_poll_date' => 'DATETIME NULL',
            'last_thread_spawn_date' => 'DATETIME NULL',
            'last_thread_spawn_request_id' => 'INT(10)'
        ], 'engine=InnoDB charset=utf8 collate=utf8_unicode_ci');

        $this->createTable('request_queue_lock', [
            'request_queue' => 'VARCHAR(45) NOT NULL PRIMARY KEY'
        ], 'engine=InnoDB charset=utf8 collate=utf8_unicode_ci');

        $this->createTable('request_type', [
            'request_type' => 'VARCHAR(45) PRIMARY KEY',
            'title_full' => 'VARCHAR(45) NOT NULL',
            'title_short' => 'VARCHAR(45) NOT NULL',
            'default_routine_name' => 'VARCHAR(50) NOT NULL',
            'default_request_queue' => 'VARCHAR(45) NOT NULL'
        ], 'engine=InnoDB charset=utf8 collate=utf8_unicode_ci');

        $this->createTable('routine_library', [
            'routine_name' => 'VARCHAR(45) NOT NULL PRIMARY KEY',
            'hash_code' => 'BIGINT(100) NULL'
        ], 'engine=InnoDB charset=utf8 collate=utf8_unicode_ci');

        $this->createTable('request_routine', [
            'id' => 'pk',
            'request_id' => 'INT NOT NULL',
            'execute_request_queue' => 'VARCHAR(45) NOT NULL',
            'status' => 'VARCHAR(45) NOT NULL',
            'routine_name' => 'VARCHAR(45) NOT NULL',
            'try_count' => 'INT NOT NULL DEFAULT 0',
            'next_try_date_time' => 'DATETIME',
            'execute_sequence' => 'INT NOT NULL',
            'hash_code' => 'BIGINT(100) NULL',
        ], 'engine=InnoDB charset=utf8 collate=utf8_unicode_ci');

        $this->createTable('request_routine_execution', [
            'id' => 'pk',
            'log_text' => 'TEXT',
            'request_routine_id' => 'INT NOT NULL',
            'execution_date_time' => 'DATETIME NOT NULL',
            'status' => 'TEXT NOT NULL',
            'try_number' => 'INT NOT NULL'
        ], 'engine=InnoDB charset=utf8 collate=utf8_unicode_ci');


        $this->createOETable('attachment_data', [
            'id' => 'pk',
            'request_id' => 'INT(11) NOT NULL',
            'attachment_mnemonic' => 'VARCHAR(45) NOT NULL',
            'body_site_snomed_type' => 'VARCHAR(45) NULL',
            'system_only_managed' => 'INT(1) NOT NULL',
            'attachment_type' => 'VARCHAR(45) NOT NULL', // there are long formats application/vnd.openxmlformats-officedocument.presentationml.presentation
            'mime_type' => 'VARCHAR(100) NOT NULL',
            'blob_data' => 'LONGBLOB',
            'text_data' => 'LONGTEXT',
            'upload_file_name' => 'VARCHAR(200)',
            'thumbnail_small_blob' => 'MEDIUMBLOB NULL',
            'thumbnail_medium_blob' => 'MEDIUMBLOB NULL',
            'thumbnail_large_blob' => 'MEDIUMBLOB NULL',
            'CONSTRAINT unique_attachment_data_attachment_mnemonic_idx  UNIQUE (request_id, attachment_mnemonic , body_site_snomed_type)',
        ], false);

        $this->createTable('attachment_type', [
            'attachment_type' => 'VARCHAR(45) NOT NULL PRIMARY KEY',
            'title_full' => 'VARCHAR(45)',
            'title_short' => 'VARCHAR(45)',
            'title_abbreviated' => 'VARCHAR(45)'
        ], 'engine=InnoDB charset=utf8 collate=utf8_unicode_ci');

        $this->createTable('mime_type', [
            'mime_type' => 'VARCHAR(45) NOT NULL PRIMARY KEY'
        ], 'engine=InnoDB charset=utf8 collate=utf8_unicode_ci');

        $this->createTable('body_site_type', [
            'body_site_snomed_type' => 'VARCHAR(45) NOT NULL PRIMARY KEY',
            'title_full' => 'VARCHAR(45)',
            'title_short' => 'VARCHAR(45)',
            'title_abbreviated' => 'VARCHAR(45)'
        ], 'engine=InnoDB charset=utf8 collate=utf8_unicode_ci');

        $this->createOETable('event_attachment_item', [
            'id' => 'pk',
            'event_attachment_group_id' => 'INT(11) NOT NULL',
            'attachment_data_id' => 'INT NOT NULL',
            'system_only_managed' => 'INT(1) NOT NULL',
            'event_document_view_set' => 'VARCHAR(45) DEFAULT NULL'
        ], false);

        $this->createOETable('event_attachment_group', [
            'id' => 'pk',
            'event_id' => 'INT(10) unsigned NOT NULL',
            'element_type_id' => 'INT(10) unsigned NOT NULL'
        ], 'engine=InnoDB charset=utf8 collate=utf8_unicode_ci');

        $this->addForeignKey('request_fk_request_type', 'request', 'request_type', 'request_type', 'request_type');
        $this->addForeignKey('request_fk_attachment_data', 'attachment_data', 'request_id', 'request', 'id');
        $this->addForeignKey('request_type_fk_routine_library', 'request_type', 'default_routine_name', 'routine_library', 'routine_name');
        $this->addForeignKey('request_routine_fk_routine_library', 'request_routine', 'routine_name', 'routine_library', 'routine_name');
        $this->addForeignKey('request_routine_fk_request', 'request_routine', 'request_id', 'request', 'id');
        $this->addForeignKey('request_routine_execution_fk_request_routine', 'request_routine_execution', 'request_routine_id', 'request_routine', 'id');
        $this->addForeignKey('attachment_data_fk_attachment_type', 'attachment_data', 'attachment_type', 'attachment_type', 'attachment_type');
        $this->addForeignKey('attachment_data_fk_mime_type', 'attachment_data', 'mime_type', 'mime_type', 'mime_type');
        $this->addForeignKey('attachment_data_fk_body_site_type', 'attachment_data', 'body_site_snomed_type', 'body_site_type', 'body_site_snomed_type');
        $this->addForeignKey('execute_request_queue_fk_request_routine', 'request_routine', 'execute_request_queue', 'request_queue', 'request_queue');
        $this->addForeignKey('request_queue_fk_request_type', 'request_type', 'default_request_queue', 'request_queue', 'request_queue');
        $this->addForeignKey('request_override_default_queue_fk_request_queue', 'request', 'request_override_default_queue', 'request_queue', 'request_queue');
        $this->addForeignKey('request_queue_lock_fk_request_queue', 'request_queue_lock', 'request_queue', 'request_queue', 'request_queue');
        $this->addForeignKey('event_attachment_group_fk_event', 'event_attachment_group', 'event_id', 'event', 'id');
        $this->addForeignKey('event_attachment_group_fk_element_type', 'event_attachment_group', 'element_type_id', 'element_type', 'id');
        $this->addForeignKey('event_attachment_item_fk_event_attachment_group', 'event_attachment_item', 'event_attachment_group_id', 'event_attachment_group', 'id');
        $this->addForeignKey('event_attachment_item_fk_attachment_data', 'event_attachment_item', 'attachment_data_id', 'attachment_data', 'id');

        $this->initialiseData(dirname(__FILE__));
    }

    public function safeDown()
    {
        $this->dropForeignKey('request_fk_request_type', 'request');
        $this->dropForeignKey('request_fk_attachment_data', 'attachment_data');
        $this->dropForeignKey('request_type_fk_routine_library', 'request_type');
        $this->dropForeignKey('request_routine_fk_routine_library', 'request_routine');
        $this->dropForeignKey('request_routine_fk_request', 'request_routine');
        $this->dropForeignKey('request_routine_execution_fk_request_routine', 'request_routine_execution');
        $this->dropForeignKey('attachment_data_fk_attachment_type', 'attachment_data');
        $this->dropForeignKey('attachment_data_fk_mime_type', 'attachment_data');
        $this->dropForeignKey('attachment_data_fk_body_site_type', 'attachment_data');
        $this->dropForeignKey('execute_request_queue_fk_request_routine', 'request_routine');
        $this->dropForeignKey('request_queue_fk_request_type', 'request_type');
        $this->dropForeignKey('request_queue_lock_fk_request_queue', 'request_queue_lock');
        $this->dropForeignKey('request_override_default_queue_fk_request_queue', 'request');
        $this->dropForeignKey('event_attachment_group_fk_event', 'event_attachment_group');
        $this->dropForeignKey('event_attachment_group_fk_element_type', 'event_attachment_group');
        $this->dropForeignKey('event_attachment_item_fk_event_attachment_group', 'event_attachment_item');
        $this->dropForeignKey('event_attachment_item_fk_attachment_data', 'event_attachment_item');

        $this->dropTable('request_type');
        $this->dropTable('routine_library');
        $this->dropTable('request_routine');
        $this->dropTable('request_routine_execution');
        $this->dropOETable('request');

        $this->dropTable('attachment_type');
        $this->dropTable('mime_type');
        $this->dropTable('body_site_type');
        $this->dropTable('event_attachment_group');
        $this->dropOETable('event_attachment_item');
        $this->dropTable('request_queue');
        $this->dropTable('request_queue_lock');
        $this->dropOETable('attachment_data');
    }
}
