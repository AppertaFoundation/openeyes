<?php

class m200909_142753_delete_hos_num_and_nhs_num_from_views extends OEMigration
{
	public function safeUp()
	{
        // update v_patient_episodes to include status
        $this->execute("CREATE OR REPLACE
        ALGORITHM = UNDEFINED VIEW `v_patient_episodes` AS
        select
            `p`.`id` AS `patient_id`,
            `ep`.`id` AS `episode_id`,
            `c`.`title` AS `patient_title`,
            `c`.`first_name` AS `patient_first_name`,
            `c`.`last_name` AS `patient_last_name`,
            `p`.`dob` AS `patient_dob`,
            `f`.`name` AS `firm_name`,
            `f`.`id` AS `firm_id`,
            `ep`.`eye_id` AS `eye_id`,
            (case
                `ep`.`eye_id`
                when 1 then 'L'
                when 2 then 'R'
                when 3 then 'B' end) AS `side`,
            `s`.`id` AS `subspecialty_id`,
            `s`.`name` AS `subspecialty`,
            `ep`.`start_date` AS `start_date`,
            `ep`.`end_date` AS `end_date`,
            `d`.`id` AS `disorder_id`,
            `d`.`term` AS `disorder_term`,
            `ep`.`disorder_date` AS `disorder_date`,
            `ep`.`episode_status_id` AS `episode_status_id`,
            `es`.`name` AS `episode_status`
        from
            (((((((`patient` `p`
        join `episode` `ep` on
            ((`ep`.`patient_id` = `p`.`id`)))
        join `episode_status` `es` on
            ((`es`.`id` = `ep`.`episode_status_id`)))
        join `contact` `c` on
            ((`c`.`id` = `p`.`contact_id`)))
        join `firm` `f` on
            ((`f`.`id` = `ep`.`firm_id`)))
        join `service_subspecialty_assignment` `ssa` on
            ((`ssa`.`id` = `f`.`service_subspecialty_assignment_id`)))
        join `subspecialty` `s` on
            ((`s`.`id` = `ssa`.`subspecialty_id`)))
        left join `disorder` `d` on
            ((`d`.`id` = `ep`.`disorder_id`)))
        where
            (`ep`.`deleted` = 0)
        order by
            `p`.`id`");

        // modify view v_patient_details
        $this->execute("CREATE OR REPLACE
        VIEW `v_patient_details` AS
        select
            `p`.`id` AS `patient_id`,
            `p`.`gender` AS `gender`,
            `p`.`dob` AS `dob`,
            IF (
                p.date_of_death IS NOT NULL,
                YEAR(p.date_of_death) - YEAR(p.dob) - if (DATE_FORMAT(p.date_of_death,'%m-%d') < DATE_FORMAT(p.dob,'%m-%d'), 1, 0),
                YEAR(CURRENT_DATE()) - YEAR(p.dob) - if (DATE_FORMAT(CURRENT_DATE(),'%m-%d') < DATE_FORMAT(p.dob,'%m-%d'), 1, 0)
               ) AS age,
            `c`.`title` AS `title`,
            `c`.`first_name` AS `first_name`,
            `c`.`last_name` AS `last_name`,
            concat(ucase(`c`.`last_name`),
            ', ',
            `c`.`first_name`,
            (case
                when (`c`.`title` > '') then concat(' (',
                `c`.`title`,
                ')')
                else ''
            end)) AS `full_name`,
            `e`.`code` AS `ethnic_group`,
            `c`.`email` AS `email`,
            `a`.`address1` AS `address1`,
            `a`.`address2` AS `address2`,
            `a`.`city` AS `city`,
            `a`.`postcode` AS `postcode`,
            `a`.`county` AS `county`,
            concat(`a`.`address1`,
            ', ',
            (case
                when (`a`.`address2` > '') then concat(`a`.`address2`,
                ', ')
                else ''
            end),
            (case
                when (`a`.`city` > '') then concat(`a`.`city`,
                ', ')
                else ''
            end),
            (case
                when (`a`.`county` > '') then concat(`a`.`county`,
                ', ')
                else ''
            end),
            (case
                when (`a`.`postcode` > '') then `a`.`postcode`
                else ''
            end)) AS `address_full`,
            `c`.`primary_phone` AS `primary_phone`,
            `p`.`is_deceased` AS `is_deceased`,
            `p`.`date_of_death` AS `date_of_death`
        from
            (((`patient` `p`
        left join `contact` `c` on
            ((`c`.`id` = `p`.`contact_id`)))
        left join `address` `a` on
            ((`c`.`id` = `a`.`contact_id`)))
        left join `ethnic_group` `e` on
            ((`p`.`ethnic_group_id` = `e`.`id`)))
        where
            (`p`.`deleted` = 0);");

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

        $this->execute("CREATE OR REPLACE VIEW `v_patient_messages` AS
    SELECT
        `m`.`id` AS `message_id`,
        `ev`.`patient_id` AS `patient_id`,
        `m`.`event_id` AS `event_id`,
        `m`.`message_type_id` AS `message_type_id`,
        `t`.`name` AS `message_type`,
        `m`.`urgent` AS `urgent`,
        `m`.`message_text` AS `message_text`,
        `m`.`marked_as_read` AS `marked_as_read`,
        `uu`.`first_name` AS `FROM_firstname`,
        `uu`.`last_name` AS `FROM_lastname`,
        `u`.`first_name` AS `TO_firstname`,
        `u`.`last_name` AS `TO_lastname`,
        `ev`.`patient_first_name` AS `patient_firstname`,
        `ev`.`patient_last_name` AS `patient_lastname`
    FROM
        ((((`et_ophcomessaging_message` `m`
        JOIN `user` `u` ON ((`u`.`id` = `m`.`for_the_attention_of_user_id`)))
        JOIN `v_patient_events` `ev` ON ((`ev`.`event_id` = `m`.`event_id`)))
        JOIN `user` `uu` ON ((`uu`.`id` = `m`.`last_modified_user_id`)))
        JOIN `ophcomessaging_message_message_type` `t` ON ((`t`.`id` = `m`.`message_type_id`)))
    WHERE
        (`m`.`deleted` = 0);");

    }

	public function safeDown()
	{
        // update v_patient_episodes to include status
        $this->execute("CREATE OR REPLACE
        ALGORITHM = UNDEFINED VIEW `v_patient_episodes` AS
        select
            `p`.`id` AS `patient_id`,
            `ep`.`id` AS `episode_id`,
            `p`.`hos_num` AS `hos_num`,
            `p`.`nhs_num` AS `nhs_num`,
            `c`.`title` AS `patient_title`,
            `c`.`first_name` AS `patient_first_name`,
            `c`.`last_name` AS `patient_last_name`,
            `p`.`dob` AS `patient_dob`,
            `f`.`name` AS `firm_name`,
            `f`.`id` AS `firm_id`,
            `ep`.`eye_id` AS `eye_id`,
            (case
                `ep`.`eye_id`
                when 1 then 'L'
                when 2 then 'R'
                when 3 then 'B' end) AS `side`,
            `s`.`id` AS `subspecialty_id`,
            `s`.`name` AS `subspecialty`,
            `ep`.`start_date` AS `start_date`,
            `ep`.`end_date` AS `end_date`,
            `d`.`id` AS `disorder_id`,
            `d`.`term` AS `disorder_term`,
            `ep`.`disorder_date` AS `disorder_date`,
            `ep`.`episode_status_id` AS `episode_status_id`,
            `es`.`name` AS `episode_status`
        from
            (((((((`patient` `p`
        join `episode` `ep` on
            ((`ep`.`patient_id` = `p`.`id`)))
        join `episode_status` `es` on
            ((`es`.`id` = `ep`.`episode_status_id`)))
        join `contact` `c` on
            ((`c`.`id` = `p`.`contact_id`)))
        join `firm` `f` on
            ((`f`.`id` = `ep`.`firm_id`)))
        join `service_subspecialty_assignment` `ssa` on
            ((`ssa`.`id` = `f`.`service_subspecialty_assignment_id`)))
        join `subspecialty` `s` on
            ((`s`.`id` = `ssa`.`subspecialty_id`)))
        left join `disorder` `d` on
            ((`d`.`id` = `ep`.`disorder_id`)))
        where
            (`ep`.`deleted` = 0)
        order by
            `p`.`id`");

        // modify view v_patient_details
        $this->execute("CREATE OR REPLACE
        VIEW `v_patient_details` AS
        select
            `p`.`id` AS `patient_id`,
            `p`.`hos_num` AS `hos_num`,
            `p`.`nhs_num` AS `nhs_num`,
            `p`.`gender` AS `gender`,
            `p`.`dob` AS `dob`,
            IF (
                p.date_of_death IS NOT NULL,
                YEAR(p.date_of_death) - YEAR(p.dob) - if (DATE_FORMAT(p.date_of_death,'%m-%d') < DATE_FORMAT(p.dob,'%m-%d'), 1, 0),
                YEAR(CURRENT_DATE()) - YEAR(p.dob) - if (DATE_FORMAT(CURRENT_DATE(),'%m-%d') < DATE_FORMAT(p.dob,'%m-%d'), 1, 0)
               ) AS age,
            `c`.`title` AS `title`,
            `c`.`first_name` AS `first_name`,
            `c`.`last_name` AS `last_name`,
            concat(ucase(`c`.`last_name`),
            ', ',
            `c`.`first_name`,
            (case
                when (`c`.`title` > '') then concat(' (',
                `c`.`title`,
                ')')
                else ''
            end)) AS `full_name`,
            `e`.`code` AS `ethnic_group`,
            `c`.`email` AS `email`,
            `a`.`address1` AS `address1`,
            `a`.`address2` AS `address2`,
            `a`.`city` AS `city`,
            `a`.`postcode` AS `postcode`,
            `a`.`county` AS `county`,
            concat(`a`.`address1`,
            ', ',
            (case
                when (`a`.`address2` > '') then concat(`a`.`address2`,
                ', ')
                else ''
            end),
            (case
                when (`a`.`city` > '') then concat(`a`.`city`,
                ', ')
                else ''
            end),
            (case
                when (`a`.`county` > '') then concat(`a`.`county`,
                ', ')
                else ''
            end),
            (case
                when (`a`.`postcode` > '') then `a`.`postcode`
                else ''
            end)) AS `address_full`,
            `c`.`primary_phone` AS `primary_phone`,
            `p`.`is_deceased` AS `is_deceased`,
            `p`.`date_of_death` AS `date_of_death`
        from
            (((`patient` `p`
        left join `contact` `c` on
            ((`c`.`id` = `p`.`contact_id`)))
        left join `address` `a` on
            ((`c`.`id` = `a`.`contact_id`)))
        left join `ethnic_group` `e` on
            ((`p`.`ethnic_group_id` = `e`.`id`)))
        where
            (`p`.`deleted` = 0);");

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
        $this->execute("CREATE OR REPLACE VIEW `v_patient_messages` AS
    SELECT
        `m`.`id` AS `message_id`,
        `ev`.`patient_id` AS `patient_id`,
        `m`.`event_id` AS `event_id`,
        `m`.`message_type_id` AS `message_type_id`,
        `t`.`name` AS `message_type`,
        `m`.`urgent` AS `urgent`,
        `m`.`message_text` AS `message_text`,
        `m`.`marked_as_read` AS `marked_as_read`,
        `uu`.`first_name` AS `FROM_firstname`,
        `uu`.`last_name` AS `FROM_lastname`,
        `u`.`first_name` AS `TO_firstname`,
        `u`.`last_name` AS `TO_lastname`,
        `ev`.`patient_first_name` AS `patient_firstname`,
        `ev`.`patient_last_name` AS `patient_lastname`,
        `ev`.`hos_num` AS `hos_num`,
        `ev`.`nhs_num` AS `nhs_num`
    FROM
        ((((`et_ophcomessaging_message` `m`
        JOIN `user` `u` ON ((`u`.`id` = `m`.`for_the_attention_of_user_id`)))
        JOIN `v_patient_events` `ev` ON ((`ev`.`event_id` = `m`.`event_id`)))
        JOIN `user` `uu` ON ((`uu`.`id` = `m`.`last_modified_user_id`)))
        JOIN `ophcomessaging_message_message_type` `t` ON ((`t`.`id` = `m`.`message_type_id`)))
    WHERE
        (`m`.`deleted` = 0);");

    }
}