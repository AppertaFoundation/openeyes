<?php

class m141222_115018_recipient_field_should_be_foreign_key extends OEMigration
{
    public function up()
    {
        foreach (array('ophcocorrespondence_letter_macro', 'ophcocorrespondence_firm_letter_macro', 'ophcocorrespondence_subspecialty_letter_macro') as $table) {
            if ($this->dbConnection->createCommand()->select('*')->from($table)->where('recipient_patient = 1 and recipient_doctor = 1')->queryRow()) {
                throw new Exception("$table has rows with both recipient_patient and recipient_doctor set.");
            }
            if ($this->dbConnection->createCommand()->select('*')->from($table.'_version')->where('recipient_patient = 1 and recipient_doctor = 1')->queryRow()) {
                throw new Exception("{$table}_version has rows with both recipient_patient and recipient_doctor set.");
            }
        }

        $this->createTable('ophcocorrespondence_letter_recipient', array(
                'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
                'name' => 'varchar(64) not null',
                'display_order' => 'tinyint(1) unsigned not null',
                'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
                'last_modified_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
                'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
                'created_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
                'PRIMARY KEY (`id`)',
                'KEY `ophcocorrespondence_letter_recipient_lmui_fk` (`last_modified_user_id`)',
                'KEY `ophcocorrespondence_letter_recipient_cui_fk` (`created_user_id`)',
                'CONSTRAINT `ophcocorrespondence_letter_recipient_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)',
                'CONSTRAINT `ophcocorrespondence_letter_recipient_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
            ), 'ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci');

        $this->versionExistingTable('ophcocorrespondence_letter_recipient');

        $this->insert('ophcocorrespondence_letter_recipient', array('id' => 1, 'name' => 'Patient', 'display_order' => 1));
        $this->insert('ophcocorrespondence_letter_recipient', array('id' => 2, 'name' => 'GP', 'display_order' => 2));

        foreach (array('ophcocorrespondence_letter_macro', 'ophcocorrespondence_firm_letter_macro', 'ophcocorrespondence_subspecialty_letter_macro') as $table) {
            $this->addColumn($table, 'recipient_id', 'int(10) unsigned null');
            $this->addColumn($table.'_version', 'recipient_id', 'int(10) unsigned null');
            $this->createIndex($table.'_rcp_fk', $table, 'recipient_id');
            $this->addForeignKey($table.'_rcp_fk', $table, 'recipient_id', 'ophcocorrespondence_letter_recipient', 'id');

            $this->dbConnection->createCommand("update $table set recipient_id = 1 where recipient_patient = 1")->query();
            $this->dbConnection->createCommand("update $table set recipient_id = 2 where recipient_doctor = 1")->query();

            $this->dbConnection->createCommand("update {$table}_version set recipient_id = 1 where recipient_patient = 1")->query();
            $this->dbConnection->createCommand("update {$table}_version set recipient_id = 2 where recipient_doctor = 1")->query();

            $this->dropColumn($table, 'recipient_patient');
            $this->dropColumn($table, 'recipient_doctor');
            $this->dropColumn($table.'_version', 'recipient_patient');
            $this->dropColumn($table.'_version', 'recipient_doctor');
        }
    }

    public function down()
    {
        foreach (array('ophcocorrespondence_letter_macro', 'ophcocorrespondence_firm_letter_macro', 'ophcocorrespondence_subspecialty_letter_macro') as $table) {
            $this->addColumn($table, 'recipient_patient', 'tinyint(1) unsigned not null');
            $this->addColumn($table, 'recipient_doctor', 'tinyint(1) unsigned not null');
            $this->addColumn($table.'_version', 'recipient_patient', 'tinyint(1) unsigned not null');
            $this->addColumn($table.'_version', 'recipient_doctor', 'tinyint(1) unsigned not null');

            $this->dbConnection->createCommand("update $table set recipient_patient = 1 where recipient_id = 1")->query();
            $this->dbConnection->createCommand("update $table set recipient_doctor = 1 where recipient_id = 2")->query();

            $this->dbConnection->createCommand("update {$table}_version set recipient_patient = 1 where recipient_id = 1")->query();
            $this->dbConnection->createCommand("update {$table}_version set recipient_doctor = 1 where recipient_id = 2")->query();

            $this->dropForeignKey($table.'_rcp_fk', $table);
            $this->dropIndex($table.'_rcp_fk', $table);
            $this->dropColumn($table, 'recipient_id');
            $this->dropColumn($table.'_version', 'recipient_id');
        }

        $this->dropTable('ophcocorrespondence_letter_recipient_version');
        $this->dropTable('ophcocorrespondence_letter_recipient');
    }
}
