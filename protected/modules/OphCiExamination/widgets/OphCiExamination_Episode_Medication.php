<?php
use OEModule\OphCiExamination\models;
class OphCiExamination_Episode_Medication extends \EpisodeSummaryWidget
{
    public function run_oescape()
    {
        $this->render('OphCiExamination_OEscape_Medication');
    }
    public function getMedicationList() {
        $medication_list = array('right'=>array(), 'left'=>array());
        $events = $this->event_type->api->getEvents($this->episode->patient, false);
        $earlist_date = time()*1000;
        $latest_date = time()*1000;
        $subspecialty_id = Subspecialty::model()->findByAttributes(array('name'=>'Glaucoma'))->id;
        foreach ($events as $event){
            if ($meds = $event->getElementByClass('OEModule\OphCiExamination\models\HistoryMedications')){
                $widget = $this->createWidget('OEModule\OphCiExamination\widgets\HistoryMedications', array(
                    'patient' => $this->episode->patient,));
                $untracked = $widget->getEntriesForUntrackedPrescriptionItems();
                $meds_entries = array_merge($meds->orderedEntries, $untracked);
                foreach ($meds_entries as $entry) {
                    Yii::log($entry->drug->name);
                    if ($entry->drug_id){
                        $meds_subspecialty = SiteSubspecialtyDrug::model()->findByAttributes(
                            array('drug_id'=> $entry->drug_id,
                                'subspecialty_id' => $subspecialty_id
                            ));
                    }
                    if ($entry['route_id'] == 1 && $meds_subspecialty) {
                        $drug_name = $entry->drug_id? $entry->drug->name: $entry->medication_drug->name;
                        $start_date = Helper::mysqlDate2JsTimestamp($entry->start_date);
                        $end_date = Helper::mysqlDate2JsTimestamp($entry->end_date);
                        $stop_reason = $entry->stop_reason?$entry->stop_reason->name: null;
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
                                $medication_list[$side][$drug_name] = array('low'=>$start_date, 'high'=>$end_date);
                            } elseif ($medication_list[$side][$drug_name]['low']>$start_date){
                                $medication_list[$side][$drug_name]['low'] = $start_date;
                            } elseif($medication_list[$side][$drug_name]['high']<$end_date) {
                                $medication_list[$side][$drug_name]['high'] = $end_date;
                            }
                            if ($stop_reason){
                                $medication_list[$side][$drug_name]['stop_reason'] = $stop_reason;
                            }
                        }
                    }
                }
            }
        }
        foreach (['left', 'right'] as $side){
            foreach ($medication_list[$side] as $key=>&$med) {
                if (is_null($med['low'])){
                    $med['low'] = $earlist_date;
                }
                if (is_null($med['high'])){
                    $med['high'] = $latest_date;
                }
            }
        }
        return $medication_list;
    }
}