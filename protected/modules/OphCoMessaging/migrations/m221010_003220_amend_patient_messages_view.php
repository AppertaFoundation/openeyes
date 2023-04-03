<?php

class m221010_003220_amend_patient_messages_view extends OEMigration
{
    public function up()
    {
        $this->execute("CREATE OR REPLACE VIEW v_patient_messages AS
    SELECT
        m.id AS message_id,
        ev.patient_id AS patient_id,
        m.event_id AS event_id,
        m.message_type_id AS message_type_id,
        t.name AS message_type,
        m.urgent AS urgent,
        m.message_text AS message_text,
        mr.marked_as_read AS marked_as_read,
        sender.name AS FROM_name,
        recipient.name AS TO_name
    FROM
        et_ophcomessaging_message m
        JOIN ophcomessaging_message_recipient mr ON mr.element_id = m.id
        JOIN mailbox recipient ON recipient.id = mr.mailbox_id
        JOIN v_patient_events ev ON ev.event_id = m.event_id
        JOIN mailbox sender ON sender.id = m.sender_mailbox_id
        JOIN ophcomessaging_message_message_type t ON t.id = m.message_type_id
    WHERE
        m.deleted = 0;");
    }

    public function down()
    {
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
}
