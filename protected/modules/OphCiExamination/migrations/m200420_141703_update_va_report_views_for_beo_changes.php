<?php
/**
 * (C) Apperta Foundation, 2020
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2020, Apperta Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

class m200420_141703_update_va_report_views_for_beo_changes extends OEMigration
{
    public function up()
    {
        $this->execute("CREATE OR REPLACE
        ALGORITHM = UNDEFINED VIEW `v_patient_va` AS
        select
            `pe`.`patient_id` AS `patient_id`,
            `eva`.`event_id` AS `event_id`,
            `pe`.`event_date` AS `reading_date`,
            `r`.`side` AS `side`,
            `r`.`value` AS `base_value`,
            `r`.`unit_id` AS `unit_id`,
            `m`.`id` AS `method_id`,
            (case
                `r`.`side`
                when 0 then 'R'
                when 1 then 'L'
                when 2 then 'B'
                 end) AS `eye`,
            `u`.`name` AS `unit_name`,
            `v`.`value` AS `value`,
            `m`.`name` AS `method`,
            (case r.side
                WHEN 0 then eva.right_notes
                WHEN 1 then eva.left_notes
                WHEN 2 then eva.beo_notes END) AS 'Comment'
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
            (`v`.`unit_id` = `r`.`unit_id`)
        order by
            `pe`.`patient_id`,
            `pe`.`event_date`,
            `pe`.`event_created_date`,
            `pe`.`event_last_modified_date`");

        $this->execute("CREATE OR REPLACE
        ALGORITHM = UNDEFINED VIEW `v_patient_va_converted` AS
        select
            `pe`.`patient_id` AS `patient_id`,
            `eva`.`event_id` AS `event_id`,
            `pe`.`event_date` AS `reading_date`,
            `r`.`side` AS `side`,
            `r`.`value` AS `base_value`,
            `r`.`unit_id` AS `original_unit_id`,
            `m`.`id` AS `method_id`,
            (case
                `r`.`side`
                when 0 then 'R'
                when 1 then 'L'
                when 2 then 'B' end) AS `eye`,
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
                WHEN 1 then eva.left_notes
                WHEN 2 then eva.beo_notes END) AS 'Comment'
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
            (`v`.`unit_id` = `r`.`unit_id`)
        order by
            `pe`.`patient_id`,
            `pe`.`event_date`,
            `pe`.`event_created_date`,
            `pe`.`event_last_modified_date`");
    }

    public function down()
    {
        // see m200218_161000_add_more_report_views for previous implementation of views
        // as setup below.
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
    }
}
