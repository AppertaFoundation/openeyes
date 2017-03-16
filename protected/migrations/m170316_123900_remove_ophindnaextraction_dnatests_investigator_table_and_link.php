<?php

class m170316_123900_remove_ophindnaextraction_dnatests_investigator_table_and_link extends CDbMigration
{
	public function up()
	{
	    $this->dropTable('ophindnaextraction_dnatests_investigator');
	    $this->dropTable('ophindnaextraction_dnatests_investigator_version');
	}

	public function down()
	{
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
        
        $this->versionExistingTable('ophindnaextraction_dnatests_investigator');
	}

}