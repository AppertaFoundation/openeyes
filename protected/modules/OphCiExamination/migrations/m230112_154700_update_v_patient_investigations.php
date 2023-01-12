<?php

class m230112_154700_update_v_patient_investigations extends OEMigration
{
    public function safeUp()
    {
        $this->execute("CREATE OR REPLACE
        ALGORITHM = UNDEFINED VIEW `v_patient_investigations` AS
        SELECT
            `p`.`id` AS `patient_id`
            , `ev`.`id` AS `event_id`
            , `eoie`.`id` AS `entry_id`
            , `eoie`.`date` AS `investigation_date`
            , `eoie`.`time` AS `investigation_time`
            , `eoie`.`investigation_code` AS `investigation_code_id`
            , `eoic`.`name` AS `investigation_name`
            , ifnull(`eoie`.`comments`, `oic`.`comments`) AS `investigation_comments`
            , `eoic`.`snomed_term` AS `snomed_term`
            , `eoic`.`snomed_code` AS `snomed_code`
            , group_concat(`oc`.`name` SEPARATOR ',') AS `opcs_code`
            , group_concat(`oc`.`description` SEPARATOR ',') AS `opcs_description`
            , `eoic`.`ecds_code` AS `ecds_code`
            , ifnull(`proc`.`ecds_term`, `d`.`ecds_term`) AS `ecds_term`
            , `eoic`.`specialty_id` AS `specialty_id`
            , `ev`.`worklist_patient_id` AS `worklist_patient_id`
            , `eoi`.`created_user_id` AS `created_user_id`
            , `eoi`.`created_date` AS `created_date`
            , `eoi`.`last_modified_user_id` AS `last_modified_user_id`
            , `eoi`.`last_modified_date` AS `last_modified_date`
        FROM
            `patient` `p`
        JOIN `episode` `ep` ON
                                `ep`.`patient_id` = `p`.`id`
        JOIN `event` `ev` ON
            `ev`.`episode_id` = `ep`.`id`
        JOIN `et_ophciexamination_investigation` `eoi` ON
            `eoi`.`event_id` = `ev`.`id`
        JOIN `et_ophciexamination_investigation_entry` `eoie` ON
            `eoie`.`element_id` = `eoi`.`id`
        JOIN `et_ophciexamination_investigation_codes` `eoic` ON
            `eoic`.`id` = `eoie`.`investigation_code`
        LEFT JOIN `ophciexamination_investigation_comments` `oic` ON
            `oic`.`investigation_code` = `eoic`.`id`
        LEFT JOIN `proc` ON
            `proc`.`ecds_code` = `eoic`.`ecds_code`
        LEFT JOIN `disorder` `d` ON
            `d`.`ecds_code` = `eoic`.`ecds_code`
        LEFT JOIN `proc_opcs_assignment` `poa` ON
            `poa`.`proc_id` = `proc`.`id`
        LEFT JOIN `opcs_code` `oc` ON
            `oc`.`id` = `poa`.`opcs_code_id`
        GROUP BY
            `eoie`.`id`;");
    }

    public function safeDown()
    {
        $this->execute("CREATE OR REPLACE
        ALGORITHM = UNDEFINED VIEW `v_patient_investigations` AS
        SELECT
            `p`.`id` AS `patient_id`
            , `ev`.`id` AS `event_id`
            , `eoie`.`id` AS `entry_id`
            , `eoie`.`date` AS `investigation_date`
            , `eoie`.`time` AS `investigation_time`
            , `eoie`.`investigation_code` AS `investigation_code_id`
            , `eoic`.`name` AS `investigation_name`
            , ifnull(`eoie`.`comments`, `oic`.`comments`) AS `investigation_comments`
            , `eoic`.`snomed_term` AS `snomed_term`
            , `eoic`.`snomed_code` AS `snomed_code`
            , `oc`.`name` AS `opcs_code`
            , `oc`.`description` AS `opcs_terms`
            , `eoic`.`ecds_code` AS `ecds_code`
            , ifnull(`proc`.`ecds_term`, `d`.`ecds_term`) AS `ecds_term`
            , `eoic`.`specialty_id` AS `specialty_id`
            , `ev`.`worklist_patient_id` AS `worklist_patient_id`
            , `eoi`.`created_user_id` AS `created_user_id`
            , `eoi`.`created_date` AS `created_date`
            , `eoi`.`last_modified_user_id` AS `last_modified_user_id`
            , `eoi`.`last_modified_date` AS `last_modified_date`
        FROM
            (
                (
                    (
                        (
                            (
                                (
                                    (
                                        (
                                            (
                                                (
                                                    `patient` `p`
                                                JOIN `episode` `ep` ON
                                                    (
                                                        `ep`.`patient_id` = `p`.`id`
                                                    )
                                                )
                                            JOIN `event` `ev` ON
                                                (
                                                    `ev`.`episode_id` = `ep`.`id`
                                                )
                                            )
                                        JOIN `et_ophciexamination_investigation` `eoi` ON
                                            (
                                                `eoi`.`event_id` = `ev`.`id`
                                            )
                                        )
                                    JOIN `et_ophciexamination_investigation_entry` `eoie` ON
                                        (
                                            `eoie`.`element_id` = `eoi`.`id`
                                        )
                                    )
                                JOIN `et_ophciexamination_investigation_codes` `eoic` ON
                                    (
                                        `eoic`.`id` = `eoie`.`investigation_code`
                                    )
                                )
                            LEFT JOIN `ophciexamination_investigation_comments` `oic` ON
                                (
                                    `oic`.`investigation_code` = `eoic`.`id`
                                )
                            )
                        LEFT JOIN `proc` ON
                            (
                                `proc`.`ecds_code` = `eoic`.`ecds_code`
                            )
                        )
                    LEFT JOIN `disorder` `d` ON
                        (
                            `d`.`ecds_code` = `eoic`.`ecds_code`
                        )
                    )
                LEFT JOIN `proc_opcs_assignment` `poa` ON
                    (
                        `poa`.`proc_id` = `proc`.`id`
                    )
                )
            LEFT JOIN `opcs_code` `oc` ON
                (
                    `oc`.`id` = `poa`.`opcs_code_id`
                )
            );");
    }
}
