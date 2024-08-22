<?php

class m220222_164000_add_institution_and_site_to_event_view extends CDbMigration
{
    public function safeUp()
    {
        // update v_patient_events to add institution and site columns
        $this->execute("CREATE OR REPLACE
ALGORITHM = UNDEFINED VIEW `v_patient_events` AS
select
    `p`.`id` AS `patient_id`,
    `ev`.`id` AS `event_id`,
    `ep`.`id` AS `episode_id`,
    `ev`.`event_type_id` AS `event_type_id`,
    `et`.`name` AS `event_name`,
    `et`.`class_name` AS `event_class`,
    `ev`.`event_date` AS `event_date`,
    `ev`.`created_date` AS `event_created_date`,
    `ev`.`last_modified_date` AS `event_last_modified_date`,
    `f`.`name` AS `firm_name`,
    `f`.`id` AS `firm_id`,
    `s`.`id` AS `subspecialty_id`,
    `s`.`name` AS `subspecialty`,
    `ev`.`institution_id` AS `institution_id`,
    `i`.`name` AS `institution_name`,
    `ev`.`site_id` AS `site_id`,
    `si`.`name` AS `site_name`
FROM patient p 
    	INNER JOIN episode ep ON ep.patient_id = p.id
		LEFT JOIN event ev ON ev.episode_id = ep.id
		LEFT JOIN `event_type` `et` ON
    `et`.`id` = `ev`.`event_type_id`
LEFT JOIN `firm` `f` ON
    `f`.`id` = `ep`.`firm_id`
LEFT JOIN `service_subspecialty_assignment` `ssa` ON
    `ssa`.`id` = `f`.`service_subspecialty_assignment_id`
LEFT JOIN `subspecialty` `s` ON
    `s`.`id` = `ssa`.`subspecialty_id`
LEFT JOIN `institution` `i` ON
    `i`.`id` = `ev`.`institution_id`
LEFT JOIN `site` `si` ON
    `si`.`id` = `ev`.`site_id`
where
    `ev`.`deleted` = 0
    and `ep`.`deleted` = 0");
    }

    public function safeDown()
    {
        $this->execute("CREATE OR REPLACE
ALGORITHM = UNDEFINED VIEW `v_patient_events` AS
select
    `p`.`id` AS `patient_id`,
    `ev`.`id` AS `event_id`,
    `ep`.`id` AS `episode_id`,
    `ev`.`event_type_id` AS `event_type_id`,
    `et`.`name` AS `event_name`,
    `et`.`class_name` AS `event_class`,
    `ev`.`event_date` AS `event_date`,
    `ev`.`created_date` AS `event_created_date`,
    `ev`.`last_modified_date` AS `event_last_modified_date`,
    `f`.`name` AS `firm_name`,
    `f`.`id` AS `firm_id`,
    `s`.`id` AS `subspecialty_id`,
    `s`.`name` AS `subspecialty`
from
    `patient` `p`
join `episode` `ep` on
    `ep`.`patient_id` = `p`.`id`
join `event` `ev` on
    `ev`.`episode_id` = `ep`.`id`
join `event_type` `et` on
    `et`.`id` = `ev`.`event_type_id`
join `firm` `f` on
    `f`.`id` = `ep`.`firm_id`
join `service_subspecialty_assignment` `ssa` on
    `ssa`.`id` = `f`.`service_subspecialty_assignment_id`
join `subspecialty` `s` on
    `s`.`id` = `ssa`.`subspecialty_id`
where
    `ev`.`deleted` = 0
    and `ep`.`deleted` = 0");
    }
}
