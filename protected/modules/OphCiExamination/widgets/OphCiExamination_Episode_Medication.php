<?php

use OEModule\OphCiExamination\models;

class OphCiExamination_Episode_Medication extends \EpisodeSummaryWidget
{
    public function run_oescape()
    {
        $this->render('OphCiExamination_OEscape_Medication');
    }

    public function getMedicationList()
    {
        $medication_list = array('right' => array(), 'left' => array());
        $events = $this->event_type->api->getEvents($this->episode->patient, false);
        $earlist_date = time() * 1000;
        $latest_date = time() * 1000;
        $subspecialty_id = Subspecialty::model()->findByAttributes(array('name' => 'Glaucoma'))->id;
        foreach ($events as $event) {
            if ($meds = $event->getElementByClass('OEModule\OphCiExamination\models\HistoryMedications')) {
                $widget = $this->createWidget('OEModule\OphCiExamination\widgets\HistoryMedications', array(
                    'patient' => $this->episode->patient,
                ));

                $untracked = $widget->getEntriesForUntrackedPrescriptionItems();
                $meds_entries = array_merge($meds->orderedEntries, $untracked);

                foreach ($meds_entries as $entry) {
                    if (!$entry->drug_id) {
                        continue;
                    }

                    $meds_tag = array();

                    foreach ($entry->drug->tags as $item) {
                        array_push($meds_tag, $item->name);
                    }

                    if ($entry['route_id'] != 1 || !$meds_tag || !in_array('Glaucoma', $meds_tag)) {
                        continue;
                    }

                    $durg_aliases = $entry->drug->aliases? ' ('.$entry->drug->aliases.')': '';
                    $drug_name = $entry->drug_id ? $entry->drug->name.$durg_aliases : $entry->medication_drug->name;
                    $start_date = Helper::mysqlDate2JsTimestamp($entry->start_date);
                    $end_date = Helper::mysqlDate2JsTimestamp($entry->end_date);
                    $stop_reason = $entry->stop_reason ? $entry->stop_reason->name : null;

                    if ($start_date < $earlist_date) {
                        $earlist_date = $start_date;
                    }

                    foreach ([1 => 'left', 2 => 'right'] as $eye_flag => $eye_side) {
                        if (!($entry->option_id & $eye_flag)) {
                            continue;
                        }


                        if (empty($medication_list[$eye_side]) || !array_key_exists($drug_name, $medication_list[$eye_side])) {
                            $medication_list[$eye_side][$drug_name] = array(
                                'low' => $start_date,
                                'high' => $end_date,
                            );
                        } elseif ($medication_list[$eye_side][$drug_name]['low'] > $start_date) {
                            $medication_list[$eye_side][$drug_name]['low'] = $start_date;
                        } elseif ($medication_list[$eye_side][$drug_name]['high'] < $end_date) {
                            $medication_list[$eye_side][$drug_name]['high'] = $end_date;
                        }

                        if ($stop_reason) {
                            $medication_list[$eye_side][$drug_name]['stop_reason'] = $stop_reason;
                        }
                    }
                }
            }
        }

        foreach (['left', 'right'] as $side) {
            foreach ($medication_list[$side] as $key => &$med) {
                if ($med['low'] === null) {
                    $med['low'] = $earlist_date;
                }
                if ($med['high'] === null) {
                    $med['high'] = $latest_date;
                }
            }
        }

        return $medication_list;
    }
}