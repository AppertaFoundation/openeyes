<?php

class m180510_093825_import_examination_events_to_medications extends CDbMigration
{

    /*
     * Insert Examination events to 'event_medication_use' table
     */
    private function insertExaminations( $events )
    {
        if($events){
            foreach($events as $event){
                $legacy_dose = explode(" ", $event['dose']);
                $dose = '';
                $dose_unit_term = '';
                
               
                if(count($legacy_dose) == 1){
                    $array = str_split($legacy_dose[0]);
                    foreach ($array as $key => $char) {
                        if(($char == '.') || ($char == '/') || (is_numeric($char))) {
                            $dose .=  $char;
                        } else {
                            $dose_unit_term .=  $char;
                        }
                    }
                } else {
                    $dose = $legacy_dose[0];
                    $dose_unit_term = $legacy_dose[1];
                }
                
                $event['ref_duration_id'] = (!isset($event['ref_duration_id'])) ? null : $event['ref_duration_id'];
                $event['ref_medication_form_id'] = (!isset($event['ref_medication_form_id'])) ? 'NULL' : $event['ref_medication_form_id'];
                $event['end_date'] = (!isset($event['end_date'])) ? $end_date_string = 'NULL' : $end_date_string = "'".$event['end_date']."'";
                        
                $ref_route_id = ($event['ref_route_id'] == null) ? 'NULL' : $event['ref_route_id'];
                $ref_frequency_id = ($event['ref_frequency_id'] == null) ? 'NULL' : $event['ref_frequency_id'];
                $ref_duration_id = ($event['ref_duration_id'] == null) ? 'NULL' : $event['ref_duration_id'];
                $stop_reason_id = ($event['stop_reason_id'] == null) ? 'NULL' : $event['stop_reason_id'];
                $prescription_item_id = ($event['prescription_item_id'] == null) ? 'NULL' : $event['prescription_item_id'];
               
                $command = Yii::app()->db
                ->createCommand("
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
                        start_date_string_YYYYMMDD,
                        end_date_string_YYYYMMDD
                    ) values(
                        ".$event['event_id'].",
                        '".$event['class_name']."',
                        'History',
                        ".$event['ref_medication_id'].",
                        ".$event['ref_medication_form_id'].", 
                        '".$event['ref_laterality_id']."', 
                        '".$dose."', 
                        '".$dose_unit_term."', 
                        ".$ref_route_id.",
                        ".$ref_frequency_id.",
                        ".$ref_duration_id.",
                        ".$stop_reason_id.",
                        '".$prescription_item_id."',
                        '".$event['event_date']."',"
                        .$end_date_string .")
                ");
                $command->execute();
                $command = null;
            }
        }
    }
    
    public function up()
	{
        echo "> The import may take a several seconds!\n";

        $this->execute("SET foreign_key_checks = 0");
        
        /*
         * Get Examination events with Drugs
         */
        $eventsDrugs = Yii::app()->db
                ->createCommand("
                    SELECT 
                        event.id                            AS event_id,
                        SUBSTRING(REPLACE(ohme.start_date, '-', ''), 1,8)   AS event_date,
                        SUBSTRING(REPLACE(ohme.end_date, '-', ''), 1,8)     AS end_date,
                        et.class_name,
                        ohme.dose,
                        ohme.units,
                        ohme.stop_reason_id,
                        medication_laterality.id        AS ref_laterality_id,
                        medication.id                   AS ref_medication_id,
                        medication_form.id              AS ref_medication_form_id,
                        medication_route.id             AS ref_route_id,
                        medication_frequency.id         AS ref_frequency_id,
                        medication_duration.id          AS ref_duration_id,
                        ( SELECT id FROM event_medication_use WHERE ohme.prescription_item_id = temp_prescription_item_id ) AS prescription_item_id
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
                    ORDER BY event.id ASC
                 ")
                ->queryAll();
        
        $this->insertExaminations($eventsDrugs);
        
        echo "> The Examinations with Drug import was successfully!\n";
        /*
         * Get Examination events with Medication Drugs
         */
        $eventMedDrugs = Yii::app()->db
                ->createCommand("
                    SELECT 
                        event.id                            AS event_id,
                        SUBSTRING(REPLACE(event.event_date, '-', ''), 1,8) AS event_date,
                        et.class_name,
                        ohme.dose,
                        ohme.units,
                        ohme.stop_reason_id,
                        medication_laterality.id        AS ref_laterality_id,
                        medication.id                   AS ref_medication_id,
                        medication_route.id             AS ref_route_id,
                        medication_frequency.id         AS ref_frequency_id,
                        ( SELECT id FROM event_medication_use WHERE ohme.prescription_item_id = temp_prescription_item_id ) AS prescription_item_id
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
                    AND medication.source_subtype = 'medication_drug'
                    ORDER BY event.id ASC
                 ")
                ->queryAll();
        
        $this->insertExaminations($eventMedDrugs);
        echo "> The Examinations with Medication Drug import was successfully!\n";
        $this->execute("SET foreign_key_checks = 1");
	}

	public function down()
	{
        $this->execute("SET foreign_key_checks = 0");    
        $this->execute("DELETE FROM event_medication_use WHERE usage_type = 'OphCiExamination' ");
        $this->execute("SET foreign_key_checks = 1");
	}
}