<?php

class m191029_172959_move_sets_to_autoset extends CDbMigration
{
    /**
     * @return bool|void
     * @throws CDbException
     * @throws Exception
     */
    public function safeUp()
    {

            // convert medication set items to auto rules
            $this->execute("INSERT INTO medication_set_auto_rule_medication (
                medication_id,
                medication_set_id,
                include_children,
                include_parent,
                created_date,
                default_form_id,
                default_dose,
                default_route_id,
                default_dispense_location_id,
                default_dispense_condition_id,
                default_frequency_id,
                default_dose_unit_term,
                default_duration_id
                )
            SELECT 
                i.medication_id,
                i.medication_set_id,
                0 AS include_children,
                0 AS include_parent,
                i.created_date,
                i.default_form_id,
                i.default_dose,
                i.default_route_id,
                i.default_dispense_location_id,
                i.default_dispense_condition_id,
                i.default_frequency_id,
                IF (i.default_dose_unit_term='drop(s)', 'drop', i.default_dose_unit_term ),
                i.default_duration_id
            FROM medication_set_item i INNER JOIN medication_set s ON i.medication_set_id = s.id
            WHERE s.`automatic` = 0");

            // switch to automatic
            $this->execute("UPDATE medication_set SET `automatic` = 1 WHERE `automatic` = 0");

            // copy tapers to auto rules
            $this->execute(
            "INSERT INTO medication_set_auto_rule_medication_taper (
                medication_set_auto_rule_id,
                dose,
                frequency_id,
                duration_id
                )
                SELECT 
                    msr.id AS medication_set_auto_rule_id,
                    NULLIF(TRIM(REGEXP_REPLACE(t.dose, '[a-z -\]', '' )), '') AS 'dose', -- strip non-numerics
                    t.frequency_id,
                    t.duration_id
                FROM drug_set_item_taper AS t
                    INNER JOIN drug_set_item AS i ON i.id = t.item_id
                        INNER JOIN drug_set AS s ON i.drug_set_id = s.id
                            INNER JOIN medication_set ms ON ms.`name` = s.`name`
                                INNER JOIN medication_set_rule msu ON ms.id = msu.medication_set_id
                                INNER JOIN medication_set_auto_rule_medication msr ON msr.medication_set_id = ms.id
                            INNER JOIN medication m on m.id = msr.medication_id
                WHERE m.source_subtype = 'drug'
                    AND msu.usage_code_id = (SELECT id from medication_usage_code WHERE usage_code = 'PRESCRIPTION_SET')
                    AND m.source_old_id = i.drug_id
                    AND msu.subspecialty_id = s.subspecialty_id
                ");

            // Delete old manual entries (this will get regenerated later)
            $this->execute('SET foreign_key_checks = 0');
            $this->execute("TRUNCATE TABLE medication_set_item");
            $this->execute('SET foreign_key_checks = 1');
    }

    public function safeDown()
    {
        echo "m191029_172959_move_sets_to_autoset does not support migration down.\n";
    }
}
