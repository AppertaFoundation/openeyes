<?php
/**
 * OpenEyes.
 *
 * (C) OpenEyes Foundation, 2019
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2019, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
class m190304_104100_add_report_views extends CDbMigration
{
    public function safeUp()
    {
        $this->execute("CREATE OR REPLACE VIEW `v_patient_messages` AS
    SELECT
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
        `u`.`last_name` AS `TO_lastname`,
        `ev`.`patient_first_name` AS `patient_firstname`,
        `ev`.`patient_last_name` AS `patient_lastname`,
        `ev`.`hos_num` AS `hos_num`,
        `ev`.`nhs_num` AS `nhs_num`
    FROM
        ((((`et_ophcomessaging_message` `m`
        JOIN `user` `u` ON ((`u`.`id` = `m`.`for_the_attention_of_user_id`)))
        JOIN `v_patient_events` `ev` ON ((`ev`.`event_id` = `m`.`event_id`)))
        JOIN `user` `uu` ON ((`uu`.`id` = `m`.`last_modified_user_id`)))
        JOIN `ophcomessaging_message_message_type` `t` ON ((`t`.`id` = `m`.`message_type_id`)))
    WHERE
        (`m`.`deleted` = 0);");

    }

    public function safeDown()
    {
        $this->execute("DROP VIEW v_patient_messages");
    }
}
