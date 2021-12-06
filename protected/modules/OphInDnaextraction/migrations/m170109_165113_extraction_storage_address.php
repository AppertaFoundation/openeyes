<?php

class m170109_165113_extraction_storage_address extends CDbMigration
{
    public function up()
    {

        $this->createTable('ophindnaextraction_storage_address', array(
            'id'     => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
            'box_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
            'letter' => 'varchar(5) COLLATE utf8_bin NOT NULL',
            'number' => 'varchar(5) COLLATE utf8_bin NOT NULL',
            'display_order' => 'int(10) unsigned NOT NULL',
            'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
            'last_modified_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
            'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
            'created_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
            'PRIMARY KEY (`id`)',
            'KEY `ophindnaextraction_storage_address_lmui_fk` (`last_modified_user_id`)',
            'KEY `ophindnaextraction_storage_address_cui_fk` (`created_user_id`)',
            'CONSTRAINT `ophindnaextraction_storage_address_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)',
            'CONSTRAINT `ophindnaextraction_storage_address_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
        ), 'ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin');

        $this->addColumn('ophindnaextraction_dnaextraction_box', 'maxletter', 'varchar(2) COLLATE utf8_bin NULL');
        $this->addColumn('ophindnaextraction_dnaextraction_box', 'maxnumber', 'varchar(5) COLLATE utf8_bin NULL');

        $this->addColumn('ophindnaextraction_dnaextraction_box_version', 'maxletter', 'varchar(2) COLLATE utf8_bin NULL');
        $this->addColumn('ophindnaextraction_dnaextraction_box_version', 'maxnumber', 'varchar(5) COLLATE utf8_bin NULL');
    }

    public function down()
    {
        $this->dropTable('ophindnaextraction_storage_address');

        $this->dropColumn('ophindnaextraction_dnaextraction_box', 'maxletter');
        $this->dropColumn('ophindnaextraction_dnaextraction_box', 'maxnumber');

        $this->dropColumn('ophindnaextraction_dnaextraction_box_version', 'maxletter');
        $this->dropColumn('ophindnaextraction_dnaextraction_box_version', 'maxnumber');
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
