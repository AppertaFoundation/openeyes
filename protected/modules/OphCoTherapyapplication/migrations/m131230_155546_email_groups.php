<?php

class m131230_155546_email_groups extends CDbMigration
{
    public function up()
    {
        $this->createTable('ophcotherapya_email_recipient_type', array(
                'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
                'name' => 'varchar(255) NOT NULL',
                'display_order' => 'tinyint(1) unsigned not null',
                'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
                'last_modified_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
                'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
                'created_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
                'PRIMARY KEY (`id`)',
                'KEY `ophcotherapya_email_recipient_type_lmui_fk` (`last_modified_user_id`)',
                'KEY `ophcotherapya_email_recipient_type_cui_fk` (`created_user_id`)',
                'CONSTRAINT `ophcotherapya_email_recipient_type_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)',
                'CONSTRAINT `ophcotherapya_email_recipient_type_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
            ), 'ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci');

        $this->insert('ophcotherapya_email_recipient_type', array('id' => 1, 'name' => 'Compliant', 'display_order' => 1));
        $this->insert('ophcotherapya_email_recipient_type', array('id' => 2, 'name' => 'Non-compliant', 'display_order' => 2));

        $this->createTable('ophcotherapya_email_recipient', array(
                'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
                'site_id' => 'int(10) unsigned null',
                'recipient_name' => 'varchar(255) NOT NULL',
                'recipient_email' => 'varchar(255) NOT NULL',
                'type_id' => 'int(10) unsigned null',
                'display_order' => 'tinyint(1) unsigned not null',
                'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
                'last_modified_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
                'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
                'created_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
                'PRIMARY KEY (`id`)',
                'KEY `ophcotherapya_email_recipient_lmui_fk` (`last_modified_user_id`)',
                'KEY `ophcotherapya_email_recipient_cui_fk` (`created_user_id`)',
                'KEY `ophcotherapya_email_recipient_site_id_fk` (`site_id`)',
                'KEY `ophcotherapya_email_recipient_type_id_fk` (`type_id`)',
                'CONSTRAINT `ophcotherapya_email_recipient_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)',
                'CONSTRAINT `ophcotherapya_email_recipient_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
                'CONSTRAINT `ophcotherapya_email_recipient_site_id_fk` FOREIGN KEY (`site_id`) REFERENCES `site` (`id`)',
                'CONSTRAINT `ophcotherapya_email_recipient_type_id_fk` FOREIGN KEY (`type_id`) REFERENCES `ophcotherapya_email_recipient_type` (`id`)',
            ), 'ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci');
    }

    public function down()
    {
        $this->dropTable('ophcotherapya_email_recipient');
        $this->dropTable('ophcotherapya_email_recipient_type');
    }
}
