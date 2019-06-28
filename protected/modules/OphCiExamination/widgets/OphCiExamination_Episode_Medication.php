<?php

use OEModule\OphCiExamination\models;

class OphCiExamination_Episode_Medication extends \EpisodeSummaryWidget
{
    public function run_oescape($widgets_no = 1)
    {
        $this->render('OphCiExamination_OEscape_Medication');
    }

    public function getMedicationList()
    {
        $medication_list = array('right' => array(), 'left' => array());
        $events = $this->event_type->api->getEvents($this->patient, false);
        $earlist_date = time() * 1000;
        $latest_date = time() * 1000;
        foreach ($events as $event) {
            if ($meds = $event->getElementByClass('OEModule\OphCiExamination\models\HistoryMedications')) {
                $widget = $this->createWidget('OEModule\OphCiExamination\widgets\HistoryMedications', array(
                    'patient' => $this->patient,
                ));

                $untracked = $widget->getEntriesForUntrackedPrescriptionItems();
                $meds_entries = array_merge($meds->entries, $untracked);

                foreach ($meds_entries as $entry) {

                    if (!$entry->medication_id) {
                        continue;
                    }

                    $meds_tag = array();

                    if ($entry->medication_id){
                        foreach($entry->medication->getTypes() as $item) {
                            $meds_tag[] = $item->name;
                        }
                    }

                    if ($entry['route_id'] != 1 || !$meds_tag || !in_array('Glaucoma', $meds_tag)) {
                        continue;
                    }

                    $drug_aliases = $entry->medication->alternativeTerms() ? ' ('.$entry->medication->alternativeTerms().')': '';
                    $drug_name = $entry->medication->preferred_term.$drug_aliases;
                    /*$drug_aliases = $entry->drug_id&&$entry->drug->aliases? ' ('.$entry->drug->aliases.')': '';
                    $drug_name = $entry->drug_id ? $entry->drug->name.$drug_aliases : $entry->medication_drug->name;*/

                    if($entry->start_date === null || $entry->start_date === "0000-00-00" || $entry->start_date === "") {
                        continue;
                    }

                    $start_date = Helper::mysqlDate2JsTimestamp($entry->start_date);
                    $end_date = Helper::mysqlDate2JsTimestamp($entry->end_date);
                    $stop_reason = $entry->stopReason ? $entry->stopReason->name : null;

                    if ($start_date < $earlist_date) {
                        $earlist_date = $start_date;
                    }

                     // Construct data to store medication records for left and right eye based on drug name.
                     // Each medication may have one or multiple apply time.
                    foreach ([1 => 'left', 2 => 'right'] as $eye_flag => $eye_side) {
                        if (!($entry->laterality & $eye_flag)) {
                            continue;
                        }
                        $new_medi_record = array(
                            'low' => $start_date,
                            'high' => $end_date?:$latest_date,
                            'stop_reason' => $stop_reason
                        );
                        if (!in_array($drug_name, array_keys($medication_list[$eye_side]))){
                            $medication_list[$eye_side][$drug_name] = [];
                        }
                        if (!in_array($new_medi_record, $medication_list[$eye_side][$drug_name]) ){
                            $medication_list[$eye_side][$drug_name][] = $new_medi_record;
                        }
                    }
                }
            }
        }


        foreach (['left', 'right'] as $side) {

            foreach ($medication_list[$side] as $key => &$med) {
                if (sizeof($med)>1){
                    $med = $this->purifyMedicationSeries($med);    //sort and merge each medication's time series
                }
            }
            uasort($medication_list[$side], function ($item1, $item2){
                if ($item1[0]['low'] == $item2[0]['low']) return 0;
                return $item1[0]['low'] < $item2[0]['low'] ? -1 : 1;
            });
        }

        return $medication_list;
    }

    /**
     * Sort each medication's time series by start date and merge overlapped periods.
     * @param $medication_series
     * @return array
     */
    public function purifyMedicationSeries($medication_series){
        // Sort medication time series by start date
        usort($medication_series, function ($item1, $item2){
            if ($item1['low'] == $item2['low']) return 0;
            return $item1['low'] < $item2['low'] ? -1 : 1;
        });

        $i = 0;
        $out_series = array();

        // From the earliest open time, merge overlopped time series into single one,
        // keep the earliest start time and latest stop time and stop reason
        // add to result array.
        while($i<sizeof($medication_series)){
            $begin = $medication_series[$i]['low'];
            $end = $medication_series[$i]['high'];
            $stop_reason = $medication_series[$i]['stop_reason'];
            while($i<sizeof($medication_series)-1 && $medication_series[$i+1]['low']<$end){
                if ($medication_series[$i+1]['high']>$end){
                    $end = $medication_series[$i+1]['high'];
                    $stop_reason = $medication_series[$i+1]['stop_reason'];
                }
                ++$i;
            }
            ++$i;
            $out_series[]= array(
                'low' => $begin,
                'high' => $end,
                'stop_reason' => $stop_reason
            );
        }

        return $out_series;
    }
}