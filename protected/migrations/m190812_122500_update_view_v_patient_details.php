<?php

class m190812_122500_update_view_v_patient_details extends CDbMigration
{
    public function safeUp()
    {
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
                YEAR(p.date_of_death) - YEAR(p.dob) - IF(DATE_FORMAT(p.date_of_death,'%m-%d') < DATE_FORMAT(p.dob,'%m-%d'), 1, 0),
                YEAR(CURRENT_DATE()) - YEAR(p.dob) - IF(DATE_FORMAT(CURRENT_DATE(),'%m-%d') < DATE_FORMAT(p.dob,'%m-%d'), 1, 0)
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
            `a`.`email` AS `email`,
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

    }

    public function safeDown()
    {
        $this->execute("CREATE OR REPLACE
        VIEW `v_patient_details` AS
        select
            `p`.`id` AS `patient_id`,
            `p`.`hos_num` AS `hos_num`,
            `p`.`nhs_num` AS `nhs_num`,
            `p`.`gender` AS `gender`,
            `p`.`dob` AS `dob`,
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
            `a`.`email` AS `email`,
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
        
    }
}
