<?php

class m190303_185200_add_report_views extends CDbMigration
{
	public function safeUp()
	{
	    $this->execute("CREATE OR REPLACE VIEW `v_patient_events` AS
    SELECT
        `p`.`id` AS `patient_id`,
        `ev`.`id` AS `event_id`,
        `ep`.`id` AS `episode_id`,
        `ev`.`event_type_id` AS `event_type_id`,
        `et`.`name` AS `event_name`,
        `et`.`class_name` AS `event_class`,
        `ev`.`event_date` AS `event_date`,
        `ev`.`created_date` AS `event_created_date`,
        `ev`.`last_modified_date` AS `event_last_modified_date`,
        `p`.`hos_num` AS `hos_num`,
        `p`.`nhs_num` AS `nhs_num`,
        `c`.`title` AS `patient_title`,
        `c`.`first_name` AS `patient_first_name`,
        `c`.`last_name` AS `patient_last_name`,
        `p`.`dob` AS `patient_dob`,
        `f`.`name` AS `firm_name`,
        `f`.`id` AS `firm_id`,
        `s`.`id` AS `subspecialty_id`,
        `s`.`name` AS `subspecialty`
    FROM
        (((((((`patient` `p`
        JOIN `episode` `ep` ON ((`ep`.`patient_id` = `p`.`id`)))
        JOIN `event` `ev` ON ((`ev`.`episode_id` = `ep`.`id`)))
        JOIN `event_type` `et` ON ((`et`.`id` = `ev`.`event_type_id`)))
        JOIN `contact` `c` ON ((`c`.`id` = `p`.`contact_id`)))
        JOIN `firm` `f` ON ((`f`.`id` = `ep`.`firm_id`)))
        JOIN `service_subspecialty_assignment` `ssa` ON ((`ssa`.`id` = `f`.`service_subspecialty_assignment_id`)))
        JOIN `subspecialty` `s` ON ((`s`.`id` = `ssa`.`subspecialty_id`)))
    WHERE
        ((`ev`.`deleted` = 0)
            AND (`ep`.`deleted` = 0));");


	    $this->execute("CREATE OR REPLACE VIEW `v_patient_details` AS
    SELECT
        `p`.`id` AS `patient_id`,
        `p`.`hos_num` AS `hos_num`,
        `p`.`nhs_num` AS `nhs_num`,
        `p`.`gender` AS `gender`,
        `p`.`dob` AS `dob`,
        `c`.`title` AS `title`,
        `c`.`first_name` AS `first_name`,
        `c`.`last_name` AS `last_name`,
        CONCAT(UCASE(`c`.`last_name`),
                ', ',
                `c`.`first_name`,
                (CASE
                    WHEN (`c`.`title` > '') THEN CONCAT(' (', `c`.`title`, ')')
                    ELSE ''
                END)) AS `full_name`,
        `e`.`code` AS `ethnic_group`,
        `a`.`email` AS `email`,
        `a`.`address1` AS `address1`,
        `a`.`address2` AS `address2`,
        `a`.`city` AS `city`,
        `a`.`postcode` AS `postcode`,
        `a`.`county` AS `county`,
        CONCAT(`a`.`address1`,
                ', ',
                (CASE
                    WHEN (`a`.`address2` > '') THEN CONCAT(`a`.`address2`, ', ')
                    ELSE ''
                END),
                (CASE
                    WHEN (`a`.`city` > '') THEN CONCAT(`a`.`city`, ', ')
                    ELSE ''
                END),
                (CASE
                    WHEN (`a`.`county` > '') THEN CONCAT(`a`.`county`, ', ')
                    ELSE ''
                END),
                (CASE
                    WHEN (`a`.`postcode` > '') THEN `a`.`postcode`
                    ELSE ''
                END)) AS `address_full`,
        `c`.`primary_phone` AS `primary_phone`,
        `p`.`is_deceased` AS `is_deceased`,
        `p`.`date_of_death` AS `date_of_death`
    FROM
        (((`patient` `p`
        LEFT JOIN `contact` `c` ON ((`c`.`id` = `p`.`contact_id`)))
        LEFT JOIN `address` `a` ON ((`c`.`id` = `a`.`contact_id`)))
        LEFT JOIN `ethnic_group` `e` ON ((`p`.`ethnic_group_id` = `e`.`id`)))
    WHERE
        (`p`.`deleted` = 0);");



			$this->execute("CREATE OR REPLACE VIEW `v_patient_gp_details` AS
    SELECT
        `p`.`id` AS `patient_id`,
        `gp`.`nat_id` AS `gp_code`,
        `gpc`.`title` AS `gp_title`,
        `gpc`.`first_name` AS `gp_first_name`,
        `gpc`.`last_name` AS `gp_last_name`,
        `prac`.`code` AS `practice_code`,
        `praccon`.`title` AS `practice_lead_title`,
        `praccon`.`first_name` AS `practice_lead_first_name`,
        `praccon`.`last_name` AS `practice_lead_last_name`,
        `a`.`address1` AS `practice_address1`,
        `a`.`address2` AS `practice_address2`,
        `a`.`city` AS `practice_city`,
        `a`.`postcode` AS `practice_postcode`,
        `a`.`county` AS `practice_county`,
        CONCAT(`a`.`address1`,
                ', ',
                (CASE
                    WHEN (`a`.`address2` > '') THEN CONCAT(`a`.`address2`, ', ')
                    ELSE ''
                END),
                (CASE
                    WHEN (`a`.`city` > '') THEN CONCAT(`a`.`city`, ', ')
                    ELSE ''
                END),
                (CASE
                    WHEN (`a`.`county` > '') THEN CONCAT(`a`.`county`, ', ')
                    ELSE ''
                END),
                (CASE
                    WHEN (`a`.`postcode` > '') THEN `a`.`postcode`
                    ELSE ''
                END)) AS `practice_address_full`,
        `praccon`.`primary_phone` AS `practice_primary_phone`,
        `prac`.`phone` AS `practice_registered_phone`
    FROM
        (((((`patient` `p`
        LEFT JOIN `gp` ON ((`gp`.`id` = `p`.`gp_id`)))
        LEFT JOIN `contact` `gpc` ON ((`gpc`.`id` = `gp`.`contact_id`)))
        LEFT JOIN `practice` `prac` ON ((`prac`.`id` = `p`.`practice_id`)))
        LEFT JOIN `contact` `praccon` ON ((`praccon`.`id` = `prac`.`contact_id`)))
        LEFT JOIN `address` `a` ON ((`a`.`contact_id` = `praccon`.`id`)))
    WHERE
        (`p`.`deleted` = 0);");

	}

	public function safeDown()
	{
		$this->execute("DROP VIEW v_patient_events");
		$this->execute("DROP VIEW v_patient_details");
		$this->execute("DROP VIEW v_patient_gp_details");
	}
}
