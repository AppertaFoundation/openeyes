<?php

/**
 * Class m180510093824_prescription_events_import
 *
 * This is not the right place for this migration, but we must ensure
 * that it runs right before the examination import.
 */

class m180510_093824_prescription_events_import extends CDbMigration
{
    public function up()
    {
        echo "> This migration may take a several seconds!\n";
        $this->dropTapersFK()
             ->runPrescriptionImport()
             ->applyTapersFK();

        return true;
    }

    public function down()
    {
        $this->execute("SET foreign_key_checks = 0");
        $this->execute("DELETE FROM event_medication_use WHERE usage_type = 'OphDrPrescription' ");
        $this->execute("SET foreign_key_checks = 1");

        $this->dropForeignKey('ophdrprescription_item_taper_item_id_fk', 'ophdrprescription_item_taper');
        $this->alterColumn('ophdrprescription_item_taper', 'item_id', 'INT(10) UNSIGNED NOT NULL');

        $this->execute("UPDATE ophdrprescription_item_taper SET item_id = (SELECT temp_prescription_item_id FROM event_medication_use WHERE id = ophdrprescription_item_taper.item_id)");

        $this->addForeignKey('ophdrprescription_item_taper_item_id_fk', 'ophdrprescription_item_taper', 'item_id', 'ophdrprescription_item', 'id');
    }

    private function dropTapersFK()
    {
        $this->dropForeignKey('ophdrprescription_item_taper_item_id_fk', 'ophdrprescription_item_taper');
        $this->alterColumn('ophdrprescription_item_taper', 'item_id', 'INT(11) NOT NULL');
        return $this;
    }

    private function applyTapersFK()
    {
        $this->addForeignKey('ophdrprescription_item_taper_item_id_fk', 'ophdrprescription_item_taper', 'item_id', 'event_medication_use', 'id');
        return $this;
    }

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
                        presc_item.comments                 AS comments,
                        medication_laterality.id        AS ref_laterality_id,
                        medication.id                   AS ref_medication_id,
                        medication_form.id              AS ref_medication_form_id,
                        medication_route.id             AS ref_route_id,
                        medication_frequency.id         AS ref_frequency_id,
                        dd.id                               AS duration_id
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
                    AND medication.source_subtype = 'drug'
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
                $ref_dispense_condition_id = ($event['ref_dispense_condition_id'] == null) ? 'NULL' : $event['ref_dispense_condition_id'];
                $ref_dispense_location_id = ($event['ref_dispense_location_id'] == null) ? 'NULL' : $event['ref_dispense_location_id'];

                $command = Yii::app()->db
                    ->createCommand("
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
                        duration, 
                        dispense_location_id, 
                        dispense_condition_id,  
                        start_date,
                        temp_prescription_item_id,
                        comments
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
                        ".$event['duration_id'].",
                        ".$ref_dispense_condition_id.",
                        ".$ref_dispense_location_id.",
                        '".$event['event_date']."',
                        ".$event['temp_prescription_item_id'].",
                        :comments
                         )
                ");
                $command->bindParam(':comments', $event['comments']);
                $command->execute();
                $command = null;

                $last_id = Yii::app()->db->getLastInsertID();

                Yii::app()->db->createCommand("UPDATE ophdrprescription_item_taper SET item_id = :new_id WHERE item_id = :old_id")
                    ->bindParam(":old_id", $event['temp_prescription_item_id'])
                    ->bindParam(":new_id", $last_id)
                    ->execute();
            }
        }

        return $this;
    }
}