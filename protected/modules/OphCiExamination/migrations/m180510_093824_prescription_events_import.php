<?php

/**
 * Class m180510093824_prescription_events_import
 *
 * This is not the right place for this migration, but we must ensure
 * that it runs right before the examination import.
 */

class m180510_093824_prescription_events_import extends OEMigration
{
    public function safeUp()
    {
        echo "> This migration may take a several seconds!\n";

        $this->dropTapersFK();
        $this->runPrescriptionImport();
        $this->applyTapersFK();

        return true;
    }

    private function dropTapersFK()
    {
        $this->execute("
            SET SESSION sql_mode = 'NO_ENGINE_SUBSTITUTION,NO_AUTO_CREATE_USER';");
        $this->execute("
            IF EXISTS(
                SELECT * 
                FROM information_schema.TABLE_CONSTRAINTS 
                WHERE
                CONSTRAINT_SCHEMA = DATABASE()
                    AND TABLE_NAME        = 'ophdrprescription_item_taper' 
                    AND CONSTRAINT_NAME   = 'ophdrprescription_item_taper_item_id_fk' 
                    AND CONSTRAINT_TYPE   = 'FOREIGN KEY')
            THEN
                ALTER TABLE `ophdrprescription_item_taper` DROP FOREIGN KEY `ophdrprescription_item_taper_item_id_fk`;
                ALTER TABLE `ophdrprescription_item_taper` DROP INDEX `ophdrprescription_item_taper_item_id_fk` ;
            END IF;");
        $this->alterOEColumn('ophdrprescription_item_taper', 'item_id', 'INT NOT NULL', true);
        return $this;
    }

    private function runPrescriptionImport()
    {
        $this->execute("SET SESSION sql_mode = 'NO_ENGINE_SUBSTITUTION,NO_AUTO_CREATE_USER';");
        $this->execute(
        "
        INSERT INTO event_medication_use(
                                event_id, 
                                usage_type, 
                                medication_id, 
                                form_id, 
                                laterality,
                                dose,
                                dose_unit_term,
                                route_id, 
                                frequency_id, 
                                duration_id,
                                dispense_location_id, 
                                dispense_condition_id,  
                                start_date,
                                temp_prescription_item_id,
                                comments
                            )
        SELECT 
            event.id AS event_id,
            et.class_name AS usage_type,
            medication.id AS medication_id,
            medication_form.id AS form_id,
            medication_laterality.id AS laterality,
            REPLACE(REGEXP_REPLACE(REPLACE(presc_item.dose, '1/2', '0.5'), 'half', '0.5'), ',', '.') + 0 AS dose, -- +0 implicitly casts to a numberic and strips trailing alpha characters
            REGEXP_REPLACE(REGEXP_REPLACE(REGEXP_REPLACE(presc_item.dose, '\\\\d|\\\\s|\\\\.|/|half(\\\\s*a)?|-|=|s/r|,', ''), 'tabletmg', 'tablet'), 'mgbd', 'mg') AS dose_unit_term, -- Lots of cleanup for mistyped dosages
            medication_route.id AS route_id,
            medication_frequency.id AS frequency_id,
            dd.id AS duration_id,
            presc_item.dispense_location_id AS dispense_location_id,
            presc_item.dispense_condition_id AS dispense_condition_id,
            SUBSTRING(REPLACE(presc_item.created_date, '-', ''), 1,8) AS start_date,
            presc_item.id AS temp_prescription_item_id,
            presc_item.comments AS comments
        FROM event 
        JOIN event_type                                 AS et               ON event.event_type_id = et.id
        LEFT JOIN et_ophdrprescription_details          AS prescDetails     ON event.id = prescDetails.event_id
        LEFT JOIN ophdrprescription_item                AS presc_item       ON prescDetails.id = presc_item.prescription_id
        LEFT JOIN drug                                  AS d                ON presc_item.drug_id = d.id
        LEFT JOIN drug_form                             AS df               ON d.form_id = df.id
        LEFT JOIN drug_route                            AS dr               ON presc_item.route_id = dr.id
        LEFT JOIN drug_frequency                        AS dfreq            ON presc_item.frequency_id = dfreq.id
        LEFT JOIN drug_duration                         AS dd               ON presc_item.duration_id = dd.id
        LEFT JOIN drug_route_option                     AS dro              ON presc_item.route_option_id = dro.id
        LEFT JOIN medication                                            ON medication.source_old_id = presc_item.drug_id
        LEFT JOIN medication_form                                       ON medication_form.term = df.name
        LEFT JOIN medication_route                                      ON medication_route.term = dr.name
        LEFT JOIN medication_frequency                                  ON medication_frequency.original_id = dfreq.id
        LEFT JOIN medication_laterality                                 ON medication_laterality.name = dro.name
        WHERE et.name = 'Prescription'
        AND medication.source_type = 'LEGACY'
        AND medication.source_subtype = 'drug'");

        $last_id = $this->dbConnection->getLastInsertID();

        $this->execute("UPDATE ophdrprescription_item_taper AS t INNER JOIN event_medication_use u ON t.item_id = u.temp_prescription_item_id
        SET t.item_id = u.id");

        $this->execute("UPDATE ophdrprescription_item_taper_version AS t INNER JOIN event_medication_use u ON t.item_id = u.temp_prescription_item_id
        SET t.item_id = u.id");
    }

    private function applyTapersFK()
    {
        $this->execute("SET SESSION sql_mode = 'NO_ENGINE_SUBSTITUTION,NO_AUTO_CREATE_USER';");
        $this->execute("
        ALTER TABLE ophdrprescription_item_taper ADD CONSTRAINT ophdrprescription_item_taper_item_id_fk FOREIGN KEY (item_id) 
        REFERENCES event_medication_use(id);");
    }

    public function safeDown()
    {
        $this->execute("SET foreign_key_checks = 0");
        $this->execute("DELETE FROM event_medication_use WHERE usage_type = 'OphDrPrescription' ");
        $this->execute("SET foreign_key_checks = 1");

        $this->dropForeignKey('ophdrprescription_item_taper_item_id_fk', 'ophdrprescription_item_taper');
        $this->alterOEColumn('ophdrprescription_item_taper', 'item_id', 'INT(10) UNSIGNED NOT NULL');

        $this->execute("UPDATE ophdrprescription_item_taper SET item_id = (SELECT temp_prescription_item_id FROM event_medication_use WHERE id = ophdrprescription_item_taper.item_id)");

        $this->addForeignKey('ophdrprescription_item_taper_item_id_fk', 'ophdrprescription_item_taper', 'item_id', 'ophdrprescription_item', 'id');
    }
}
