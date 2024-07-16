<?php

class m240715_113700_fix_v_patient_intravitreal_injections_view extends OEMigration
{
    public function safeUp()
    {
        $this->execute("CREATE OR REPLACE
ALGORITHM = UNDEFINED VIEW `v_patient_intravitreal_injections` AS
	SELECT
	    `p`.`id` AS `patient_id`,
	    'R' AS `eye`,
	    `ev`.`event_date` AS `event_date`,
	    `ev`.`worklist_patient_id` AS `worklist_patient_id`,
	    `ijt`.`id` AS `entry_id`,
	    CONCAT(`ijt`.`id`, '-2') AS `entry_id_unique`,
	    `ijad`.`name` AS `pre_antisept_drug`,
	    `ijsd`.`name` AS `pre_skin_drug`,
	    `ijtd`.`name` AS `drug`,
	    `ijt`.`right_number` AS `number`,
	    `ijt`.`right_batch_number` AS `batch_number`,
	    `ijt`.`right_batch_expiry_date` AS `batch_expiry_date`,
	    CONCAT(`user`.`title`, ' ', `user`.`first_name`, ' ', `user`.`last_name`) AS `injection_given_by`,
	    `ijt`.`right_injection_time` AS `injection_time`,
	    CASE `ijt`.`right_pre_ioplowering_required` WHEN 0 THEN 'No' WHEN 1 THEN 'Yes' END AS `pre_ioplowering_required`,
	    CASE `ijt`.`right_post_ioplowering_required` WHEN 0 THEN 'No' WHEN 1 THEN 'Yes' END AS `post_ioplowering_required`,
	    `ijls`.`name` AS `lens_status`,
        GROUP_CONCAT(ijcn.name SEPARATOR ' // ') AS 'complication', 
	    `user`.`role` AS `administrator_role`,
	    `dg`.`grade` AS `doctor_grade`,
	    `site`.`name` AS `site`,
	    `institution`.`name` AS `institution`,
	    `ijt`.`last_modified_user_id` AS `last_modified_user_id`,
	    `ijt`.`last_modified_date` AS `last_modified_date`,
	    `ev`.`id` AS `event_id`
	FROM `patient` `p`
	JOIN `episode` `ep` ON `ep`.`patient_id` = `p`.`id`
	JOIN `event` `ev` ON `ev`.`episode_id` = `ep`.`id`
	JOIN `et_ophtrintravitinjection_treatment` `ijt` ON `ijt`.`event_id` = `ev`.`id`
	LEFT JOIN `et_ophtrintravitinjection_site` `ijs` ON `ijs`.`event_id` = `ev`.`id`
	LEFT JOIN `ophtrintravitinjection_antiseptic_drug` `ijad` ON `ijad`.`id` = `ijt`.`right_pre_antisept_drug_id`
	LEFT JOIN `ophtrintravitinjection_skin_drug` `ijsd` ON `ijsd`.`id` = `ijt`.`right_pre_skin_drug_id`
	LEFT JOIN `ophtrintravitinjection_treatment_drug` `ijtd` ON `ijtd`.`id` = `ijt`.`right_drug_id`
	LEFT JOIN `et_ophtrintravitinjection_anteriorseg` `ija` ON `ija`.`event_id` = `ev`.`id`
	LEFT JOIN `ophtrintravitinjection_lens_status` `ijls` ON `ijls`.`id` = `ija`.`right_lens_status_id`
	LEFT JOIN `et_ophtrintravitinjection_complications` `ijc` ON `ijc`.`event_id` = `ev`.`id`
	LEFT JOIN `ophtrintravitinjection_complicat_assignment` ijca ON ijca.element_id = ijc.id AND ijca.eye_id = 2
	LEFT JOIN `ophtrintravitinjection_complicat` ijcn ON ijcn.id = ijca.complication_id
	LEFT JOIN `user` ON `user`.`id` = `ijt`.`right_injection_given_by_id`
	LEFT JOIN `doctor_grade` `dg` ON `dg`.`id` = `user`.`doctor_grade_id`
	LEFT JOIN `site` ON `site`.`id` = `ijs`.`site_id`
	LEFT JOIN `institution` ON `institution`.`id` = `site`.`institution_id`
	WHERE `ijt`.`eye_id` <> 1
	GROUP BY ijt.id

UNION

	SELECT
	    `p`.`id` AS `patient_id`,
	    'L' AS `eye`,
	    `ev`.`event_date` AS `event_date`,
	    `ev`.`worklist_patient_id` AS `worklist_patient_id`,
	    `ijt`.`id` AS `entry_id`,
	    CONCAT(`ijt`.`id`, '-1') AS `entry_id_unique`,
	    `ijad`.`name` AS `pre_antisept_drug`,
	    `ijsd`.`name` AS `pre_skin_drug`,
	    `ijtd`.`name` AS `drug`,
	    `ijt`.`left_number` AS `number`,
	    `ijt`.`left_batch_number` AS `batch_number`,
	    `ijt`.`left_batch_expiry_date` AS `batch_expiry_date`,
	    CONCAT(`user`.`title`, ' ', `user`.`first_name`, ' ', `user`.`last_name`) AS `injection_given_by`,
	    `ijt`.`left_injection_time` AS `injection_time`,
	    CASE `ijt`.`left_pre_ioplowering_required` WHEN 0 THEN 'No' WHEN 1 THEN 'Yes' END AS `pre_ioplowering_required`,
	    CASE `ijt`.`left_post_ioplowering_required` WHEN 0 THEN 'No' WHEN 1 THEN 'Yes' END AS `post_ioplowering_required`,
	    `ijls`.`name` AS `lens_status`,
	    GROUP_CONCAT(ijcn.name SEPARATOR ' // ') AS 'complication', 
	    `user`.`role` AS `administrator_role`,
	    `dg`.`grade` AS `doctor_grade`,
	    `site`.`name` AS `site`,
	    `institution`.`name` AS `institution`,
	    `ijt`.`last_modified_user_id` AS `last_modified_user_id`,
	    `ijt`.`last_modified_date` AS `last_modified_date`,
	    `ev`.`id` AS `event_id`
	FROM `patient` `p`
	JOIN `episode` `ep` ON `ep`.`patient_id` = `p`.`id`
	JOIN `event` `ev` ON `ev`.`episode_id` = `ep`.`id`
	JOIN `et_ophtrintravitinjection_treatment` `ijt` ON `ijt`.`event_id` = `ev`.`id`
	LEFT JOIN `et_ophtrintravitinjection_site` `ijs` ON `ijs`.`event_id` = `ev`.`id`
	LEFT JOIN `ophtrintravitinjection_antiseptic_drug` `ijad` ON `ijad`.`id` = `ijt`.`left_pre_antisept_drug_id`
	LEFT JOIN `ophtrintravitinjection_skin_drug` `ijsd` ON `ijsd`.`id` = `ijt`.`left_pre_skin_drug_id`
	LEFT JOIN `ophtrintravitinjection_treatment_drug` `ijtd` ON `ijtd`.`id` = `ijt`.`left_drug_id`
	LEFT JOIN `et_ophtrintravitinjection_anteriorseg` `ija` ON `ija`.`event_id` = `ev`.`id`
	LEFT JOIN `ophtrintravitinjection_lens_status` `ijls` ON `ijls`.`id` = `ija`.`left_lens_status_id`
	LEFT JOIN `et_ophtrintravitinjection_complications` `ijc` ON `ijc`.`event_id` = `ev`.`id`
	LEFT JOIN `ophtrintravitinjection_complicat_assignment` ijca ON ijca.element_id = ijc.id AND ijca.eye_id = 1
	LEFT JOIN `ophtrintravitinjection_complicat` ijcn ON ijcn.id = ijca.complication_id
	LEFT JOIN `user` ON `user`.`id` = `ijt`.`left_injection_given_by_id`
	LEFT JOIN `doctor_grade` `dg` ON `dg`.`id` = `user`.`doctor_grade_id`
	LEFT JOIN `site` ON `site`.`id` = `ijs`.`site_id`
	LEFT JOIN `institution` ON `institution`.`id` = `site`.`institution_id`
	WHERE `ijt`.`eye_id` <> 2
	GROUP BY ijt.id");
    }

    public function safeDown()
    {
        return $this->execute("CREATE OR REPLACE
ALGORITHM = UNDEFINED VIEW `v_patient_intravitreal_injections` AS
SELECT
    `p`.`id` AS `patient_id`
    , 'R' AS `eye`
    , `ev`.`event_date` AS `event_date`
    , `ev`.`worklist_patient_id` AS `worklist_patient_id`
    , `ijt`.`id` AS `entry_id`
    , concat(`ijt`.`id`, '-2') AS `entry_id_unique`
    , `ijad`.`name` AS `pre_antisept_drug`
    , `ijsd`.`name` AS `pre_skin_drug`
    , `ijtd`.`name` AS `drug`
    , `ijt`.`right_number` AS `number`
    , `ijt`.`right_batch_number` AS `batch_number`
    , `ijt`.`right_batch_expiry_date` AS `batch_expiry_date`
    , concat(`user`.`title`, ' ', `user`.`first_name`, ' ', `user`.`last_name`) AS `injection_given_by`
    , `ijt`.`right_injection_time` AS `injection_time`
    , CASE
        `ijt`.`right_pre_ioplowering_required` WHEN 0 THEN 'No'
        WHEN 1 THEN 'Yes'
    END AS `pre_ioplowering_required`
    , CASE
        `ijt`.`right_post_ioplowering_required` WHEN 0 THEN 'No'
        WHEN 1 THEN 'Yes'
    END AS `post_ioplowering_required`
    , `ijls`.`name` AS `lens_status`
    , `ijc`.`right_oth_descrip` AS `complication`
    , `user`.`role` AS `administrator`
    , `dg`.`grade` AS `doctor_grade`
    , `site`.`name` AS `site`
    , `institution`.`name` AS `institution`
    , `ijt`.`last_modified_user_id` AS `last_modified_user_id`
    , `ijt`.`last_modified_date` AS `last_modified_date`
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
                                                JOIN `et_ophtrintravitinjection_treatment` `ijt` ON
                                                    (
                                                        `ijt`.`event_id` = `ev`.`id`
                                                    )
                                                )
                                            LEFT JOIN `et_ophtrintravitinjection_site` `ijs` ON
                                                (
                                                    `ijs`.`event_id` = `ev`.`id`
                                                )
                                            )
                                        LEFT JOIN `ophtrintravitinjection_antiseptic_drug` `ijad` ON
                                            (
                                                `ijad`.`id` = `ijt`.`right_pre_antisept_drug_id`
                                            )
                                        )
                                    LEFT JOIN `ophtrintravitinjection_skin_drug` `ijsd` ON
                                        (
                                            `ijsd`.`id` = `ijt`.`right_pre_skin_drug_id`
                                        )
                                    )
                                LEFT JOIN `ophtrintravitinjection_treatment_drug` `ijtd` ON
                                    (
                                        `ijtd`.`id` = `ijt`.`right_drug_id`
                                    )
                                )
                            LEFT JOIN `et_ophtrintravitinjection_anteriorseg` `ija` ON
                                (
                                    `ija`.`event_id` = `ev`.`id`
                                )
                            )
                        LEFT JOIN `ophtrintravitinjection_lens_status` `ijls` ON
                            (
                                `ijls`.`id` = `ija`.`left_lens_status_id`
                            )
                        )
                    LEFT JOIN `et_ophtrintravitinjection_complications` `ijc` ON
                        (
                            `ijc`.`event_id` = `ev`.`id`
                        )
                    )
                LEFT JOIN `user` ON
                    (
                        `user`.`id` = `ijt`.`right_injection_given_by_id`
                    )
                )
            LEFT JOIN `doctor_grade` `dg` ON
                (
                    `dg`.`id` = `user`.`doctor_grade_id`
                )
            )
        LEFT JOIN `site` ON
            (
                `site`.`id` = `ijs`.`site_id`
            )
        )
    LEFT JOIN `institution` ON
        (
            `institution`.`id` = `site`.`institution_id`
        )
    )
