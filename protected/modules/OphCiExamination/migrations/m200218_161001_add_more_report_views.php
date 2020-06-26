<?php

class m200218_161001_add_more_report_views extends CDbMigration
{
    public function safeUp()
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


        // v_patient_IOP
        $this->execute("CREATE OR REPLACE
        ALGORITHM = UNDEFINED VIEW `v_patient_iop` AS
        select
            `pe`.`patient_id` AS `patient_id`,
            `eoi`.`event_id` AS `event_id`,
            `pe`.`event_date` AS `event_date`,
            `iov`.`reading_time` AS `reading_time`,
            (case
                `iov`.`eye_id`
                when 2 then 0
                when 1 then 1 end) AS `side`,
            (case
                `iov`.`eye_id`
                when 2 then 'R'
                when 1 then 'L' end) AS `eye`,
            `ior`.`value` AS `value`,
            `ins`.`name` AS `Instrument`,
            (case
                `iov`.`eye_id`
                when 2 then `eoi`.`right_comments`
                when 1 then `eoi`.`left_comments` end) AS `Comment`,
            `pe`.`event_name` AS `event_name`
        from
            ((((`et_ophciexamination_intraocularpressure` `eoi`
        left join `ophciexamination_intraocularpressure_value` `iov` on
            ((`iov`.`element_id` = `eoi`.`id`)))
        left join `ophciexamination_intraocularpressure_reading` `ior` on
            ((`iov`.`reading_id` = `ior`.`id`)))
        left join `ophciexamination_instrument` `ins` on
            ((`iov`.`instrument_id` = `ins`.`id`)))
        join `v_patient_events` `pe` on
            ((`eoi`.`event_id` = `pe`.`event_id`)))
        where
            (`ior`.`id` is not null)
        union
        select
            `pe`.`patient_id` AS `patient_id`,
            `eph`.`event_id` AS `event_id`,
            `pe`.`event_date` AS `event_date`,
            `ior`.`measurement_timestamp` AS `reading_time`,
            `ior`.`side` AS `side`,
            (case
                `ior`.`side`
                when 0 then 'R'
                when 1 then 'L' end) AS `eye`,
            `ior`.`value` AS `value`,
            (case
                `ior`.`side`
                when 0 then `pins_r`.`name`
                when 1 then `pins_l`.`name` end) AS `Instrument`,
            (case
                `ior`.`side`
                when 0 then `eph`.`right_comments`
                when 1 then `eph`.`left_comments` end) AS `Comment`,
            `pe`.`event_name` AS `event_name`
        from
            ((((`et_ophciphasing_intraocularpressure` `eph`
        left join `ophciphasing_reading` `ior` on
            ((`ior`.`element_id` = `eph`.`id`)))
        left join `ophciphasing_instrument` `pins_l` on
            ((`pins_l`.`id` = `eph`.`left_instrument_id`)))
        left join `ophciphasing_instrument` `pins_r` on
            ((`pins_r`.`id` = `eph`.`right_instrument_id`)))
        join `v_patient_events` `pe` on
            ((`eph`.`event_id` = `pe`.`event_id`)))
        where
            (`ior`.`id` is not null)
        order by 1, 3, 4");

        // v_patient_cct
        $this->execute("CREATE OR REPLACE
        ALGORITHM = UNDEFINED VIEW `v_patient_cct` AS
        select
            `e`.`patient_id` AS `patient_id`,
            `cct`.`event_id` AS `event_id`,
            `e`.`event_date` AS `event_date`,
            `cct`.`left_value` AS `value`,
            `m`.`name` AS `method`,
            1 AS `side`,
            'L' AS `eye`
        from
            ((`et_ophciexamination_anteriorsegment_cct` `cct`
        join `v_patient_events` `e` on
            ((`cct`.`event_id` = `e`.`event_id`)))
        join `ophciexamination_anteriorsegment_cct_method` `m` on
            ((`cct`.`left_method_id` = `m`.`id`)))
        union
        select
            `e`.`patient_id` AS `patient_id`,
            `cct`.`event_id` AS `event_id`,
            `e`.`event_date` AS `event_date`,
            `cct`.`right_value` AS `value`,
            `m`.`name` AS `method`,
            0 AS `side`,
            'R' AS `eye`
        from
            ((`et_ophciexamination_anteriorsegment_cct` `cct`
        join `v_patient_events` `e` on
            ((`cct`.`event_id` = `e`.`event_id`)))
        join `ophciexamination_anteriorsegment_cct_method` `m` on
            ((`cct`.`right_method_id` = `m`.`id`)))
        order by
            1,
            3");

        // v_patient_max_cct - selects the highest CCT result for each patient

        $this->execute("CREATE OR REPLACE
            ALGORITHM = UNDEFINED VIEW `v_patient_max_cct` AS
            select
                `cct`.`patient_id` AS `patient_id`,
                `cct`.`event_id` AS `event_id`,
                `cct`.`event_date` AS `event_date`,
                `cct`.`value` AS `value`,
                cct.`method`,
                `cct`.`side` AS `side`,
                `cct`.`eye` AS `eye`
            from
                `v_patient_cct` `cct`
            where
                (`cct`.`value` = (
                select
                    max(`cctmax`.`value`)
                from
                    `v_patient_cct` `cctmax`
                where
                    ((`cctmax`.`patient_id` = `cct`.`patient_id`)
                    and (`cctmax`.`side` = `cct`.`side`))))
            group by
                `cct`.`patient_id`,
                `cct`.`side`");


        // update v_patient_va to add comment column

        $this->execute("CREATE OR REPLACE
        ALGORITHM = UNDEFINED VIEW `v_patient_va` AS
        select
            `pe`.`patient_id` AS `patient_id`,
            `eva`.`event_id` AS `event_id`,
            `pe`.`event_date` AS `reading_date`,
            `r`.`side` AS `side`,
            `r`.`value` AS `base_value`,
            `eva`.`unit_id` AS `unit_id`,
            `m`.`id` AS `method_id`,
            (case
                `r`.`side`
                when 0 then 'R'
                when 1 then 'L' end) AS `eye`,
            `u`.`name` AS `unit_name`,
            `v`.`value` AS `value`,
            `m`.`name` AS `method`,
            (case r.side
                WHEN 0 then eva.right_notes
                WHEN 1 then eva.left_notes END) AS 'Comment'
        from
            (((((`et_ophciexamination_visualacuity` `eva`
        join `ophciexamination_visualacuity_reading` `r` on
            ((`r`.`element_id` = `eva`.`id`)))
        join `ophciexamination_visual_acuity_unit_value` `v` on
            ((`v`.`base_value` = `r`.`value`)))
        join `ophciexamination_visual_acuity_unit` `u` on
            ((`u`.`id` = `v`.`unit_id`)))
        join `v_patient_events` `pe` on
            ((`pe`.`event_id` = `eva`.`event_id`)))
        join `ophciexamination_visualacuity_method` `m` on
            ((`r`.`method_id` = `m`.`id`)))
        where
            (`v`.`unit_id` = `eva`.`unit_id`)
        order by
            `pe`.`patient_id`,
            `pe`.`event_date`,
            `pe`.`event_created_date`,
            `pe`.`event_last_modified_date`");

        // Update v_patient_va_converted to add comments field

        $this->execute("CREATE OR REPLACE
        ALGORITHM = UNDEFINED VIEW `v_patient_va_converted` AS
        select
            `pe`.`patient_id` AS `patient_id`,
            `eva`.`event_id` AS `event_id`,
            `pe`.`event_date` AS `reading_date`,
            `r`.`side` AS `side`,
            `r`.`value` AS `base_value`,
            `eva`.`unit_id` AS `original_unit_id`,
            `m`.`id` AS `method_id`,
            (case
                `r`.`side`
                when 0 then 'R'
                when 1 then 'L' end) AS `eye`,
            `u`.`name` AS `original_unit_name`,
            `v`.`value` AS `original_value`,
            `m`.`name` AS `method`,
            (
            select
                `sub`.`value`
            from
                `ophciexamination_visual_acuity_unit_value` `sub`
            where
                (`sub`.`unit_id` = (
                select
                    `ophciexamination_visual_acuity_unit`.`id`
                from
                    `ophciexamination_visual_acuity_unit`
                where
                    (lcase(`ophciexamination_visual_acuity_unit`.`name`) = lcase('Snellen Metre'))))
            order by
                abs((cast(`sub`.`base_value` as signed) - cast(`v`.`base_value` as signed)))
            limit 1) AS `snellen_value`,
            (
            select
                `sub`.`value`
            from
                `ophciexamination_visual_acuity_unit_value` `sub`
            where
                (`sub`.`unit_id` = (
                select
                    `ophciexamination_visual_acuity_unit`.`id`
                from
                    `ophciexamination_visual_acuity_unit`
                where
                    (lcase(`ophciexamination_visual_acuity_unit`.`name`) = lcase('ETDRS Letters'))))
            order by
                abs((cast(`sub`.`base_value` as signed) - cast(`v`.`base_value` as signed)))
            limit 1) AS `ETDRS_value`,
            (
            select
                `sub`.`value`
            from
                `ophciexamination_visual_acuity_unit_value` `sub`
            where
                (`sub`.`unit_id` = (
                select
                    `ophciexamination_visual_acuity_unit`.`id`
                from
                    `ophciexamination_visual_acuity_unit`
                where
                    (lcase(`ophciexamination_visual_acuity_unit`.`name`) = lcase('LogMAR single-letter'))))
            order by
                abs((cast(`sub`.`base_value` as signed) - cast(`v`.`base_value` as signed)))
            limit 1) AS `LogMAR_value`
            , (case r.side
                WHEN 0 then eva.right_notes
                WHEN 1 then eva.left_notes END) AS 'Comment'
        from
            (((((`et_ophciexamination_visualacuity` `eva`
        join `ophciexamination_visualacuity_reading` `r` on
            ((`r`.`element_id` = `eva`.`id`)))
        join `ophciexamination_visual_acuity_unit_value` `v` on
            ((`v`.`base_value` = `r`.`value`)))
        join `ophciexamination_visual_acuity_unit` `u` on
            ((`u`.`id` = `v`.`unit_id`)))
        join `v_patient_events` `pe` on
            ((`pe`.`event_id` = `eva`.`event_id`)))
        join `ophciexamination_visualacuity_method` `m` on
            ((`r`.`method_id` = `m`.`id`)))
        where
            (`v`.`unit_id` = `eva`.`unit_id`)
        order by
            `pe`.`patient_id`,
            `pe`.`event_date`,
            `pe`.`event_created_date`,
            `pe`.`event_last_modified_date`");

        // v_anon_patient_details
        // Can be used in place of v_patient_details to provide anonymisation
        $this->execute("CREATE OR REPLACE
        ALGORITHM = UNDEFINED VIEW `v_anon_patient_details` AS
        select
            `p`.`id` AS `patient_id`,
            `p`.`gender` AS `gender`,
            year(`p`.`dob`) AS `dob`,
            if((`p`.`date_of_death` is not null),
            ((year(`p`.`date_of_death`) - year(`p`.`dob`)) - if((date_format(`p`.`date_of_death`,
            '%m-%d') < date_format(`p`.`dob`,
            '%m-%d')),
            1,
            0)),
            ((year(curdate()) - year(`p`.`dob`)) - if((date_format(curdate(),
            '%m-%d') < date_format(`p`.`dob`,
            '%m-%d')),
            1,
            0))) AS `age`,
            (case
                `p`.`gender`
                when 'M' then 'Mr'
                else 'Mrs' end) AS `title`,
            concat(left(`c`.`first_name`,
            1),
            '*****') AS `first_name`,
            concat('****',
            right(`c`.`last_name`,
            2)) AS `last_name`,
            concat(concat('****',
            right(`c`.`last_name`,
            2),
            ', '),
            left(`c`.`first_name`,
            1),
            '***** (',
            (case
                `p`.`gender`
                when 'M' then 'Mr'
                else 'Mrs' end),
            ')') AS `full_name`,
            `e`.`code` AS `ethnic_group`,
            'blank@anonymous.anon' AS `email`,
            '123 A street' AS `address1`,
            '' AS `address2`,
            'A Town' AS `city`,
            left(`a`.`postcode`,
            (length(`a`.`postcode`) - 2)) AS `postcode`,
            `a`.`county` AS `county`,
            concat('123 A street, A Town, ',
            (case
                when (`a`.`county` > '') then concat(`a`.`county`,
                ', ')
                else '' end),
            (case
                when (`a`.`postcode` > '') then left(`a`.`postcode`,
                (length(`a`.`postcode`) - 2))
                else '' end)) AS `address_full`,
            '01234567890' AS `primary_phone`,
            `p`.`is_deceased` AS `is_deceased`,
            year(`p`.`date_of_death`) AS `date_of_death`
        from
            (((`patient` `p`
        left join `contact` `c` on
            ((`c`.`id` = `p`.`contact_id`)))
        left join `address` `a` on
            ((`c`.`id` = `a`.`contact_id`)))
        left join `ethnic_group` `e` on
            ((`p`.`ethnic_group_id` = `e`.`id`)))
        where
            (`p`.`deleted` = 0)");

        // v_patient_iop_max
        // Selects the highest ever IOP reading per patient, per eye
        $this->execute("CREATE OR REPLACE
        ALGORITHM = UNDEFINED VIEW `v_patient_max_iop` AS
        select
            `iop`.`patient_id` AS `patient_id`,
            `iop`.`event_id` AS `event_id`,
            `iop`.`event_date` AS `event_date`,
            `iop`.`reading_time` AS `reading_time`,
            `iop`.`value` AS `value`,
            `iop`.`Instrument` AS `instrument`,
            `iop`.`side` AS `side`,
            `iop`.`eye` AS `eye`,
            `iop`.`Comment` AS `comment`
        from
            `v_patient_iop` `iop`
        where
            (`iop`.`value` = (
            select
                max(`iopmax`.`value`)
            from
                `v_patient_iop` `iopmax`
            where
                ((`iopmax`.`patient_id` = `iop`.`patient_id`)
                and (`iopmax`.`side` = `iop`.`side`)
                and (`iop`.`event_name` = 'Examination'))))
        group by
            `iop`.`patient_id`,
            `iop`.`side`");

        // v_patient_iop_max_active_episodes
        // Similar to v_patient_max_iop, except excludes discharged patients
        $this->execute("CREATE OR REPLACE
        ALGORITHM = UNDEFINED VIEW `v_patient_max_iop_active_episodes` AS
        select
            `iop`.`patient_id` AS `patient_id`,
            `iop`.`event_id` AS `event_id`,
            `iop`.`event_date` AS `event_date`,
            `iop`.`reading_time` AS `reading_time`,
            `iop`.`value` AS `value`,
            `iop`.`Instrument` AS `instrument`,
            `iop`.`side` AS `side`,
            `iop`.`eye` AS `eye`,
            `iop`.`Comment` AS `comment`,
            `vep`.`subspecialty` AS `subspecialty`,
            `vep`.`episode_status` AS `episode_status`
        from
            ((`v_patient_iop` `iop`
        join `v_patient_events` `vev` on
            ((`vev`.`event_id` = `iop`.`event_id`)))
        join `v_patient_episodes` `vep` on
            ((`vep`.`episode_id` = `vev`.`episode_id`)))
        where
            ((`iop`.`value` = (
            select
                max(`iopmax`.`value`)
            from
                `v_patient_iop` `iopmax`
            where
                ((`iopmax`.`patient_id` = `iop`.`patient_id`)
                and (`iopmax`.`side` = `iop`.`side`)
                and (`iop`.`event_name` = 'Examination'))))
            and (`vep`.`episode_status` <> 'Discharged'))
        group by
            `iop`.`patient_id`,
            `iop`.`side`
        order by
            `iop`.`event_date`,
            `iop`.`reading_time`");

        // v_patient_min_iop
        // Selects the lowest ever IOP reading per patient, per eye
        $this->execute("CREATE OR REPLACE
        ALGORITHM = UNDEFINED VIEW `v_patient_min_iop` AS
        select
            `iop`.`patient_id` AS `patient_id`,
            `iop`.`event_id` AS `event_id`,
            `iop`.`event_date` AS `event_date`,
            `iop`.`reading_time` AS `reading_time`,
            `iop`.`value` AS `value`,
            `iop`.`Instrument` AS `instrument`,
            `iop`.`side` AS `side`,
            `iop`.`eye` AS `eye`,
            `iop`.`Comment` AS `comment`
        from
            `v_patient_iop` `iop`
        where
            (`iop`.`value` = (
            select
                min(`iopmin`.`value`)
            from
                `v_patient_iop` `iopmin`
            where
                ((`iopmin`.`patient_id` = `iop`.`patient_id`)
                and (`iopmin`.`side` = `iop`.`side`)
                and (`iop`.`event_name` = 'Examination'))))
        group by
            `iop`.`patient_id`,
            `iop`.`side`");

        // v_patient_latest_iop
        $this->execute("CREATE OR REPLACE
        ALGORITHM = UNDEFINED VIEW `v_patient_latest_iop` AS
        select
            `iop`.`patient_id` AS `patient_id`,
            `iop`.`event_id` AS `event_id`,
            `iop`.`event_date` AS `event_date`,
            `iop`.`reading_time` AS `reading_time`,
            `iop`.`value` AS `value`,
            `iop`.`Instrument` AS `instrument`,
            `iop`.`side` AS `side`,
            `iop`.`eye` AS `eye`,
            `iop`.`Comment` AS `comment`
        from
            (`v_patient_iop` `iop`
        left join `v_patient_iop` `x` on
            (((`iop`.`patient_id` = `x`.`patient_id`)
            and (`iop`.`side` = `x`.`side`)
            and (`iop`.`event_date` < `x`.`event_date`))))
        where
            isnull(`x`.`event_date`)");

        // v_patient_cvi_status
        $this->execute("CREATE OR REPLACE
        ALGORITHM = UNDEFINED VIEW `v_patient_cvi_status` AS
        select
            `e`.`patient_id` AS `patient_id`,
            `e`.`event_id` AS `event_id`,
            `e`.`event_date` AS `event_date`,
            `cvi`.`cvi_status_id` AS `cvi_status_id`,
            `s`.`name` AS `cvi_status`,
            `cvi`.`element_date` AS `registration_date`
        from
            ((`et_ophciexamination_cvi_status` `cvi`
        join `v_patient_events` `e` on
            ((`e`.`event_id` = `cvi`.`event_id`)))
        join `patient_oph_info_cvi_status` `s` on
            ((`s`.`id` = `cvi`.`cvi_status_id`)))
        where
            (`e`.`event_date` = (
            select
                max(`maxe`.`event_date`)
            from
                (`v_patient_events` `maxe`
            join `et_ophciexamination_cvi_status` `cvi2` on
                ((`maxe`.`event_id` = `cvi2`.`event_id`)))
            where
                (`maxe`.`patient_id` = `e`.`patient_id`)))");

        //v_diabetes_disorders
        $this->execute("CREATE OR REPLACE
        ALGORITHM = UNDEFINED VIEW `v_diabetes_disorders` AS
        select
            `d`.`id` AS `disorder_id`,
            `d`.`term` AS `term`
        from
            `disorder` `d`
        where
            (`d`.`id` in (
            select
                `node`.`disorder_id`
            from
                (`disorder_tree` `node`
            join `disorder_tree` `parent`)
            where
                ((`node`.`lft` between `parent`.`lft` and `parent`.`rght`)
                and (`parent`.`disorder_id` = (
                select
                    `disorder`.`id`
                from
                    `disorder`
                where
                    (`disorder`.`term` = 'Diabetes mellitus'))))
            order by
                `parent`.`lft`)
            and (`d`.`active` = 1))");

        //v_patient_is_diabetic
        $this->execute("CREATE OR REPLACE
        ALGORITHM = UNDEFINED VIEW `v_patient_is_diabetic` AS
        select
            distinct `p`.`id` AS `patient_id`,
            if(`p`.`id` in (
            select
                `vpd`.`patient_id`
            from
                (`v_patient_diagnoses` `vpd`
            join `v_diabetes_disorders` `vdd` on
                ((`vdd`.`disorder_id` = `vpd`.`disorder_id`)))),
            1,
            0) AS `is_diabetic`
        from
            `patient` `p`
        where
            (`p`.`deleted` = 0)");
    }

    public function safeDown()
    {
        $this->execute("DROP VIEW v_patient_iop");
        $this->execute("DROP VIEW v_patient_cct");
        $this->execute("DROP VIEW v_patient_max_cct");
        $this->execute("DROP VIEW v_anon_patient_details");
        $this->execute("DROP VIEW v_patient_max_iop");
        $this->execute("DROP VIEW v_patient_min_iop");
        $this->execute("DROP VIEW v_patient_cvi_status");
        $this->execute("DROP VIEW v_diabetes_disorders");
        $this->execute("DROP VIEW v_patient_is_diabetic");
    }
}
