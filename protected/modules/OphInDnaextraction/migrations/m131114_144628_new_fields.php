<?php

class m131114_144628_new_fields extends CDbMigration
{
    public function up()
    {
        $this->createTable('ophindnaextraction_dnaextraction_study', array(
                'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
                'name' => 'varchar(100) COLLATE utf8_bin NOT NULL',
                'display_order' => 'int(10) unsigned NOT NULL',
                'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
                'last_modified_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
                'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
                'created_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
                'PRIMARY KEY (`id`)',
                'KEY `ophindnaextraction_dnaextraction_study_lmui_fk` (`last_modified_user_id`)',
                'KEY `ophindnaextraction_dnaextraction_study_cui_fk` (`created_user_id`)',
                'CONSTRAINT `ophindnaextraction_dnaextraction_study_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)',
                'CONSTRAINT `ophindnaextraction_dnaextraction_study_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
            ), 'ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin');

        $this->createTable('ophindnaextraction_dnaextraction_transaction', array(
                'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
                'element_id' => 'int(10) unsigned NOT NULL',
                'investigator_id' => 'int(10) unsigned NOT NULL',
                'study_id' => 'int(10) unsigned NOT NULL',
                'volume' => 'float NOT NULL',
                'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
                'last_modified_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
                'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
                'created_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
                'PRIMARY KEY (`id`)',
                'KEY `ophindnaextraction_dnaextraction_transaction_lmui_fk` (`last_modified_user_id`)',
                'KEY `ophindnaextraction_dnaextraction_transaction_cui_fk` (`created_user_id`)',
                'KEY `ophindnaextraction_dnaextraction_transaction_ele_fk` (`element_id`)',
                'KEY `ophindnaextraction_dnaextraction_transaction_ini_fk` (`investigator_id`)',
                'KEY `ophindnaextraction_dnaextraction_transaction_sti_fk` (`study_id`)',
                'CONSTRAINT `ophindnaextraction_dnaextraction_transaction_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)',
                'CONSTRAINT `ophindnaextraction_dnaextraction_transaction_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
                'CONSTRAINT `ophindnaextraction_dnaextraction_transaction_ele_fk` FOREIGN KEY (`element_id`) REFERENCES `et_ophindnaextraction_dnaextraction` (`id`)',
                'CONSTRAINT `ophindnaextraction_dnaextraction_transaction_ini_fk` FOREIGN KEY (`investigator_id`) REFERENCES `user` (`id`)',
                'CONSTRAINT `ophindnaextraction_dnaextraction_transaction_sti_fk` FOREIGN KEY (`study_id`) REFERENCES `ophindnaextraction_dnaextraction_study` (`id`)',
            ), 'ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin');
    }

    public function down()
    {
        $this->dropTable('ophindnaextraction_dnaextraction_transaction');
        $this->dropTable('ophindnaextraction_dnaextraction_study');
    }
}
