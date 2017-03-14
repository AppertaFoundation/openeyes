<?php

class m170314_153701_create_internal_referral_settings_table extends OEMigration
{
	public function up()
	{
        $this->execute(
            "CREATE TABLE `ophcocorrespondence_internal_referral_settings` (
							 `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
							 `display_order` tinyint(3) unsigned DEFAULT '0',
							 `field_type_id` int(10) unsigned NOT NULL,
							 `key` varchar(64) NOT NULL,
							 `name` varchar(64) NOT NULL,
							 `data` varchar(4096) NOT NULL,
							 `default_value` varchar(64) NOT NULL,
							 `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
							 `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
							 PRIMARY KEY (`id`),
							 KEY `ophcocorrespondence_int_ref_set_type_id_fk` (`field_type_id`),
							 KEY `ophcocorrespondence_int_ref_set_last_modified_user_id_fk` (`last_modified_user_id`),
							 KEY `ophcocorrespondence_int_ref_set_created_user_id_fk` (`created_user_id`),
							 CONSTRAINT `ophcocorrespondence_int_ref_set_field_type_id_fk` FOREIGN KEY (`field_type_id`) REFERENCES `setting_field_type` (`id`),
							 CONSTRAINT `ophcocorrespondence_int_ref_set_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
							 CONSTRAINT `ophcocorrespondence_int_ref_set_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)
							)
							ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci");

        $this->execute(
            "CREATE TABLE `ophcocorrespondence_internal_referral_settings_version` (
							 `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
							 `display_order` tinyint(3) unsigned DEFAULT '0',
							 `field_type_id` int(10) unsigned NOT NULL,
							 `key` varchar(64) NOT NULL,
							 `name` varchar(64) NOT NULL,
							 `data` varchar(4096) NOT NULL,
							 `default_value` varchar(64) NOT NULL,
							 `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
							 `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
							 PRIMARY KEY (`id`),
							 KEY `ophcocorrespondence_int_ref_set_type_id_fk` (`field_type_id`),
							 KEY `ophcocorrespondence_int_ref_set_last_modified_user_id_fk` (`last_modified_user_id`),
							 KEY `ophcocorrespondence_int_ref_set_created_user_id_fk` (`created_user_id`)
							)
							ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci");

        $this->insert('ophcocorrespondence_internal_referral_settings',array(
            'field_type_id' => 3,
            'key' => 'is_active',
            'name' => 'Enable Internal referral',
            'data' => 'a:2:{s:2:"on";s:2:"On";s:3:"off";s:3:"Off";}',
            'default_value' => 1
        ));

        $this->insert('ophcocorrespondence_internal_referral_settings',array(
            'field_type_id' => 4,
            'key' => 'booking_address',
            'name' => 'Booking Address'
        ));



	}

	public function down()
	{
		$this->dropOETable('ophcocorrespondence_internal_referral_settings');
		$this->dropOETable('ophcocorrespondence_internal_referral_settings_version');
	}
}