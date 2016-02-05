<?php

class m131127_113657_remove_visual_fields_element extends CDbMigration
{
    public function up()
    {
        $this->dropTable('et_ophciexamination_visual_fields');
    }

    public function down()
    {
        $this->execute("CREATE TABLE `et_ophciexamination_visual_fields` (
				`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
				`event_id` int(10) unsigned NOT NULL,
				`left_eyedraw` text,
				`right_eyedraw` text,
				`left_description` text,
				`right_description` text,
				`eye_id` int(10) unsigned NOT NULL DEFAULT '3',
				`last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
				`last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
				`created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
				`created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
				PRIMARY KEY (`id`),
				KEY `et_ophciexamination_visual_acuity_event_id_fk` (`event_id`),
				KEY `et_ophciexamination_visual_acuity_last_modified_user_id_fk` (`last_modified_user_id`),
				KEY `et_ophciexamination_visual_acuity_created_user_id_fk` (`created_user_id`),
				KEY `et_ophciexamination_visual_acuity_eye_id_fk` (`eye_id`),
				CONSTRAINT `et_ophciexamination_visual_acuity_event_id_fk` FOREIGN KEY (`event_id`) REFERENCES `event` (`id`),
				CONSTRAINT `et_ophciexamination_visual_acuity_last_modified_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
				CONSTRAINT `et_ophciexamination_visual_acuity_created_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
				CONSTRAINT `et_ophciexamination_visual_acuity_eye_id_fk` FOREIGN KEY (`eye_id`) REFERENCES `eye` (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");
    }
}
