<?php

class m180510_093825_import_examination_events_to_medications extends CDbMigration
{
    /*
     * Prescription events import 
     */
    private function runPrescriptionImport()
    {
        $events = Yii::app()->db
                ->createCommand("
                    SELECT 
                        event.id                            AS event_id,
                        SUBSTRING(REPLACE(presc_item.created_date, '-', ''), 1,8) AS event_date,
                        et.class_name,
                        presc_item.id                       AS temp_prescription_item_id,
                        presc_item.dose                     AS legacy_dose,
                        presc_item.dispense_condition_id    AS ref_dispense_condition_id,
                        presc_item.dispense_location_id     AS ref_dispense_location_id,  
                        ref_medication_laterality.id        AS ref_laterality_id,
                        ref_medication.id                   AS ref_medication_id,
                        ref_medication_form.id              AS ref_medication_form_id,
                        ref_medication_route.id             AS ref_route_id,
                        ref_medication_frequency.id         AS ref_frequency_id,
                        ref_medication_duration.id          AS ref_duration_id
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
                    LEFT JOIN ref_medication                                            ON ref_medication.source_old_id = presc_item.drug_id
                    LEFT JOIN ref_medication_form                                       ON ref_medication_form.term = df.name
                    LEFT JOIN ref_medication_route                                      ON ref_medication_route.term = dr.name
                    LEFT JOIN ref_medication_frequency                                  ON ref_medication_frequency.original_id = dfreq.id
                    LEFT JOIN ref_medication_duration                                   ON ref_medication_duration.name = dd.name
                    LEFT JOIN ref_medication_laterality                                 ON ref_medication_laterality.name = dro.name
                    WHERE et.name = 'Prescription'
                    AND ref_medication.source_type = 'LEGACY'
                    AND ref_medication.source_subtype = 'drug'
                    ORDER BY event.id ASC
                 ")
                ->queryAll();
       
        if($events){
            foreach($events as $event){
                
                $legacy_dose = explode(" ", $event['legacy_dose']);
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
                    //var_dump($dose.' : '.$dose_unit_term);
                } else if(count($legacy_dose) == 2) {
                    $dose = $legacy_dose[0];
                    $dose_unit_term = $legacy_dose[1];
                    
                } else {
                    $dose = $legacy_dose[0];
                    for($i = 1; $i < count($legacy_dose); $i++){
                        $dose_unit_term .= $legacy_dose[$i].' ';
                    }
                }
               
                if((strtolower($dose) == 'half') || ($dose == '1/2')) {
                    $dose = '0.5';
                }
                
                $dose = str_replace(',', '.', $dose);
                  
                $ref_route_id = ($event['ref_route_id'] == null) ? 'NULL' : $event['ref_route_id'];
                $ref_frequency_id = ($event['ref_frequency_id'] == null) ? 'NULL' : $event['ref_frequency_id'];
                $ref_duration_id = ($event['ref_duration_id'] == null) ? 'NULL' : $event['ref_duration_id'];
                $ref_dispense_condition_id = ($event['ref_dispense_condition_id'] == null) ? 'NULL' : $event['ref_dispense_condition_id'];
                $ref_dispense_location_id = ($event['ref_dispense_location_id'] == null) ? 'NULL' : $event['ref_dispense_location_id'];
                
                $command = Yii::app()->db
                ->createCommand("
                    INSERT INTO event_medication_uses(
                        event_id, 
                        usage_type, 
                        ref_medication_id, 
                        form_id, 
                        laterality,
                        dose,
                        dose_unit_term,
                        route_id, 
                        frequency_id, 
                        duration, 
                        dispense_location_id, 
                        dispense_condition_id,  
                        temp_prescription_item_id, 
                        start_date_string_YYYYMMDD
                    ) values(
                        ".$event['event_id'].",
                        '".$event['class_name']."',
                        ".$event['ref_medication_id'].",
                        ".$event['ref_medication_form_id'].", 
                        '".$event['ref_laterality_id']."', 
                        '".$dose."', 
                        '".$dose_unit_term."', 
                        ".$ref_route_id.",
                        ".$ref_frequency_id.",
                        ".$ref_duration_id.",
                        ".$ref_dispense_condition_id.",
                        ".$ref_dispense_location_id.",
                        ".$event['temp_prescription_item_id'].",
                        '".$event['event_date']."' )
                ");
                $command->execute();
                $command = null;
            }
        }
    }
    
     /*
     * Prescription events import down
     */
    private function downPrescriptionImport()
    {
        $this->execute("SET foreign_key_checks = 0");
        
        $this->execute("DELETE FROM event_medication_uses WHERE usage_type = 'OphDrPrescription' ");
        $this->execute("SET foreign_key_checks = 1");
    }
    
    /*
     * Insert Examination events to 'event_medication_uses' table
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
                $event['end_date'] = (!isset($event['end_date'])) ? null : $event['end_date'];
                        
                $ref_route_id = ($event['ref_route_id'] == null) ? 'NULL' : $event['ref_route_id'];
                $ref_frequency_id = ($event['ref_frequency_id'] == null) ? 'NULL' : $event['ref_frequency_id'];
                $ref_duration_id = ($event['ref_duration_id'] == null) ? 'NULL' : $event['ref_duration_id'];
                $stop_reason_id = ($event['stop_reason_id'] == null) ? 'NULL' : $event['stop_reason_id'];
                $prescription_item_id = ($event['prescription_item_id'] == null) ? 'NULL' : $event['prescription_item_id'];
               
                $command = Yii::app()->db
                ->createCommand("
                    INSERT INTO event_medication_uses(
                        event_id, 
                        usage_type, 
                        ref_medication_id, 
                        form_id, 
                        laterality,
                        dose,
                        dose_unit_term,
                        route_id, 
                        frequency_id, 
                        duration, 
                        stop_reason_id,
                        prescription_item_id,
                        start_date_string_YYYYMMDD,
                        end_date_string_YYYYMMDD
                    ) values(
                        ".$event['event_id'].",
                        '".$event['class_name']."',
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
                        '".$event['event_date']."',
                        '".$event['end_date']."' )
                ");
                $command->execute();
                $command = null;
            }
        }
    }
    
    public function up()
	{
        echo "> The import may take a several seconds!\n";
        
        if( Yii::app()->getModule('OphDrPrescription') ){
            $this->runPrescriptionImport();
            echo "> The Prescription events import is done!\n";
        }
        
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
                        ref_medication_laterality.id        AS ref_laterality_id,
                        ref_medication.id                   AS ref_medication_id,
                        ref_medication_form.id              AS ref_medication_form_id,
                        ref_medication_route.id             AS ref_route_id,
                        ref_medication_frequency.id         AS ref_frequency_id,
                        ref_medication_duration.id          AS ref_duration_id,
                        ( SELECT id FROM event_medication_uses WHERE ohme.prescription_item_id = temp_prescription_item_id ) AS prescription_item_id
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
                    LEFT JOIN ref_medication                                                ON ref_medication.source_old_id = ohme.drug_id 
                    LEFT JOIN ref_medication_form                                           ON ref_medication_form.term = df.name
                    LEFT JOIN ref_medication_route                                          ON ref_medication_route.term = dr.name
                    LEFT JOIN ref_medication_frequency                                      ON ref_medication_frequency.original_id = dfreq.id
                    LEFT JOIN ref_medication_laterality                                     ON ref_medication_laterality.name = dro.name
                    LEFT JOIN ref_medication_duration                                       ON ref_medication_duration.name = dd.name
                    WHERE et.name = 'Examination'
                    AND ref_medication.source_type = 'LEGACY'
                    AND ref_medication.source_subtype = 'drug'
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
                        ref_medication_laterality.id        AS ref_laterality_id,
                        ref_medication.id                   AS ref_medication_id,
                        ref_medication_route.id             AS ref_route_id,
                        ref_medication_frequency.id         AS ref_frequency_id,
                        ( SELECT id FROM event_medication_uses WHERE ohme.prescription_item_id = temp_prescription_item_id ) AS prescription_item_id
                    FROM event 
                    LEFT JOIN event_type                                    AS et           ON event.event_type_id = et.id
                    LEFT JOIN et_ophciexamination_history_medications       AS ehm          ON event.id = ehm.event_id
                    LEFT JOIN ophciexamination_history_medications_entry    AS ohme         ON ehm.id = ohme.element_id
                    LEFT JOIN medication_drug                               AS md           ON ohme.medication_drug_id = md.id
                    LEFT JOIN drug_route                                    AS dr           ON ohme.route_id = dr.id
                    LEFT JOIN drug_frequency                                AS dfreq        ON ohme.frequency_id = dfreq.id
                    LEFT JOIN drug_route_option                             AS dro          ON ohme.option_id = dro.id

                    LEFT JOIN ref_medication                                                ON ref_medication.source_old_id = ohme.medication_drug_id 
                    LEFT JOIN ref_medication_route                                          ON ref_medication_route.term = dr.name
                    LEFT JOIN ref_medication_frequency                                      ON ref_medication_frequency.original_id = dfreq.id
                    LEFT JOIN ref_medication_laterality                                     ON ref_medication_laterality.name = dro.name
                    WHERE et.name = 'Examination'
                    AND ref_medication.source_type = 'LEGACY'
                    AND ref_medication.source_subtype = 'medication_drug'
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
        $this->execute("DELETE FROM event_medication_uses WHERE usage_type = 'OphCiExamination' ");   
        $this->execute("SET foreign_key_checks = 1");
        
        if( Yii::app()->getModule('OphDrPrescription') ){
            $this->downPrescriptionImport();
        }
	}
}