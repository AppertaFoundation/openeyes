<?php

class m230307_164100_add_more_columns_to_v_patient_laser_procedures extends OEMigration
{
    public function safeUp()
    {
        $this->execute("CREATE OR REPLACE
                ALGORITHM = UNDEFINED VIEW `v_patient_laser_procedure` AS
                SELECT
                `p`.`id` AS `patient_id`,
                ev.worklist_patient_id AS 'worklist_patient_id',
                `lpa`.`id` AS `entry_id`,
                concat(`lpa`.`id`, '-2') AS `entry_id_unique`,
                `proc`.`id` AS `procedure_id`,
                `proc`.`term` AS `term`,
                `proc`.`short_format` AS `short_term`,
                `proc`.`snomed_code` AS `snomed_code`,
                `proc`.`snomed_term` AS `snomed_term`,
                `proc`.`ecds_code` AS `ecds_code`,
                `proc`.`ecds_term` AS `ecds_term`,
                'R' AS `eye`,
                ev.id as event_id,
                `ev`.`event_date` AS `event_date`,
                group_concat(`oc`.`name` SEPARATOR ',') AS `opcs_codes`,
                group_concat(`oc`.`description` SEPARATOR ',') AS `opcs_descriptions`,
                ev.last_modified_date AS 'last_modified_date',
                ev.last_modified_user_id AS 'last_modified_user'
            FROM `patient` `p`
            LEFT JOIN `episode` `ep` ON `ep`.`patient_id` = `p`.`id`
            LEFT JOIN `event` `ev` ON `ev`.`episode_id` = `ep`.`id`
            LEFT JOIN `et_ophtrlaser_treatment` `lt` ON `lt`.`event_id` = `ev`.`id`
            LEFT JOIN `ophtrlaser_laserprocedure_assignment` `lpa` ON `lpa`.`treatment_id` = `lt`.`id`
            LEFT JOIN `proc` ON `proc`.`id` = `lpa`.`procedure_id`
            LEFT JOIN proc_opcs_assignment poa ON poa.proc_id = proc.id
            LEFT JOIN opcs_code oc ON oc.id = poa.opcs_code_id
            WHERE `lpa`.`eye_id` <> 1
                AND ev.deleted = 0
            GROUP BY
                `ep`.`patient_id`,
                ev.id,
                `ev`.`worklist_patient_id`,
                `lpa`.`eye_id`,
                `proc`.`id`
            UNION ALL
            SELECT
                `p`.`id` AS `patient_id`,
                ev.worklist_patient_id AS 'worklist_patient_id',
                `lpa`.`id` AS `entry_id`,
                concat(`lpa`.`id`, '-1') AS `entry_id_unique`,
                `proc`.`id` AS `procedure_id`,
                `proc`.`term` AS `term`,
                `proc`.`short_format` AS `short_term`,
                `proc`.`snomed_code` AS `snomed_code`,
                `proc`.`snomed_term` AS `snomed_term`,
                `proc`.`ecds_code` AS `ecds_code`,
                `proc`.`ecds_term` AS `ecds_term`,
                'L' AS `eye`,
                ev.id as event_id,
                `ev`.`event_date` AS `event_date`,
                group_concat(`oc`.`name` SEPARATOR ',') AS `opcs_codes`,
                group_concat(`oc`.`description` SEPARATOR ',') AS `opcs_descriptions`,
                ev.last_modified_date AS 'last_modified_date',
                ev.last_modified_user_id AS 'last_modified_user'
            FROM `patient` `p`
            LEFT JOIN `episode` `ep` ON `ep`.`patient_id` = `p`.`id`
            LEFT JOIN `event` `ev` ON `ev`.`episode_id` = `ep`.`id`
            LEFT JOIN `et_ophtrlaser_treatment` `lt` ON `lt`.`event_id` = `ev`.`id`
            LEFT JOIN `ophtrlaser_laserprocedure_assignment` `lpa` ON `lpa`.`treatment_id` = `lt`.`id`
            LEFT JOIN `proc` ON `proc`.`id` = `lpa`.`procedure_id`
            LEFT JOIN proc_opcs_assignment poa ON poa.proc_id = proc.id
            LEFT JOIN opcs_code oc ON oc.id = poa.opcs_code_id
            WHERE `lpa`.`eye_id` <> 0
                AND ev.deleted = 0
            GROUP BY
                `ep`.`patient_id`,
                ev.id,
                `ev`.`worklist_patient_id`,
                `lpa`.`eye_id`,
                `proc`.`id`;
    ");

        return true;
    }

    public function safeDown()
    {
        $this->execute("CREATE OR REPLACE
        ALGORITHM = UNDEFINED VIEW `v_patient_laser_procedure` AS
                    SELECT
                        `p`.`id` AS `patient_id`,
                        `proc`.`id` AS `procedure_id`,
                        `proc`.`term` AS `term`,
                        `proc`.`short_format` AS `short_term`,
                        `proc`.`snomed_code` AS `snomed_code`,
                        `proc`.`snomed_term` AS `snomed_term`,
                        `proc`.`ecds_code` AS `ecds_code`,
                        `proc`.`ecds_term` AS `ecds_term`,
                        'R' AS `eye`,
                        `ev`.`event_date` AS `event_date`
                    FROM `patient` `p`
                    JOIN `episode` `ep` ON `ep`.`patient_id` = `p`.`id`
                    JOIN `event` `ev` ON `ev`.`episode_id` = `ep`.`id`
                    JOIN `et_ophtrlaser_treatment` `lt` ON `lt`.`event_id` = `ev`.`id`
                    JOIN `ophtrlaser_laserprocedure_assignment` `lpa` ON `lpa`.`treatment_id` = `lt`.`id`
                    JOIN `proc` ON `proc`.`id` = `lpa`.`procedure_id`
                    WHERE `lpa`.`eye_id` <> 1
                    UNION ALL
                    SELECT
                        `p`.`id` AS `patient_id`,
                        `proc`.`id` AS `procedure_id`,
                        `proc`.`term` AS `term`,
                        `proc`.`short_format` AS `short_term`,
                        `proc`.`snomed_code` AS `snomed_code`,
                        `proc`.`snomed_term` AS `snomed_term`,
                        `proc`.`ecds_code` AS `ecds_code`,
                        `proc`.`ecds_term` AS `ecds_term`,
                        'L' AS `eye`,
                        `ev`.`event_date` AS `event_date`
                    FROM `patient` `p`
                    JOIN `episode` `ep` ON `ep`.`patient_id` = `p`.`id`
                    JOIN `event` `ev` ON `ev`.`episode_id` = `ep`.`id`
                    JOIN `et_ophtrlaser_treatment` `lt` ON `lt`.`event_id` = `ev`.`id`
                    JOIN `ophtrlaser_laserprocedure_assignment` `lpa` ON `lpa`.`treatment_id` = `lt`.`id`
                    JOIN `proc` ON `proc`.`id` = `lpa`.`procedure_id`
                    WHERE `lpa`.`eye_id` <> 0;
    ");
        return true;
    }
}
