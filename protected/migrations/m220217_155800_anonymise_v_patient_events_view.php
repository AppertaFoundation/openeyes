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

        // remove patient name columns from v_messages
        $this->execute("CREATE OR REPLACE
ALGORITHM = UNDEFINED VIEW `v_patient_messages` AS
select
    `m`.`id` AS `message_id`,
    `ev`.`patient_id` AS `patient_id`,
    `m`.`event_id` AS `event_id`,
    `m`.`message_type_id` AS `message_type_id`,
    `t`.`name` AS `message_type`,
    `m`.`urgent` AS `urgent`,
    `m`.`message_text` AS `message_text`,
    `m`.`marked_as_read` AS `marked_as_read`,
    `uu`.`first_name` AS `FROM_firstname`,
    `uu`.`last_name` AS `FROM_lastname`,
    `u`.`first_name` AS `TO_firstname`,
    `u`.`last_name` AS `TO_lastname`
from
    `et_ophcomessaging_message` `m`
join `user` `u` on
    `u`.`id` = `m`.`for_the_attention_of_user_id`
join `v_patient_events` `ev` on
    `ev`.`event_id` = `m`.`event_id`
join `user` `uu` on
    `uu`.`id` = `m`.`last_modified_user_id`
join `ophcomessaging_message_message_type` `t` on
    `t`.`id` = `m`.`message_type_id`
where
    `m`.`deleted` = 0");
    }

    public function safeDown()
    {
        echo("Down not supported");
    }
}
