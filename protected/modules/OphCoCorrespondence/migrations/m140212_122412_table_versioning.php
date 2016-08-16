<?php

class m140212_122412_table_versioning extends OEMigration
{
    public function up()
    {
        $this->versionExistingTable('ophcocorrespondence_firm_letter_macro');
        $this->versionExistingTable('ophcocorrespondence_firm_letter_string');
        $this->versionExistingTable('ophcocorrespondence_firm_site_secretary');
        $this->versionExistingTable('et_ophcocorrespondence_letter');
        $this->versionExistingTable('ophcocorrespondence_letter_macro');
        $this->versionExistingTable('ophcocorrespondence_letter_string');
        $this->versionExistingTable('ophcocorrespondence_letter_string_group');
        $this->versionExistingTable('ophcocorrespondence_subspecialty_letter_macro');
        $this->versionExistingTable('ophcocorrespondence_subspecialty_letter_string');
        $this->versionExistingTable('ophcocorrespondence_cbt_recipient');
        $this->versionExistingTable('ophcocorrespondence_letter_enclosure');

        $offset = 0;

        while (1) {
            $letters = $this->dbConnection->createCommand()
                ->select('et_ophcocorrespondence_letter_old.*, et_ophcocorrespondence_letter.event_id')
                ->from('et_ophcocorrespondence_letter_old')
                ->join('et_ophcocorrespondence_letter', 'et_ophcocorrespondence_letter.id = et_ophcocorrespondence_letter_old.letter_id')
                ->order('id asc')
                ->offset($offset)
                ->limit(1000)
                ->queryAll();

            if (empty($letters)) {
                break;
            }

            foreach ($letters as $old_letter) {
                $old_letter['id'] = $old_letter['letter_id'];
                unset($old_letter['letter_id']);

                $this->insert('et_ophcocorrespondence_letter_version', $old_letter);
            }

            $offset += 1000;
        }

        $this->dropTable('et_ophcocorrespondence_letter_old');
    }

    public function down()
    {
        $this->execute("
CREATE TABLE `et_ophcocorrespondence_letter_old` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`letter_id` int(10) unsigned NOT NULL,
	`use_nickname` tinyint(1) unsigned NOT NULL DEFAULT '0',
	`date` datetime NOT NULL,
	`address` varchar(1024) DEFAULT NULL,
	`introduction` varchar(255) DEFAULT NULL,
	`re` varchar(1024) DEFAULT NULL,
	`body` text,
	`footer` varchar(2048) DEFAULT NULL,
	`cc` text,
	`draft` tinyint(1) unsigned NOT NULL DEFAULT '0',
	`last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
	`created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
	`print` tinyint(1) unsigned NOT NULL DEFAULT '0',
	`locked` tinyint(1) unsigned NOT NULL DEFAULT '0',
	`site_id` int(10) unsigned NOT NULL,
	PRIMARY KEY (`id`),
	KEY `et_ophcocorrespondence_letter_old_letter_id_fk` (`letter_id`),
	KEY `et_ophcocorrespondence_letter_old_last_modified_user_id_fk` (`last_modified_user_id`),
	KEY `et_ophcocorrespondence_letter_old_created_user_id_fk` (`created_user_id`),
	KEY `et_ophcocorrespondence_letter_old_site_id_fk` (`site_id`),
	CONSTRAINT `et_ophcocorrespondence_letter_old_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
	CONSTRAINT `et_ophcocorrespondence_letter_old_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
	CONSTRAINT `et_ophcocorrespondence_letter_old_letter_id_fk` FOREIGN KEY (`letter_id`) REFERENCES `et_ophcocorrespondence_letter` (`id`),
	CONSTRAINT `et_ophcocorrespondence_letter_old_site_id_fk` FOREIGN KEY (`site_id`) REFERENCES `site` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

        foreach ($this->dbConnection->createCommand()->select('*')->from('et_ophcocorrespondence_letter_version')->order('id asc')->queryAll() as $versiond_letter) {
            $versiond_letter['letter_id'] = $versiond_letter['id'];
            unset($versiond_letter['id']);

            foreach (array('direct_line', 'fax', 'clinic_date', 'print_all') as $field) {
                unset($versiond_letter[$field]);
            }

            $this->insert('et_ophcocorrespondence_letter_old', $versiond_letter);
        }

        $this->dropTable('ophcocorrespondence_firm_letter_macro_version');
        $this->dropTable('ophcocorrespondence_firm_letter_string_version');
        $this->dropTable('ophcocorrespondence_firm_site_secretary_version');
        $this->dropTable('et_ophcocorrespondence_letter_version');
        $this->dropTable('ophcocorrespondence_letter_macro_version');
        $this->dropTable('ophcocorrespondence_letter_string_version');
        $this->dropTable('ophcocorrespondence_letter_string_group_version');
        $this->dropTable('ophcocorrespondence_subspecialty_letter_macro_version');
        $this->dropTable('ophcocorrespondence_subspecialty_letter_string_version');
        $this->dropTable('ophcocorrespondence_cbt_recipient_version');
        $this->dropTable('ophcocorrespondence_letter_enclosure_version');
    }
}
