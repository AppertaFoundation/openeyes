<?php

class m221013_105300_add_v_patient_base_iop_view extends CDbMigration
{
    public function safeUp()
    {
        $this->execute("CREATE OR REPLACE
        ALGORITHM = UNDEFINED VIEW `v_patient_base_iop` AS
        select
            `iop`.`patient_id` AS `patient_id`,
            `iop`.`event_id` AS `event_id`,
            `iop`.`event_date` AS `event_date`,
            `iop`.`reading_time` AS `reading_time`,
            `iop`.`value` AS `value`,
            `iop`.`Instrument` AS `instrument`,
            `iop`.`side` AS `side`,
            `iop`.`eye` AS `eye`,
            `iop`.`Comment` AS `comment`
        from
            (`v_patient_iop` `iop`
        left join `v_patient_iop` `x` on
            (`iop`.`patient_id` = `x`.`patient_id`
                and `iop`.`side` = `x`.`side`
                and `iop`.`event_date` > `x`.`event_date`))
        where
            `x`.`event_date` is null;");
    }

    public function safeDown()
    {
        $this->execute("DROP VIEW `v_patient_base_iop`");
    }
}
