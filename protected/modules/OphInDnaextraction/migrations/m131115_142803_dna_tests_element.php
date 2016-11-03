<?php

class m131115_142803_dna_tests_element extends OEMigration
{
    public function up()
    {
        $this->dropTable('ophindnaextraction_dnaextraction_transaction');
        $this->dropTable('ophindnaextraction_dnaextraction_study');

        $event_type = $this->dbConnection->createCommand()->select('id')->from('event_type')->where('class_name=:class_name', array(':class_name' => 'OphInDnaextraction'))->queryRow();

        $this->insert('element_type', array('name' => 'DNA Withdrawals', 'class_name' => 'Element_OphInDnaextraction_DnaTests', 'event_type_id' => $event_type['id'], 'display_order' => 2));

        $this->createTable('et_ophindnaextraction_dnatests', array(
                'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
                'event_id' => 'int(10) unsigned NOT NULL',
                'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
                'last_modified_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
                'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
                'created_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
                'PRIMARY KEY (`id`)',
                'KEY `et_ophindnaextraction_dnatests_lmui_fk` (`last_modified_user_id`)',
                'KEY `et_ophindnaextraction_dnatests_cui_fk` (`created_user_id`)',
                'KEY `et_ophindnaextraction_dnatests_ev_fk` (`event_id`)',
                'CONSTRAINT `et_ophindnaextraction_dnatests_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)',
                'CONSTRAINT `et_ophindnaextraction_dnatests_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
                'CONSTRAINT `et_ophindnaextraction_dnatests_ev_fk` FOREIGN KEY (`event_id`) REFERENCES `event` (`id`)',
            ), 'ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin');

        $this->createTable('ophindnaextraction_dnatests_study', array(
                'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
                'name' => 'varchar(100) COLLATE utf8_bin NOT NULL',
                'display_order' => 'int(10) unsigned NOT NULL',
                'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
                'last_modified_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
                'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
                'created_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
                'PRIMARY KEY (`id`)',
                'KEY `ophindnaextraction_dnatests_study_lmui_fk` (`last_modified_user_id`)',
                'KEY `ophindnaextraction_dnatests_study_cui_fk` (`created_user_id`)',
                'CONSTRAINT `ophindnaextraction_dnatests_study_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)',
                'CONSTRAINT `ophindnaextraction_dnatests_study_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
            ), 'ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin');

        $this->createTable('ophindnaextraction_dnatests_investigator', array(
                'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
                'name' => 'varchar(100) COLLATE utf8_bin NOT NULL',
                'display_order' => 'int(10) unsigned NOT NULL',
                'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
                'last_modified_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
                'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
                'created_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
                'PRIMARY KEY (`id`)',
                'KEY `ophindnaextraction_dnatests_investigator_lmui_fk` (`last_modified_user_id`)',
                'KEY `ophindnaextraction_dnatests_investigator_cui_fk` (`created_user_id`)',
                'CONSTRAINT `ophindnaextraction_dnatests_investigator_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)',
                'CONSTRAINT `ophindnaextraction_dnatests_investigator_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
            ), 'ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin');

        $this->createTable('ophindnaextraction_dnatests_transaction', array(
                'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
                'element_id' => 'int(10) unsigned NOT NULL',
                'date' => 'date not null',
                'investigator_id' => 'int(10) unsigned NOT NULL',
                'study_id' => 'int(10) unsigned NOT NULL',
                'volume' => 'float NOT NULL',
                'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
                'last_modified_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
                'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
                'created_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
                'PRIMARY KEY (`id`)',
                'KEY `ophindnaextraction_dnatests_transaction_lmui_fk` (`last_modified_user_id`)',
                'KEY `ophindnaextraction_dnatests_transaction_cui_fk` (`created_user_id`)',
                'KEY `ophindnaextraction_dnatests_transaction_ele_fk` (`element_id`)',
                'KEY `ophindnaextraction_dnatests_transaction_sti_fk` (`study_id`)',
                'KEY `ophindnaextraction_dnatests_transaction_inv_fk` (`investigator_id`)',
                'CONSTRAINT `ophindnaextraction_dnatests_transaction_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)',
                'CONSTRAINT `ophindnaextraction_dnatests_transaction_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
                'CONSTRAINT `ophindnaextraction_dnatests_transaction_ele_fk` FOREIGN KEY (`element_id`) REFERENCES `et_ophindnaextraction_dnatests` (`id`)',
                'CONSTRAINT `ophindnaextraction_dnatests_transaction_sti_fk` FOREIGN KEY (`study_id`) REFERENCES `ophindnaextraction_dnatests_study` (`id`)',
                'CONSTRAINT `ophindnaextraction_dnatests_transaction_inv_fk` FOREIGN KEY (`investigator_id`) REFERENCES `ophindnaextraction_dnatests_investigator` (`id`)',
            ), 'ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin');

        $this->initialiseData(dirname(__FILE__));
    }

    public function down()
    {
        $this->dropTable('ophindnaextraction_dnatests_transaction');
        $this->dropTable('ophindnaextraction_dnatests_investigator');
        $this->dropTable('ophindnaextraction_dnatests_study');
        $this->dropTable('et_ophindnaextraction_dnatests');

        $event_type = $this->dbConnection->createCommand()->select('id')->from('event_type')->where('class_name=:class_name', array(':class_name' => 'OphInDnaextraction'))->queryRow();
        $this->delete('element_type', "event_type_id = {$event_type['id']} and class_name = 'Element_OphInDnaextraction_DnaTests'");

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
}
