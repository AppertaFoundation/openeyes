<?php

class m200211_124300_fix_old_medication_views extends CDbMigration
{
    public function up()
    {
        $this->execute("DROP VIEW IF EXISTS medication_old;");

        $this->execute("CREATE OR REPLACE
        ALGORITHM = UNDEFINED VIEW `patient_medication_assignment` AS
        select
            `meds_entry`.`id` AS `id`,
            `latest`.`patient_id` AS `patient_id`,
             null AS `drug_id`,
            `meds_entry`.`medication_id` AS `medication_drug_id`,
            `meds`.`preferred_term` AS `medication_name`,
            `meds_entry`.`dose` AS `dose`,
            meds_entry.dose_unit_term AS `units`,
            `meds_entry`.`route_id` AS `route_id`,
            `meds_entry`.`laterality` AS `option_id`,
            `meds_entry`.`frequency_id` AS `frequency_id`,
            `meds_entry`.`start_date` AS `start_date`,
            `meds_entry`.`end_date` AS `end_date`,
            `meds_entry`.`stop_reason_id` AS `stop_reason_id`,
            `meds_entry`.`prescription_item_id` AS `prescription_item_id`,
            `meds_entry`.`last_modified_user_id` AS `last_modified_user_id`,
            `meds_entry`.`last_modified_date` AS `last_modified_date`,
            `meds_entry`.`created_user_id` AS `created_user_id`,
            `meds_entry`.`created_date` AS `created_date`
        from
            `event_medication_use` `meds_entry`
                JOIN medication meds ON meds.id = meds_entry.medication_id
                    JOIN  `latest_medication_examination_events` `latest` ON `meds_entry`.`event_id` = `latest`.`event_id`
        WHERE meds_entry.usage_type = 'OphCiExamination'
            AND meds_entry.usage_subtype = 'History';
        ");
    }

    public function down()
    {
        $this->execute("CREATE OR REPLACE
        ALGORITHM = UNDEFINED VIEW `patient_medication_assignment` AS
        select
            `meds_entry`.`id` AS `id`,
            `latest`.`patient_id` AS `patient_id`,
            `meds_entry`.`drug_id` AS `drug_id`,
            `meds_entry`.`medication_drug_id` AS `medication_drug_id`,
            `meds_entry`.`medication_name` AS `medication_name`,
            `meds_entry`.`dose` AS `dose`,
            `meds_entry`.`units` AS `units`,
            `meds_entry`.`route_id` AS `route_id`,
            `meds_entry`.`option_id` AS `option_id`,
            `meds_entry`.`frequency_id` AS `frequency_id`,
            `meds_entry`.`start_date` AS `start_date`,
            `meds_entry`.`end_date` AS `end_date`,
            `meds_entry`.`stop_reason_id` AS `stop_reason_id`,
            `meds_entry`.`prescription_item_id` AS `prescription_item_id`,
            `meds_entry`.`last_modified_user_id` AS `last_modified_user_id`,
            `meds_entry`.`last_modified_date` AS `last_modified_date`,
            `meds_entry`.`created_user_id` AS `created_user_id`,
            `meds_entry`.`created_date` AS `created_date`
        from
            ((`ophciexamination_history_medications_entry` `meds_entry`
        join `et_ophciexamination_history_medications` `element` on
            ((`meds_entry`.`element_id` = `element`.`id`)))
        join `latest_medication_examination_events` `latest` on
            ((`element`.`event_id` = `latest`.`event_id`)))");
    }
}
