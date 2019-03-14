<?php

class m190303_184800_add_report_views extends CDbMigration
{
	public function safeUp()
	{
	    $this->execute("CREATE OR REPLACE VIEW `v_patient_va` AS
    SELECT
        `pe`.`patient_id` AS `patient_id`,
        `eva`.`event_id` AS `event_id`,
        `pe`.`event_date` AS `reading_date`,
        `eva`.`eye_id` AS `eye_id`,
        `r`.`side` AS `side`,
        `r`.`value` AS `base_value`,
        `eva`.`unit_id` AS `unit_id`,
        `m`.`id` AS `method_id`,
        (CASE `r`.`side`
            WHEN 0 THEN 'R'
            WHEN 1 THEN 'L'
        END) AS `eye`,
        `u`.`name` AS `unit_name`,
        `v`.`value` AS `value`,
        `m`.`name` AS `method`
    FROM
        (((((`et_ophciexamination_visualacuity` `eva`
        JOIN `ophciexamination_visualacuity_reading` `r` ON ((`r`.`element_id` = `eva`.`id`)))
        JOIN `ophciexamination_visual_acuity_unit_value` `v` ON ((`v`.`base_value` = `r`.`value`)))
        JOIN `ophciexamination_visual_acuity_unit` `u` ON ((`u`.`id` = `v`.`unit_id`)))
        JOIN `v_patient_events` `pe` ON ((`pe`.`event_id` = `eva`.`event_id`)))
        JOIN `ophciexamination_visualacuity_method` `m` ON ((`r`.`method_id` = `m`.`id`)))
    WHERE
        (`v`.`unit_id` = `eva`.`unit_id`)
    ORDER BY `pe`.`patient_id` , `pe`.`event_date` , `pe`.`event_created_date` , `pe`.`event_last_modified_date`;");


	    $this->execute("CREATE OR REPLACE VIEW `v_patient_va_converted` AS
    SELECT
        `pe`.`patient_id` AS `patient_id`,
        `eva`.`event_id` AS `event_id`,
        `pe`.`event_date` AS `reading_date`,
        `eva`.`eye_id` AS `eye_id`,
        `r`.`side` AS `side`,
        `r`.`value` AS `base_value`,
        `eva`.`unit_id` AS `original_unit_id`,
        `m`.`id` AS `method_id`,
        (CASE `r`.`side`
            WHEN 0 THEN 'R'
            WHEN 1 THEN 'L'
        END) AS `eye`,
        `u`.`name` AS `original_unit_name`,
        `v`.`value` AS `original_value`,
        `m`.`name` AS `method`,
        (SELECT
                `sub`.`value`
            FROM
                `ophciexamination_visual_acuity_unit_value` `sub`
            WHERE
                (`sub`.`unit_id` = (SELECT
                        `ophciexamination_visual_acuity_unit`.`id`
                    FROM
                        `ophciexamination_visual_acuity_unit`
                    WHERE
                        (LCASE(`ophciexamination_visual_acuity_unit`.`name`) = LCASE('Snellen Metre'))))
            ORDER BY ABS((CAST(`sub`.`base_value` AS SIGNED) - CAST(`v`.`base_value` AS SIGNED)))
            LIMIT 1) AS `snellen_value`,
        (SELECT
                `sub`.`value`
            FROM
                `ophciexamination_visual_acuity_unit_value` `sub`
            WHERE
                (`sub`.`unit_id` = (SELECT
                        `ophciexamination_visual_acuity_unit`.`id`
                    FROM
                        `ophciexamination_visual_acuity_unit`
                    WHERE
                        (LCASE(`ophciexamination_visual_acuity_unit`.`name`) = LCASE('ETDRS Letters'))))
            ORDER BY ABS((CAST(`sub`.`base_value` AS SIGNED) - CAST(`v`.`base_value` AS SIGNED)))
            LIMIT 1) AS `ETDRS_value`,
        (SELECT
                `sub`.`value`
            FROM
                `ophciexamination_visual_acuity_unit_value` `sub`
            WHERE
                (`sub`.`unit_id` = (SELECT
                        `ophciexamination_visual_acuity_unit`.`id`
                    FROM
                        `ophciexamination_visual_acuity_unit`
                    WHERE
                        (LCASE(`ophciexamination_visual_acuity_unit`.`name`) = LCASE('LogMAR single-letter'))))
            ORDER BY ABS((CAST(`sub`.`base_value` AS SIGNED) - CAST(`v`.`base_value` AS SIGNED)))
            LIMIT 1) AS `LogMAR_value`
    FROM
        (((((`et_ophciexamination_visualacuity` `eva`
        JOIN `ophciexamination_visualacuity_reading` `r` ON ((`r`.`element_id` = `eva`.`id`)))
        JOIN `ophciexamination_visual_acuity_unit_value` `v` ON ((`v`.`base_value` = `r`.`value`)))
        JOIN `ophciexamination_visual_acuity_unit` `u` ON ((`u`.`id` = `v`.`unit_id`)))
        JOIN `v_patient_events` `pe` ON ((`pe`.`event_id` = `eva`.`event_id`)))
        JOIN `ophciexamination_visualacuity_method` `m` ON ((`r`.`method_id` = `m`.`id`)))
    WHERE
        (`v`.`unit_id` = `eva`.`unit_id`)
    ORDER BY `pe`.`patient_id` , `pe`.`event_date` , `pe`.`event_created_date` , `pe`.`event_last_modified_date`;");
	}

	public function safeDown()
	{
		$this->execute("DROP VIEW v_patient_va");
		$this->execute("DROP VIEW v_patient_va_converted");
	}
}
