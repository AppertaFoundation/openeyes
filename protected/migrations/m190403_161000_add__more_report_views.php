<?php

class m190403_161000_add__more_report_views extends CDbMigration
{
    public static $diagnosis_view_definition = <<<EOSQL
    CREATE OR REPLACE VIEW `v_patient_diagnoses` AS
    SELECT
        `p`.`patient_id` AS `patient_id`,
        `p`.`eye_id` AS `eye_id`,
        `p`.`side` AS `side`,
        `d`.`id` AS `disorder_id`,
        `d`.`term` AS `disorder_term`,
        `d`.`fully_specified_name` AS `disorder_fully_specified`,
        `p`.`disorder_date` AS `disorder_date`,
        `d`.`aliases` AS `disorder_aliases`,
        `s`.`name` AS `specialty`
    FROM
        (((`v_patient_episodes` `p`
        JOIN `disorder` `d` ON ((`p`.`disorder_id` = `d`.`id`)))
        LEFT JOIN `subspecialty` `ss` ON ((`ss`.`id` = `p`.`subspecialty_id`)))
        LEFT JOIN `specialty` `s` ON ((`s`.`id` = `ss`.`specialty_id`)))
    UNION SELECT
        `sd`.`patient_id` AS `patient_id`,
        `sd`.`eye_id` AS `eye_id`,
        (CASE `sd`.`eye_id`
            WHEN 1 THEN 'L'
            WHEN 2 THEN 'R'
            WHEN 3 THEN 'B'
        END) AS `side`,
        `d`.`id` AS `disorder_id`,
        `d`.`term` AS `disorder_term`,
        `d`.`fully_specified_name` AS `disorder_fully_specified`,
        `sd`.`date` AS `disorder_date`,
        `d`.`aliases` AS `disorder_aliases`,
        `s`.`name` AS `specialty`
    FROM
        ((`secondary_diagnosis` `sd`
        JOIN `disorder` `d` ON ((`d`.`id` = `sd`.`disorder_id`)))
        LEFT JOIN `specialty` `s` ON ((`s`.`id` = `d`.`specialty_id`)))
    ORDER BY `patient_id`
EOSQL;

    public function safeUp()
    {
        $this->execute("CREATE OR REPLACE VIEW `v_patient_episodes` AS
    SELECT
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
        (CASE `ep`.`eye_id`
            WHEN 1 THEN 'L'
            WHEN 2 THEN 'R'
            WHEN 3 THEN 'B'
        END) AS `side`,
        `s`.`id` AS `subspecialty_id`,
        `s`.`name` AS `subspecialty`,
        `ep`.`start_date` AS `start_date`,
        `ep`.`end_date` AS `end_date`,
        `d`.`id` AS `disorder_id`,
        `d`.`term` AS `disorder_term`,
        `ep`.`disorder_date` AS `disorder_date`
    FROM
        ((((((`patient` `p`
        JOIN `episode` `ep` ON ((`ep`.`patient_id` = `p`.`id`)))
        JOIN `contact` `c` ON ((`c`.`id` = `p`.`contact_id`)))
        JOIN `firm` `f` ON ((`f`.`id` = `ep`.`firm_id`)))
        JOIN `service_subspecialty_assignment` `ssa` ON ((`ssa`.`id` = `f`.`service_subspecialty_assignment_id`)))
        JOIN `subspecialty` `s` ON ((`s`.`id` = `ssa`.`subspecialty_id`)))
        LEFT JOIN `disorder` `d` ON ((`d`.`id` = `ep`.`disorder_id`)))
    WHERE
        (`ep`.`deleted` = 0)
    ORDER BY `p`.`id`
	");

        $this->execute(static::$diagnosis_view_definition);
    }

    public function safeDown()
    {
        $this->execute("DROP VIEW v_patient_episodes");
        $this->execute("DROP VIEW v_patient_diagnoses");
    }
}
