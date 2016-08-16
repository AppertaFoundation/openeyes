<?php

class m141209_111300_appointment_types extends OEMigration
{
    public function up()
    {
        $this->createTable('patientticketing_appointment_type', array(
                'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
                'name' => 'varchar(255) not null',
                'display_order' => 'tinyint(1) unsigned not null',
                'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
                'last_modified_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
                'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
                'created_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
                'PRIMARY KEY (`id`)',
                'KEY `patientticketing_appointment_type_last_modified_user_id_fk` (`last_modified_user_id`)',
                'KEY `patientticketing_appointment_type_created_user_id_fk` (`created_user_id`)',
                'CONSTRAINT `patientticketing_appointment_type_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)',
                'CONSTRAINT `patientticketing_appointment_type_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
            ), 'ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci');

        $this->versionExistingTable('patientticketing_appointment_type');

        $this->initialiseData(__DIR__);
    }

    public function down()
    {
        $this->dropTable('patientticketing_appointment_type_version');
        $this->dropTable('patientticketing_appointment_type');
    }
}
