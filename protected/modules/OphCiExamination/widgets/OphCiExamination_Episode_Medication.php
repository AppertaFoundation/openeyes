<?php
use OEModule\OphCiExamination\models;
class OphCiExamination_Episode_Medication extends \EpisodeSummaryWidget
{
    public function run()
    {
        $this->render('OphCiExamination_Episode_Medication');
    }
    public function getMedicationList() {
        $medication_list = array('right'=>array(), 'left'=>array());
        $events = $this->event_type->api->getEvents($this->episode->patient, false);
        $earlist_date = time()*1000;
        $latest_date = time()*1000;
        foreach ($events as $event){
            if ($meds = $event->getElementByClass('OEModule\OphCiExamination\models\HistoryMedications')){
                $meds_entries = $meds->orderedEntries;
                foreach ($meds_entries as $entry) {
                    if ($entry['route_id'] == 1) {
                        $drug_name = $entry->drug_id? $entry->drug->name: $entry->medication_drug->name;
                        $start_date = Helper::mysqlDate2JsTimestamp($entry->start_date);
                        $end_date = Helper::mysqlDate2JsTimestamp($entry->end_date);
                        $option_id = $entry->option_id;
                        $eye_side = array();
                        if ($start_date < $earlist_date){
                            $earlist_date = $start_date;
                        }
                        switch ($option_id){
                            case 1:             //left eye
                                array_push($eye_side, 'left');
                                break;
                            case 2:
                                array_push($eye_side, 'right');
                                break;
                            case 3:
                                array_push($eye_side, 'left');
                                array_push($eye_side, 'right');
                                break;
                            default:
                                break;
                        }
                        foreach ($eye_side as $side) {
                            if (empty($medication_list[$side])||!array_key_exists($drug_name, $medication_list[$side])){
                                $medication_list[$side][$drug_name] = array($start_date, $end_date);
                            } elseif ($medication_list[$side][$drug_name][0]>$start_date){
                                $medication_list[$side][$drug_name][0] = $start_date;
                            } elseif($medication_list[$side][$drug_name][1]<$end_date) {
                                $medication_list[$side][$drug_name][1] = $end_date;
                            }
                        }
                    }
                }
            }
        }
        foreach (['left', 'right'] as $side){
            foreach ($medication_list[$side] as $key=>&$med) {
                if (is_null($med[0])){
                    $med[0] = $earlist_date;
                }
                if (is_null($med[1])){
                    $med[1] = $latest_date;
                }
            }
        }
        return $medication_list;
    }
}