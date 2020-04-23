<?php

class m170714_145116_associated_content extends CDbMigration
{
    public function up()
    {

        $this->createTable('macro_init_associated_content', array(
            'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
            'macro_id' => 'int(10) unsigned NOT NULL',
            'is_system_hidden' => 'tinyint(1) NOT NULL',
            'is_print_appended' => 'tinyint(1) NOT NULL',
            'init_protected_file_id' => 'int(10) unsigned NULL',
            'short_code' => 'varchar(45) NOT NULL',
            'display_order' => 'int(3) NULL',
            'display_title' => 'varchar(45) NULL',
            'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
            'last_modified_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
            'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
            'created_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
            'PRIMARY KEY (`id`)',
            'KEY `fk_macro_init_associated_content_ophcocorrespondence_letter_idx` (`macro_id`)',
            'KEY `fk_macro_init_associated_content_protected_file1_idx` (`init_protected_file_id`)',
            'CONSTRAINT `fk_macro_init_associated_content_ophcocorrespondence_letter_m1` FOREIGN KEY (`macro_id`) REFERENCES `ophcocorrespondence_letter_macro` (`id`)',
            'CONSTRAINT `fk_macro_init_associated_content_protected_file1` FOREIGN KEY (`init_protected_file_id`) REFERENCES `protected_file` (`id`)',
        ), 'ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci');

        $this->createTable('event_associated_content', array(
            'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
            'parent_event_id' => 'int(10) unsigned NOT NULL',
            'is_system_hidden' => 'tinyint(1) NOT NULL',
            'is_print_appended' => 'tinyint(1) NOT NULL',
            'short_code' => 'varchar(45) NOT NULL',
            'association_storage' => 'varchar(10) NOT NULL',
            'associated_event_id' => 'int(10) unsigned NULL',
            'associated_protected_file_id' => 'int(10) unsigned NULL',
            'associated_url' => 'varchar(255) NULL',
            'display_order' => 'int(3) NOT NULL',
            'display_title' => 'varchar(80) NULL',
            'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
            'last_modified_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
            'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
            'created_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
            'PRIMARY KEY (`id`)',
            'KEY `fk_event_associated_content_event1_idx` (`parent_event_id`)',
            'KEY `fk_event_associated_content_event2_idx` (`associated_event_id`)',
            'KEY `fk_event_associated_content_protected_file1_idx` (`associated_protected_file_id`)',
            'CONSTRAINT `fk_event_associated_content_event1` FOREIGN KEY (`parent_event_id`) REFERENCES `event` (`id`)',
            'CONSTRAINT `fk_event_associated_content_event2` FOREIGN KEY (`associated_event_id`) REFERENCES `event` (`id`)',
            'CONSTRAINT `fk_event_associated_content_protected_file1` FOREIGN KEY (`associated_protected_file_id`) REFERENCES `protected_file` (`id`)',
        ), 'ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci');

    }

    public function down()
    {
        $this->dropForeignKey('fk_event_associated_content_protected_file1', 'event_associated_content');
        $this->dropForeignKey('fk_event_associated_content_event2', 'event_associated_content');
        $this->dropForeignKey('fk_event_associated_content_event1', 'event_associated_content');
        $this->dropIndex('fk_event_associated_content_protected_file1_idx', 'event_associated_content');
        $this->dropIndex('fk_event_associated_content_event2_idx', 'event_associated_content');
        $this->dropIndex('fk_event_associated_content_event1_idx', 'event_associated_content');
        $this->dropTable('event_associated_content');

        $this->dropForeignKey('fk_macro_init_associated_content_protected_file1', 'macro_init_associated_content');
        $this->dropForeignKey('fk_macro_init_associated_content_ophcocorrespondence_letter_m1', 'macro_init_associated_content');
        $this->dropIndex('fk_macro_init_associated_content_protected_file1_idx', 'macro_init_associated_content');
        $this->dropIndex('fk_macro_init_associated_content_ophcocorrespondence_letter_idx', 'macro_init_associated_content');
        $this->dropTable('macro_init_associated_content');
    }
}
