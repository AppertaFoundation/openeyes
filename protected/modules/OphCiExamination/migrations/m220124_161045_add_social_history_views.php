<?php

class m220124_161045_add_social_history_views extends OEMigration
{
    public function safeUp()
    {
        $this->dbConnection->createCommand("CREATE OR REPLACE
                                            ALGORITHM = UNDEFINED VIEW `socialhistory_examination_events` AS
                                            select
                                                `event`.`event_date` AS `event_date`,
                                                `event`.`created_date` AS `created_date`,
                                                `et`.`event_id` AS `event_id`,
                                                `episode`.`patient_id` AS `patient_id`
                                            from
                                                ((`et_ophciexamination_socialhistory` `et`
                                            join `event` on
                                                (`et`.`event_id` = `event`.`id`))
                                            join `episode` on
                                                (`event`.`episode_id` = `episode`.`id`
                                                    and `event`.`deleted` = 0))")->execute();

        $this->dbConnection->createCommand("CREATE OR REPLACE
                                            ALGORITHM = UNDEFINED VIEW `latest_socialhistory_examination_events` AS
                                            select
                                                `t1`.`event_id` AS `event_id`,
                                                `t1`.`patient_id` AS `patient_id`
                                            from
                                                (`socialhistory_examination_events` `t1`
                                            left join `socialhistory_examination_events` `t2` on
                                                (`t1`.`patient_id` = `t2`.`patient_id`
                                                    and (`t1`.`event_date` < `t2`.`event_date`
                                                        or `t1`.`event_date` = `t2`.`event_date`
                                                        and `t1`.`created_date` < `t2`.`created_date`)))
                                            where
                                                `t2`.`patient_id` is null")->execute();

        $this->dbConnection->createCommand("CREATE OR REPLACE
                                            ALGORITHM = UNDEFINED VIEW `socialhistory` AS
                                            select
                                                `element`.`id` AS `id`,
                                                `latest`.`patient_id` AS `patient_id`,
                                                `element`.`id` AS `socialhistory_id`,
                                                `element`.`occupation_id` AS `occupation_id`,
                                                `element`.`smoking_status_id` AS `smoking_status_id`,
                                                `element`.`accommodation_id` AS `accommodation_id`,
                                                `element`.`carer_id` AS `carer_id`,
                                                `element`.`substance_misuse_id`,
                                                `element`.`alcohol_intake`,
                                                `element`.`comments`,
                                                `element`.`type_of_job`,
                                                `element`.`last_modified_user_id` AS `last_modified_user_id`,
                                                `element`.`last_modified_date` AS `last_modified_date`,
                                                `element`.`created_user_id` AS `created_user_id`,
                                                `element`.`created_date` AS `created_date`
                                            from
                                                (`et_ophciexamination_socialhistory` `element`
                                            join `latest_socialhistory_examination_events` `latest` on
                                                (`element`.`event_id` = `latest`.`event_id`))")->execute();
    }

    public function safeDown()
    {
        $this->execute("DROP VIEW IF EXISTS patient_socialhistory_assignment;");
        $this->execute("DROP VIEW IF EXISTS latest_socialhistory_examination_events");
        $this->execute("DROP VIEW IF EXISTS socialhistory_examination_events");
    }
}
