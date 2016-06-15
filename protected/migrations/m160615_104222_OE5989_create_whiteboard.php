<?php

class m160615_104222_OE5989_create_whiteboard extends OEMigration
{
	public function up()
	{
		$this->execute("CREATE TABLE `ophtroperationbooking_whiteboard` (
			`id` int unsigned NOT NULL AUTO_INCREMENT,
			`event_id` int unsigned NOT NULL,
			`patientName` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
			`dob` DATE,
			`hos_num` int unsigned NOT NULL,
			`procedure` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
			`last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
			`created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			`created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
			PRIMARY KEY (`id`),
			UNIQUE KEY `event_id` (`event_id`)
		) ENGINE=InnoDB");
	}

	public function down()
	{
		$this->dropTable('ophtroperationbooking_whiteboard');
	}
}