<?php

class m200730_053753_move_email_column_to_contact extends OEMigration
{
    public function up()
    {
        $address_emails = $this->dbConnection->createCommand()
        ->select('email, contact_id')
        ->from('address')
        ->where('email IS NOT NULL')
        ->queryAll();

        $this->addOEColumn('contact', 'email', 'varchar(255) default null AFTER qualifications', true);

        foreach ($address_emails as $address_email) {
            $this->update(
                'contact',
                array(
                    'email' => $address_email['email'],
                ),
                'id = :contact_id',
                array(
                    ':contact_id' => $address_email['contact_id'],
                )
            );
        }

        $this->dropOEColumn('address', 'email', true);

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
    }

    public function down()
    {
        $contact_emails = $this->dbConnection->createCommand()
        ->select('email, id')
        ->from('contact')
        ->where('email IS NOT NULL')
        ->queryAll();

        $this->addOEColumn('address', 'email', 'varchar(255) default null AFTER country_id', true);


        foreach ($contact_emails as $contact_email) {
            $this->update(
                'address',
                array(
                    'email' => $contact_email['email'],
                ),
                'contact_id = :contact_id',
                array(
                    ':contact_id' => $contact_email['id'],
                )
            );
        }

        $this->dropOEColumn('contact', 'email', true);

        // revert view v_patient_details
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
