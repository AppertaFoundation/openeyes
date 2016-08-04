<?php

class m141002_104319_queue_event_types extends OEMigration
{
    public function up()
    {
        $this->createTable('patientticketing_queue_event_type', array(
                'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
                'queue_id' => 'int(11) not null',
                'event_type_id' => 'int(10) unsigned not null',
                'display_order' => 'tinyint(1) unsigned not null',
                'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
                'last_modified_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
                'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
                'created_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
                'PRIMARY KEY (`id`)',
                'KEY `patientticketing_queue_last_modified_user_id_fk` (`last_modified_user_id`)',
                'KEY `patientticketing_queue_created_user_id_fk` (`created_user_id`)',
                'KEY `patientticketing_queue_queue_id_fk` (`queue_id`)',
                'KEY `patientticketing_queue_event_type_id_fk` (`event_type_id`)',
                'CONSTRAINT `patientticketing_queue_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)',
                'CONSTRAINT `patientticketing_queue_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
                'CONSTRAINT `patientticketing_queue_queue_id_fk` FOREIGN KEY (`queue_id`) REFERENCES `patientticketing_queue` (`id`)',
                'CONSTRAINT `patientticketing_queue_event_type_id_fk` FOREIGN KEY (`event_type_id`) REFERENCES `event_type` (`id`)',
            ), 'ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci');

        $this->versionExistingTable('patientticketing_queue_event_type');
    }

    public function down()
    {
        $this->dropTable('patientticketing_queue_event_type_version');
        $this->dropTable('patientticketing_queue_event_type');
    }
}
