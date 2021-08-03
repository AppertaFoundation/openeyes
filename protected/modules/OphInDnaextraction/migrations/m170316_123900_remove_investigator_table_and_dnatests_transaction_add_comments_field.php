<?php

class m170316_123900_remove_investigator_table_and_dnatests_transaction_add_comments_field extends CDbMigration
{
    public function up()
    {
        $this->dropForeignKey('ophindnaextraction_dnatests_transaction_inv_fk', 'ophindnaextraction_dnatests_transaction');

        $this->dropColumn('ophindnaextraction_dnatests_transaction', 'investigator_id');
            $this->dropColumn('ophindnaextraction_dnatests_transaction_version', 'investigator_id');

        $this->dropTable('ophindnaextraction_dnatests_investigator');
        $this->dropTable('ophindnaextraction_dnatests_investigator_version');

            $this->addColumn('ophindnaextraction_dnatests_transaction', 'comments', 'varchar(255)');
            $this->addColumn('ophindnaextraction_dnatests_transaction_version', 'comments', 'varchar(255)');

        $this->dropForeignKey('ophindnaextraction_dnatests_transaction_sti_fk', 'ophindnaextraction_dnatests_transaction');

        $this->alterColumn('ophindnaextraction_dnatests_transaction', 'study_id', 'INT(11) NOT NULL');
        $this->alterColumn('ophindnaextraction_dnatests_transaction_version', 'study_id', 'INT(11) NOT NULL');

        $this->addForeignKey(
            'ophindnaextraction_dnatests_transaction_sti_fk',
            'ophindnaextraction_dnatests_transaction',
            'study_id',
            'genetics_study',
            'id',
            'RESTRICT',
            'RESTRICT'
        );
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

        $this->addColumn('ophindnaextraction_dnatests_transaction', 'investigator_id', 'int(10) unsigned NOT NULL');
        $this->dropColumn('ophindnaextraction_dnatests_transaction', 'comments');

        $this->dropForeignKey('ophindnaextraction_dnatests_transaction_sti_fk', 'ophindnaextraction_dnatests_transaction');
        $this->alterColumn('ophindnaextraction_dnatests_transaction', 'study_id', 'INT(10) UNSIGNED NOT NULL');

        $this->alterColumn('ophindnaextraction_dnatests_transaction', 'study_id', 'INT(10) UNSIGNED NOT NULL');
        $this->addForeignKey(
            'ophindnaextraction_dnatests_transaction_sti_fk',
            'ophindnaextraction_dnatests_transaction',
            'study_id',
            'ophindnaextraction_dnatests_study',
            'id',
            'RESTRICT',
            'RESTRICT'
        );

        $this->addColumn('ophindnaextraction_dnatests_transaction_version', 'investigator_id', 'int(10) unsigned NOT NULL');
        $this->addForeignKey(
            'ophindnaextraction_dnatests_transaction_inv_fk',
            'ophindnaextraction_dnatests_transaction',
            'investigator_id',
            'ophindnaextraction_dnatests_investigator',
            'id',
            'RESTRICT',
            'RESTRICT'
        );
        $this->dropColumn('ophindnaextraction_dnatests_transaction_version', 'comments');
    }
}
