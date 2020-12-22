<?php
/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2019
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2019, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 *
 */

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
        $earliest_date = time() * 1000;
        $latest_date = time() * 1000;
        $lateral_routes = array_map(function ($e) {
            return $e->id;
        }, MedicationRoute::model()->findAll());

        foreach ($events as $event) {
            $meds = $event->getElementByClass('OEModule\OphCiExamination\models\HistoryMedications');
            $start_date = '';
            $end_date = '';
            if ($meds) {
                $widget = $this->createWidget('OEModule\OphCiExamination\widgets\HistoryMedications', array(
                    'patient' => $this->patient,
                ));

                $untracked = $widget->element->getEntriesForUntrackedPrescriptionItems($this->patient);
                $meds_entries = array_merge($meds->entries, $untracked);

                foreach ($meds_entries as $entry) {
                    if (!$entry->medication_id) {
                        continue;
                    }

                    $is_glaucoma = false;
                    if ($entry->medication_id) {
                        foreach ($entry->medication->getSetsByUsageCode('OEScape') as $set) {
                            foreach ($set->medicationSetRules as $rule) {
                                if ($rule->subspecialty && $rule->subspecialty->ref_spec === 'GL') {
                                    $is_glaucoma = true;
                                }
                            }
                        }
                    }

                    if ( !in_array($entry['route_id'], $lateral_routes) || !$is_glaucoma) {
                        continue;
                    }

                    $drug_aliases = $entry->medication->alternativeTerms(true) ? ' ('.$entry->medication->alternativeTerms(true).')': '';
                    $drug_name = $entry->medication->getLabel(true) . $drug_aliases;

                    if ($entry->start_date !== "0000-00-00" && $entry->start_date !== "") {
                        $start_date = $this->changeInvalidSqlDateMonthAndDayToOne($entry->start_date, 'start');
                        $start_date = Helper::mysqlDate2JsTimestamp($start_date);

                        if ($start_date < $earliest_date) {
                            $earliest_date = $start_date;
                        }
                    }

                    $end_date = $this->changeInvalidSqlDateMonthAndDayToOne($entry->end_date, 'end');
                    $end_date = Helper::mysqlDate2JsTimestamp($end_date);

                    $stop_reason = $entry->stopReason ? $entry->stopReason->name : null;

                     // Construct data to store medication records for left and right eye based on drug name.
                     // Each medication may have one or multiple apply time.
                    foreach ([1 => 'left', 2 => 'right'] as $eye_flag => $eye_side) {
                        if (isset($entry->laterality) && !($entry->laterality & $eye_flag)) {
                            continue;
                        }
                        $new_medi_record = array(
                            'low' => $start_date,
                            'high' => $end_date?:$latest_date,
                            'stop_reason' => $stop_reason
                        );
                        if (!in_array($drug_name, array_keys($medication_list[$eye_side]))) {
                            $medication_list[$eye_side][$drug_name] = [];
                        }
                        if (!in_array($new_medi_record, $medication_list[$eye_side][$drug_name]) ) {
                            $medication_list[$eye_side][$drug_name][] = $new_medi_record;
                        }
                    }
                }
            }
        }

        foreach (['left', 'right'] as $side) {
            foreach ($medication_list[$side] as $key => &$med) {
                if ($med[0]['low'] === '') {
                    $med[0]['low'] = $earliest_date;
                }

                if (sizeof($med)>1) {
                    $med = $this->purifyMedicationSeries($med);    //sort and merge each medication's time series
                }
            }
            uasort($medication_list[$side], function ($item1, $item2){
                if ($item1[0]['low'] == $item2[0]['low']) {
                    if ($item1[0]['high'] == $item2[0]['high']) {
                        return 0;
                    }
                    return $item1[0]['high'] < $item2[0]['high'] ? -1 : 1;
                }
                return $item1[0]['low'] < $item2[0]['low'] ? -1 : 1;
            });
        }

        return $medication_list;
    }

    /**
     * Removes any occurrences of 00 in SQL date for OEscape formatting
     * @param $date
     * @param $date_type
     * @return string
     */
    private function changeInvalidSqlDateMonthAndDayToOne($date, $date_type) {

        if (is_null($date)) {
            return $date;
        }

        $default_month = ['start' => '01', 'end' => '12'];
        $default_day = ['start' => '01', 'end' => '31'];

        $date_sections = explode('-', $date);
        $new_date[] = $date_sections[0];
        $month = $date_sections[1] === '00' ? $default_month[$date_type] : $date_sections[1];
        $new_date[] = $month;
        $day = $date_sections[2] === '00' ? $default_day[$date_type] : $date_sections[2];
        $new_date[] = $day;

        return implode('-', $new_date);
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
        while ($i<sizeof($medication_series)) {
            $begin = $medication_series[$i]['low'];
            $end = $medication_series[$i]['high'];
            $stop_reason = $medication_series[$i]['stop_reason'];
            while ($i<sizeof($medication_series)-1 && $medication_series[$i+1]['low']<$end) {
                if ($medication_series[$i+1]['high']>$end) {
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
