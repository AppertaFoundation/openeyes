<?php

class m170216_114943_add_missing_FKs extends CDbMigration
{
    public function up()
    {
            $this->addForeignKey('ophindnaextraction_storage_address_ibfk_1', 'ophindnaextraction_storage_address', 'box_id', 'ophindnaextraction_dnaextraction_box', 'id');
            $this->addForeignKey('et_ophindnaextraction_dnaextraction_ibfk_1', 'et_ophindnaextraction_dnaextraction', 'storage_id', 'ophindnaextraction_storage_address', 'id');

            $this->dropTable('ophindnaextraction_dnaextraction_letter');
            $this->dropTable('ophindnaextraction_dnaextraction_number');
    }

    public function down()
    {
        $this->dropForeignKey('ophindnaextraction_storage_address_ibfk_1', 'ophindnaextraction_storage_address');
        $this->dropForeignKey('et_ophindnaextraction_dnaextraction_ibfk_1', 'et_ophindnaextraction_dnaextraction');

                $this->createTable('ophindnaextraction_dnaextraction_letter', array(
                    'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
                    'value' => 'varchar(2) COLLATE utf8_bin NOT NULL',
                    'display_order' => 'int(10) unsigned NOT NULL',
                    'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
                    'last_modified_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
                    'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
                    'created_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
                    'PRIMARY KEY (`id`)',
                    'KEY `ophindnaextraction_dnaextraction_letter_lmui_fk` (`last_modified_user_id`)',
                    'KEY `ophindnaextraction_dnaextraction_letter_cui_fk` (`created_user_id`)',
                    'CONSTRAINT `ophindnaextraction_dnaextraction_letter_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)',
                    'CONSTRAINT `ophindnaextraction_dnaextraction_letter_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
                ), 'ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin');

                $this->createTable('ophindnaextraction_dnaextraction_number', array(
                        'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
                        'value' => 'varchar(5) COLLATE utf8_bin NOT NULL',
                        'display_order' => 'int(10) unsigned NOT NULL',
                        'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
                        'last_modified_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
                        'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
                        'created_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
                        'PRIMARY KEY (`id`)',
                        'KEY `ophindnaextraction_dnaextraction_number_lmui_fk` (`last_modified_user_id`)',
                        'KEY `ophindnaextraction_dnaextraction_number_cui_fk` (`created_user_id`)',
                        'CONSTRAINT `ophindnaextraction_dnaextraction_number_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)',
                        'CONSTRAINT `ophindnaextraction_dnaextraction_number_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
                    ), 'ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin');
    }
}
