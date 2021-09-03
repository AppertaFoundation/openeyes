<?php

class m200623_052130_add_crt_and_refraction_views extends CDbMigration
{
    public function up()
    {
        $this->execute("CREATE OR REPLACE
        ALGORITHM = UNDEFINED VIEW `v_patient_crt` AS
        select
            `e`.`patient_id` AS `patient_id`,
            `oct`.`event_id` AS `event_id`,
            `e`.`event_date` AS `event_date`,
            `oct`.`left_crt` AS `value`,
            `m`.`name` AS `method`,
            1 AS `side`,
            'L' AS `eye`
        from
            ((`et_ophciexamination_oct` `oct`
        join `v_patient_events` `e` on
            ((`oct`.`event_id` = `e`.`event_id`)))
        join `ophciexamination_oct_method` `m` on
            ((`oct`.`left_method_id` = `m`.`id`)))
        union
        select
            `e`.`patient_id` AS `patient_id`,
            `oct`.`event_id` AS `event_id`,
            `e`.`event_date` AS `event_date`,
            `oct`.`right_crt` AS `value`,
            `m`.`name` AS `method`,
            0 AS `side`,
            'R' AS `eye`
        from
            ((`et_ophciexamination_oct` `oct`
        join `v_patient_events` `e` on
            ((`oct`.`event_id` = `e`.`event_id`)))
        join `ophciexamination_oct_method` `m` on
            ((`oct`.`right_method_id` = `m`.`id`)))
        order by
            1,
            3");

        $this->execute("CREATE OR REPLACE
        ALGORITHM = UNDEFINED VIEW `v_patient_refraction` AS
        select
            `e`.`patient_id` AS `patient_id`,
            `r`.`event_id` AS `event_id`,
            `e`.`event_date` AS `event_date`,
            `rr`.`sphere` AS `value`,
            `t`.`name` AS `type`,
            1 AS `side`,
            'L' AS `eye`
        from
            (
                `et_ophciexamination_refraction` `r`
                join `v_patient_events` `e` on
                (`r`.`event_id` = `e`.`event_id`)
                join `ophciexamination_refraction_reading` rr ON rr.id = (
                    SELECT single_reading.id
                    FROM ophciexamination_refraction_reading single_reading
                    LEFT JOIN ophciexamination_refraction_type rt
                    ON single_reading.type_id = rt.id
                    WHERE element_id = r.id
                    AND single_reading.eye_id = 1
                    ORDER BY -rt.priority DESC
                    LIMIT 1
                )
                join `ophciexamination_refraction_type` t on
                rr.type_id = t.id
            )
        union
        select
            `e`.`patient_id` AS `patient_id`,
            `r`.`event_id` AS `event_id`,
            `e`.`event_date` AS `event_date`,
            `rr`.`sphere` AS `value`,
            `t`.`name` AS `type`,
            0 AS `side`,
            'R' AS `eye`
        from
            (
                `et_ophciexamination_refraction` `r`
                join `v_patient_events` `e` on
                `r`.`event_id` = `e`.`event_id`
                join `ophciexamination_refraction_reading` rr ON rr.id = (
                    SELECT single_reading.id
                    FROM ophciexamination_refraction_reading single_reading
                    LEFT JOIN ophciexamination_refraction_type rt
                    ON single_reading.type_id = rt.id
                    WHERE element_id = r.id
                    AND single_reading.eye_id = 2
                    ORDER BY -rt.priority DESC
                    LIMIT 1
                )
                join `ophciexamination_refraction_type` `t` on
                rr.type_id = t.id
            )
        order by
            1,
            3");
    }

    public function down()
    {
        $this->execute('DROP VIEW v_patient_refraction');
        $this->execute('DROP VIEW v_patient_crt');
    }
}