WHERE
    `ijt`.`eye_id` <> 1
UNION
SELECT
    `p`.`id` AS `patient_id`
    , 'L' AS `eye`
    , `ev`.`event_date` AS `event_date`
    , `ev`.`worklist_patient_id` AS `worklist_patient_id`
    , `ijt`.`id` AS `entry_id`
    , concat(`ijt`.`id`, '-1') AS `entry_id_unique`
    , `ijad`.`name` AS `pre_antisept_drug`
    , `ijsd`.`name` AS `pre_skin_drug`
    , `ijtd`.`name` AS `drug`
    , `ijt`.`left_number` AS `number`
    , `ijt`.`left_batch_number` AS `batch_number`
    , `ijt`.`left_batch_expiry_date` AS `batch_expiry_date`
    , concat(`user`.`title`, ' ', `user`.`first_name`, ' ', `user`.`last_name`) AS `injection_given_by`
    , `ijt`.`left_injection_time` AS `injection_time`
    , CASE
        `ijt`.`left_pre_ioplowering_required` WHEN 0 THEN 'No'
        WHEN 1 THEN 'Yes'
    END AS `pre_ioplowering_required`
    , CASE
        `ijt`.`left_post_ioplowering_required` WHEN 0 THEN 'No'
        WHEN 1 THEN 'Yes'
    END AS `post_ioplowering_required`
    , `ijls`.`name` AS `lens_status`
    , `ijc`.`left_oth_descrip` AS `complication`
    , `user`.`role` AS `administrator`
    , `dg`.`grade` AS `doctor_grade`
    , `site`.`name` AS `site`
    , `institution`.`name` AS `institution`
    , `ijt`.`last_modified_user_id` AS `last_modified_user_id`
    , `ijt`.`last_modified_date` AS `last_modified_date`
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
                                                    JOIN `et_ophtrintravitinjection_treatment` `ijt` ON
                                                        (
                                                            `ijt`.`event_id` = `ev`.`id`
                                                        )
                                                    )
                                                LEFT JOIN `et_ophtrintravitinjection_site` `ijs` ON
                                                    (
                                                        `ijs`.`event_id` = `ev`.`id`
                                                    )
                                                )
                                            LEFT JOIN `ophtrintravitinjection_antiseptic_drug` `ijad` ON
                                                (
                                                    `ijad`.`id` = `ijt`.`left_pre_antisept_drug_id`
                                                )
                                            )
                                        LEFT JOIN `ophtrintravitinjection_skin_drug` `ijsd` ON
                                            (
                                                `ijsd`.`id` = `ijt`.`left_pre_skin_drug_id`
                                            )
                                        )
                                    LEFT JOIN `ophtrintravitinjection_treatment_drug` `ijd` ON
                                        (
                                            `ijd`.`id` = `ijt`.`left_pre_antisept_drug_id`
                                        )
                                    )
                                LEFT JOIN `ophtrintravitinjection_treatment_drug` `ijtd` ON
                                    (
                                        `ijtd`.`id` = `ijt`.`left_drug_id`
                                    )
                                )
                            LEFT JOIN `et_ophtrintravitinjection_anteriorseg` `ija` ON
                                (
                                    `ija`.`event_id` = `ev`.`id`
                                )
                            )
                        LEFT JOIN `ophtrintravitinjection_lens_status` `ijls` ON
                            (
                                `ijls`.`id` = `ija`.`left_lens_status_id`
                            )
                        )
                    LEFT JOIN `et_ophtrintravitinjection_complications` `ijc` ON
                        (
                            `ijc`.`event_id` = `ev`.`id`
                        )
                    )
                LEFT JOIN `user` ON
                    (
                        `user`.`id` = `ijt`.`left_injection_given_by_id`
                    )
                )
            LEFT JOIN `doctor_grade` `dg` ON
                (
                    `dg`.`id` = `user`.`doctor_grade_id`
                )
            )
        LEFT JOIN `site` ON
            (
                `site`.`id` = `ijs`.`site_id`
            )
        )
    LEFT JOIN `institution` ON
        (
            `institution`.`id` = `site`.`institution_id`
        )
    )
WHERE
    `ijt`.`eye_id` <> 0;");
    }
}
