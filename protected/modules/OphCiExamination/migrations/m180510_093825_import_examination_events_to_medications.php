<?php

class m180510_093825_import_examination_events_to_medications extends CDbMigration
{

    /*
     * Insert Examination events to 'event_medication_use' table
     */
    public function up()
    {

        echo "> The import may take a several seconds...\n";



        /*
         * Import Examination events with Drugs
         */
        $this->execute("SET SESSION sql_mode = 'NO_ENGINE_SUBSTITUTION,NO_AUTO_CREATE_USER';");
        $this->execute(
                "
            INSERT INTO event_medication_use (
                    event_id, 
                    usage_type, 
                    usage_subtype,
                    medication_id, 
                    form_id, 
                    laterality,
                    dose,
                    dose_unit_term,
                    route_id, 
                    frequency_id, 
                    duration_id, 
                    stop_reason_id,
                    prescription_item_id,
                    start_date,
                    end_date,
                    stopped_in_event_id
                )   			
                SELECT 
                    event.id AS event_id,
                    et.class_name AS usage_type,
                    'History' AS usage_subtype, 
                    medication.id AS medication_id,
                    medication_form.id  AS form_id,
                    medication_laterality.id AS laterality,
                    -- ohme.dose AS legacy_dose, -- left commented for debugging
                    REPLACE(REGEXP_REPLACE(REPLACE(ohme.dose, '1/2', '0.5'), 'half', '0.5'), ',', '.') + 0 AS dose, -- +0 implicitly casts to a numberic and strips trailing alpha characters
                    CASE 
                        WHEN (ohme.units IS NULL OR ohme.units = '') THEN  
                            REGEXP_REPLACE(REGEXP_REPLACE(REGEXP_REPLACE(ohme.dose, '\\\\d|\\\\s|\\\\.|/|half(\\\\s*a)?|-|=|s/r|,', ''), 'tabletmg', 'tablet'), 'mgbd', 'mg') -- Lots of cleanup for mistyped dosages
                        ELSE
                            ohme.units
                    END  AS dose_unit_term,
                    medication_route.id AS route_id,
                    medication_frequency.id AS frequency_id,
                    medication_duration.id  AS duration_id,
                    ohme.stop_reason_id,
                    ( SELECT id FROM event_medication_use WHERE ohme.prescription_item_id = temp_prescription_item_id ) AS prescription_item_id,
                    SUBSTRING(REPLACE(ohme.start_date, '-', ''), 1,8)   AS start_date,
                    SUBSTRING(REPLACE(ohme.end_date, '-', ''), 1,8)     AS end_date,
                    CASE
                        WHEN (DATE(ohme.end_date) < DATE(NOW())) THEN
                            event.id
                        ELSE
                            NULL
                    END AS stopped_in_event_id
                FROM event 
                LEFT JOIN event_type                                    AS et           ON event.event_type_id = et.id
                LEFT JOIN et_ophciexamination_history_medications       AS ehm          ON event.id = ehm.event_id
                LEFT JOIN ophciexamination_history_medications_entry    AS ohme         ON ehm.id = ohme.element_id
                LEFT JOIN drug                                          AS d            ON ohme.drug_id = d.id
                LEFT JOIN drug_form                                     AS df           ON d.form_id = df.id
                LEFT JOIN drug_route                                    AS dr           ON ohme.route_id = dr.id
                LEFT JOIN drug_frequency                                AS dfreq        ON ohme.frequency_id = dfreq.id
                LEFT JOIN drug_route_option                             AS dro          ON ohme.option_id = dro.id
                LEFT JOIN drug_duration                                 AS dd           ON d.default_duration_id = dd.id
                LEFT JOIN medication                                                ON medication.source_old_id = ohme.drug_id 
                LEFT JOIN medication_form                                           ON medication_form.term = df.name
                LEFT JOIN medication_route                                          ON medication_route.term = dr.name
                LEFT JOIN medication_frequency                                      ON medication_frequency.original_id = dfreq.id
                LEFT JOIN medication_laterality                                     ON medication_laterality.name = dro.name
                LEFT JOIN medication_duration                                       ON medication_duration.name = dd.name
                WHERE et.name = 'Examination'
                AND medication.source_type = 'LEGACY'
                AND medication.source_subtype = 'drug'
                "
        );

        echo "> Examinations with Drugs imported successfully\n";

        /*
         * Import Examination events with Medication Drugs
         */
        $this->execute("SET SESSION sql_mode = 'NO_ENGINE_SUBSTITUTION,NO_AUTO_CREATE_USER';");
        $this->execute(
        "
        INSERT INTO event_medication_use (
                event_id, 
                usage_type, 
                usage_subtype,
                medication_id, 
                form_id, 
                laterality,
                dose,
                dose_unit_term,
                route_id, 
                frequency_id, 
                duration_id, 
                stop_reason_id,
                prescription_item_id,
                start_date,
                end_date
            )   			
        SELECT 
            event.id AS event_id,
            et.class_name AS usage_type,
            'History' AS usage_subtype,
            medication.id AS medication_id,
            NULL as form_id,
            medication_laterality.id AS laterality,
            REPLACE(REGEXP_REPLACE(REPLACE(ohme.dose, '1/2', '0.5'), 'half', '0.5'), ',', '.') + 0 AS dose, -- +0 implicitly casts to a numberic and strips trailing alpha characters
                CASE 
                    WHEN (ohme.units IS NULL OR ohme.units = '') THEN 
                        REGEXP_REPLACE(REGEXP_REPLACE(REGEXP_REPLACE(ohme.dose, '\\\\d|\\\\s|\\\\.|/|half(\\\\s*a)?|-|=|s/r|,', ''), 'tabletmg', 'tablet'), 'mgbd', 'mg') -- Lots of cleanup for mistyped dosages
                    ELSE
                        ohme.units
                END  AS dose_unit_term,
            medication_route.id AS route_id,
            medication_frequency.id AS frequency_id,
            NULL AS duration_id,
            ohme.stop_reason_id,
            ( SELECT id FROM event_medication_use WHERE ohme.prescription_item_id = temp_prescription_item_id ) AS prescription_item_id,
            SUBSTRING(REPLACE(ohme.start_date, '-', ''), 1,8) AS start_date,
            SUBSTRING(REPLACE(ohme.end_date, '-', ''), 1,8) AS end_date
        FROM event 
        LEFT JOIN event_type                                    AS et           ON event.event_type_id = et.id
        LEFT JOIN et_ophciexamination_history_medications       AS ehm          ON event.id = ehm.event_id
        LEFT JOIN ophciexamination_history_medications_entry    AS ohme         ON ehm.id = ohme.element_id
        LEFT JOIN medication_drug                               AS md           ON ohme.medication_drug_id = md.id
        LEFT JOIN drug_route                                    AS dr           ON ohme.route_id = dr.id
        LEFT JOIN drug_frequency                                AS dfreq        ON ohme.frequency_id = dfreq.id
        LEFT JOIN drug_route_option                             AS dro          ON ohme.option_id = dro.id
        LEFT JOIN medication                                                ON medication.source_old_id = ohme.medication_drug_id 
        LEFT JOIN medication_route                                          ON medication_route.term = dr.name
        LEFT JOIN medication_frequency                                      ON medication_frequency.original_id = dfreq.id
        LEFT JOIN medication_laterality                                     ON medication_laterality.name = dro.name
        WHERE et.name = 'Examination'
        AND medication.source_type = 'LEGACY'
        AND medication.source_subtype = 'medication_drug'");
        echo "> Examinations with Medication Drugs imported successfully!\n";
    }
    public function down()
    {
        $this->execute("SET foreign_key_checks = 0");
        $this->execute("DELETE FROM event_medication_use WHERE usage_type = 'OphCiExamination' ");
        $this->execute("SET foreign_key_checks = 1");
    }
}
