<?php

class m220217_155800_anonymise_v_patient_events_view extends CDbMigration
{
    public function safeUp()
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

    public function safeDown()
    {
        echo("Down not supported");
    }
}
