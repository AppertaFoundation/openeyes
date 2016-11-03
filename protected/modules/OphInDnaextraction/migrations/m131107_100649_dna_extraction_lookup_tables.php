<?php

class m131107_100649_dna_extraction_lookup_tables extends OEMigration
{
    public function up()
    {
        $this->createTable('ophindnaextraction_dnaextraction_box', array(
                'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
                'value' => 'varchar(5) COLLATE utf8_bin NOT NULL',
                'display_order' => 'int(10) unsigned NOT NULL',
                'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
                'last_modified_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
                'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
                'created_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
                'PRIMARY KEY (`id`)',
                'KEY `ophindnaextraction_dnaextraction_box_lmui_fk` (`last_modified_user_id`)',
                'KEY `ophindnaextraction_dnaextraction_box_cui_fk` (`created_user_id`)',
                'CONSTRAINT `ophindnaextraction_dnaextraction_box_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)',
                'CONSTRAINT `ophindnaextraction_dnaextraction_box_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
            ), 'ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin');

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

        $this->renameColumn('et_ophindnaextraction_dnaextraction', 'box', 'box_id');
        $this->alterColumn('et_ophindnaextraction_dnaextraction', 'box_id', 'int(10) unsigned not null');
        $this->createIndex('et_ophindnaextraction_dnaextraction_box_id_fk', 'et_ophindnaextraction_dnaextraction', 'box_id');
        $this->addForeignKey('et_ophindnaextraction_dnaextraction_box_id_fk', 'et_ophindnaextraction_dnaextraction', 'box_id', 'ophindnaextraction_dnaextraction_box', 'id');

        $this->renameColumn('et_ophindnaextraction_dnaextraction', 'letter', 'letter_id');
        $this->alterColumn('et_ophindnaextraction_dnaextraction', 'letter_id', 'int(10) unsigned not null');
        $this->createIndex('et_ophindnaextraction_dnaextraction_letter_id_fk', 'et_ophindnaextraction_dnaextraction', 'letter_id');
        $this->addForeignKey('et_ophindnaextraction_dnaextraction_letter_id_fk', 'et_ophindnaextraction_dnaextraction', 'letter_id', 'ophindnaextraction_dnaextraction_letter', 'id');

        $this->renameColumn('et_ophindnaextraction_dnaextraction', 'number', 'number_id');
        $this->alterColumn('et_ophindnaextraction_dnaextraction', 'number_id', 'int(10) unsigned not null');
        $this->createIndex('et_ophindnaextraction_dnaextraction_number_id_fk', 'et_ophindnaextraction_dnaextraction', 'number_id');
        $this->addForeignKey('et_ophindnaextraction_dnaextraction_number_id_fk', 'et_ophindnaextraction_dnaextraction', 'number_id', 'ophindnaextraction_dnaextraction_number', 'id');

        $this->initialiseData(dirname(__FILE__));
    }

    public function down()
    {
        $this->dropForeignKey('et_ophindnaextraction_dnaextraction_number_id_fk', 'et_ophindnaextraction_dnaextraction');
        $this->dropIndex('et_ophindnaextraction_dnaextraction_number_id_fk', 'et_ophindnaextraction_dnaextraction');
        $this->alterColumn('et_ophindnaextraction_dnaextraction', 'number_id', 'varchar(5) collate utf8_bin not null');
        $this->renameColumn('et_ophindnaextraction_dnaextraction', 'number_id', 'number');

        $this->dropForeignKey('et_ophindnaextraction_dnaextraction_letter_id_fk', 'et_ophindnaextraction_dnaextraction');
        $this->dropIndex('et_ophindnaextraction_dnaextraction_letter_id_fk', 'et_ophindnaextraction_dnaextraction');
        $this->alterColumn('et_ophindnaextraction_dnaextraction', 'letter_id', 'varchar(5) collate utf8_bin not null');
        $this->renameColumn('et_ophindnaextraction_dnaextraction', 'letter_id', 'letter');

        $this->dropForeignKey('et_ophindnaextraction_dnaextraction_box_id_fk', 'et_ophindnaextraction_dnaextraction');
        $this->dropIndex('et_ophindnaextraction_dnaextraction_box_id_fk', 'et_ophindnaextraction_dnaextraction');
        $this->alterColumn('et_ophindnaextraction_dnaextraction', 'box_id', 'varchar(5) collate utf8_bin not null');
        $this->renameColumn('et_ophindnaextraction_dnaextraction', 'box_id', 'box');

        $this->dropTable('ophindnaextraction_dnaextraction_box');
        $this->dropTable('ophindnaextraction_dnaextraction_letter');
        $this->dropTable('ophindnaextraction_dnaextraction_number');
    }
}
