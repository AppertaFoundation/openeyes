<?php

class m131010_074031_allergy_checking extends CDbMigration
{
    public function up()
    {
        $this->createTable('ophtrintravitinjection_antiseptic_allergy_assignment', array(
                'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
                'antisepticdrug_id' => 'int(10) unsigned NOT NULL',
                'allergy_id' => 'int(10) unsigned NOT NULL',
                'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
                'last_modified_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
                'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
                'created_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
                'PRIMARY KEY (`id`)',
                'KEY `ophtrintravitinjection_antiseptic_allergy_assign_lmui_fk` (`last_modified_user_id`)',
                'KEY `ophtrintravitinjection_antiseptic_allergy_assign_cui_fk` (`created_user_id`)',
                'KEY `ophtrintravitinjection_antiseptic_allergy_assign_iopi_fk` (`antisepticdrug_id`)',
                'KEY `ophtrintravitinjection_antiseptic_allergy_assign_allergyi_fk` (`allergy_id`)',
                'CONSTRAINT `ophtrintravitinjection_antiseptic_allergy_assign_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)',
                'CONSTRAINT `ophtrintravitinjection_antiseptic_allergy_assign_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
                'CONSTRAINT `ophtrintravitinjection_antiseptic_allergy_assign_iopi_fk` FOREIGN KEY (`antisepticdrug_id`) REFERENCES `ophtrintravitinjection_antiseptic_drug` (`id`)',
                'CONSTRAINT `ophtrintravitinjection_antiseptic_allergy_assign_allergyi_fk` FOREIGN KEY (`allergy_id`) REFERENCES `archive_allergy` (`id`)',
            ), 'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci');

        $this->createTable('ophtrintravitinjection_skindrug_allergy_assignment', array(
                'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
                'skindrug_id' => 'int(10) unsigned NOT NULL',
                'allergy_id' => 'int(10) unsigned NOT NULL',
                'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
                'last_modified_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
                'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
                'created_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
                'PRIMARY KEY (`id`)',
                'KEY `ophtrintravitinjection_skindrug_allergy_assign_lmui_fk` (`last_modified_user_id`)',
                'KEY `ophtrintravitinjection_skindrug_allergy_assign_cui_fk` (`created_user_id`)',
                'KEY `ophtrintravitinjection_skindrug_allergy_assign_iopi_fk` (`skindrug_id`)',
                'KEY `ophtrintravitinjection_skindrug_allergy_assign_allergyi_fk` (`allergy_id`)',
                'CONSTRAINT `ophtrintravitinjection_skindrug_allergy_assign_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)',
                'CONSTRAINT `ophtrintravitinjection_skindrug_allergy_assign_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
                'CONSTRAINT `ophtrintravitinjection_skindrug_allergy_assign_iopi_fk` FOREIGN KEY (`skindrug_id`) REFERENCES `ophtrintravitinjection_skin_drug` (`id`)',
                'CONSTRAINT `ophtrintravitinjection_skindrug_allergy_assign_allergyi_fk` FOREIGN KEY (`allergy_id`) REFERENCES `archive_allergy` (`id`)',
            ), 'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci');
    }

    public function down()
    {
        $this->dropTable('ophtrintravitinjection_skindrug_allergy_assignment');
        $this->dropTable('ophtrintravitinjection_antiseptic_allergy_assignment');
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
