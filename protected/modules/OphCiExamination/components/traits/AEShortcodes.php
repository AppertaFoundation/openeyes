<?php
/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2021
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2021, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

namespace OEModule\OphCiExamination\components\traits;

use OEModule\OphCiExamination\models\OphCiExamination_VisualAcuityUnit;

trait AEShortcodes
{
    /**
     * Get the latest examination event from AE subspecialty and returns the
     * created date day name, eg.: Wednesday
     *
     * Shortcode: ady
     *
     * @param $patient
     * @param false $use_context
     * @return false|string
     */
    public function getLatestAEEventDay($patient, $use_context = false)
    {
        $event = $this->getLatestEventBySubspecialty($patient, 'AE');

        if ($event) {
            return date("l", strtotime($event->event_date));
        }

        return '';
    }

    /**
     * Latest Examination saved date in A&E subspecialty
     *
     * Shortcode: adt
     *
     * @param $patient
     * @param false $use_context
     * @return string|null
     */
    public function getLatestAEEventDate($patient, $use_context = false)
    {
        $event = $this->getLatestEventBySubspecialty($patient, 'AE');
        return $event ? \Helper::convertMySQL2NHS($event->event_date) : null;
    }

    /**
     * Most recent Triage element's chief complaint in AE subspecialty
     *
     * Shortcode: cco
     *
     * @param Patient $patient
     * @param false $use_context
     * @return string
     */
    public function getLastAEChiefComplaints(\Patient $patient, $use_context = false): string
    {
        $element = $this->getLatestElementBySubspecialty($patient, 'models\Element_OphCiExamination_Triage', 'AE');
        return $element->triage->chief_complaint->description ?? '';
    }

    /**
     * Most recent Triage element's chief complaint's eye in AE subspecialty
     *
     * Shortcode: cce
     *
     * @param Patient $patient
     * @param false $use_context
     * @return string
     */
    public function getLastAEChiefComplaintsEye(\Patient $patient, $use_context = false): string
    {
        $element = $this->getLatestElementBySubspecialty($patient, 'models\Element_OphCiExamination_Triage', 'AE');
        return $element->triage->eye->name ?? '';
    }

    /**
     * Most recent Triage element's comment in AE subspecialty
     *
     * Shortcode: ccc
     *
     * @param Patient $patient
     * @param false $use_context
     * @return string
     */
    public function getLastAETriageComment(\Patient $patient, $use_context = false): string
    {
        $element = $this->getLatestElementBySubspecialty($patient, 'models\Element_OphCiExamination_Triage', 'AE');
        return $element->triage->comments ?? '';
    }

    /**
     * Most recent Triage element's chief complaint's priority in A&E subspecialty
     *
     * Shortcode: pri
     *
     * @param Patient $patient
     * @param false $use_context
     * @return string
     */
    public function getLastAETriagePriority(\Patient $patient, $use_context = false): string
    {
        $element = $this->getLatestElementBySubspecialty($patient, 'models\Element_OphCiExamination_Triage', 'AE');
        return $element->triage->priority->description ?? '';
    }
    // Most recent Examination Event pain in A&E subspecialty


    /**
     * Most recent pain score in A&E subspecialty
     *
     * Shortcode: aps
     *
     * @param Patient $patient
     * @param false $use_context
     * @return string
     */
    public function getLastAEPain(\Patient $patient, $use_context = false): string
    {
        $element = $this->getLatestElementBySubspecialty($patient, 'models\Element_OphCiExamination_Pain', 'AE');

        if (!$element) {
            return '';
        }

        $result = 'Pain score: <br>';
        foreach ($element->entries as $pain) {
            $result .= $pain->pain_score . ' at ' . date("H:i", strtotime($pain->datetime)) . "<br>";
        }

        return $result;
    }

    /**
     * Most recent safeguarding concern in A&E subspecialty
     *
     * Shortcode: asc
     *
     * @param Patient $patient
     * @param false $use_context
     * @return string
     * @throws Exception
     */
    public function getLastAESafeguardingConcern(\Patient $patient, $use_context = false): string
    {
        $text = 'No safeguarding issues identified';
        $element = $this->getLatestElementBySubspecialty($patient, 'models\Element_OphCiExamination_Safeguarding', 'AE');
        $collection = new \ModelCollection($element->entries ?? []);
        $concerns = $collection->pluck('concern');

        if ($element) {
            $text = "Safeguarding issues identified: <br> - " . (implode("<br> - ", $concerns));
        }

        return $text;
    }

    /**
     * Comment of the most recent Freehand element's Ant Seg drawing in A&E subspecialty
     *
     * Shortcode: aas
     *
     * @param Patient $patient
     * @param false $use_context
     * @return string
     */
    public function getLastAEFreehandAntSegComment(\Patient $patient, $use_context = false): string
    {
        $elements = $this->getAllElementsBySubspecialty($patient, 'models\FreehandDraw', 'AE');

        // at least one entry needs to be in the array to continue
        if (!isset($elements[0]->entries[0])) {
            return '';
        }

        $most_recent_entry = $this->getLatestEntryByTemplateName($elements, 'Examination anterior segment');

        return $most_recent_entry->comments;
    }

    /**
     * Comment of the most recent Freehand element's Ant Seg drawing in A&E subspecialty
     *
     * Shortcode: afu
     *
     * @param Patient $patient
     * @param false $use_context
     * @return string
     */
    public function getLastAEFreehandFundusComment(\Patient $patient, $use_context = false): string
    {
        $elements = $this->getAllElementsBySubspecialty($patient, 'models\FreehandDraw', 'AE');

        // at least one entry needs to be in the array to continue
        if (!isset($elements[0]->entries[0])) {
            return '';
        }

        $most_recent_entry = $this->getLatestEntryByTemplateName($elements, 'Examination fundus');

        return $most_recent_entry->comments ?? '';
    }

    private function getLatestEntryByTemplateName($elements, $name)
    {
        $most_recent_entry = '';

        foreach ($elements as $element) {
            // Ok, so this is poor approach, the text($name) like 'Examination anterior segment' can be
            // renamed in the admin and then this shortcode will not work anymore,
            // but we can not grab this in any other way atm.
            $entries = $element->entries([
                    'with' => ['protected_file'],
                    'condition' => "protected_file.name = '{$name}'",
                    'order' => 'entries.id DESC', // if multiple entries with the same name we select latest/last one
                    'limit' => 1
                ]);

            $entry = $entries[0] ?? null;

            if (!$entry) {
                return '';
            }

            // set the starting value if there is none
            $most_recent_entry = !$most_recent_entry ? $entry : $most_recent_entry;
            $entry_event_data = $entry->element->event->event_date;
            $most_recent_entry_event_date = $most_recent_entry->element->event->event_date;

            $most_recent_entry = $entry_event_data < $most_recent_entry_event_date ? $entry : $most_recent_entry;
        }

        return $most_recent_entry;
    }

    /**
     * Most recent Examination Event Advice Given element's comment in A&E subspecialty
     *
     * Shortcode: aag
     *
     * @param \Patient $patient
     * @param false $use_context
     * @return string
     */
    public function getLastAEAdvicesGivenComment(\Patient $patient, $use_context = false): string
    {
        $element = $this->getLatestElementBySubspecialty($patient, 'models\AdviceGiven', 'AE');
        return $element ? $element->letter_string : '';
    }

    /**
     * Most recent Examination Event Follow up element's discharge info in A&E subspecialty
     *
     * Shortcode: adi
     *
     * @param \Patient $patient
     * @param false $use_context
     * @return string
     */
    public function getLastAEDischargeManagement(\Patient $patient, $use_context = false): string
    {
        $element = $this->getLatestElementBySubspecialty($patient, 'models\Element_OphCiExamination_ClinicOutcome', 'AE');

        if (!$element) {
            return '';
        }

        $entry = $element->entries([
            'condition' => 'discharge_status_id IS NOT NULL AND discharge_destination_id IS NOT NULL',
            'order' => 'id DESC', //latest discharge among entries
            'limit' => 1
        ])[0] ?? null;

        if (!$entry) {
            return '';
        }

        return "Discharge :" . $entry->discharge_status->name . ", " . $entry->discharge_destination->name;
    }

    /**
     *
     * Most recent Examination Clinic Procedures in A&E subspecialty
     *
     * Shortcode: atr
     *
     * @param \Patient $patient
     * @param false $use_context
     * @param false $with_comments
     * @return string
     */
    public function getLastAETreatments(\Patient $patient, $use_context = false, $with_comments = false): string
    {
        $element = $this->getLatestElementBySubspecialty($patient, 'models\Element_OphCiExamination_ClinicProcedures', 'AE');

        if (!$element) {
            return '';
        }

        if (isset($element->entries)) {
            return implode("<br>", array_map(function ($e) use ($with_comments) {
                return $e->procedure->term . ($with_comments ? " {$e->comments}" : '');
            }, $element->entries));
        }

        return '';
    }

    /**
     *
     * Most recent Examination Clinic Procedures with comments in A&E subspecialty
     *
     * Shortcode: atc
     *
     * @param \Patient $patient
     * @param false $use_context
     * @param false $with_comments
     * @return string
     */
    public function getLastAETreatmentsComments(\Patient $patient, $use_context = false, $with_comments = false): string
    {
        return $this->getLastAETreatments($patient, false, true);
    }

    /**
     * Most recent Examination Investigations in A&E subspecialty
     *
     * Shortcode: ain
     *
     * @param \Patient $patient
     * @param false $use_context
     * @return string
     * @throws \Exception
     */
    public function getLastAEInvestigation(\Patient $patient, $use_context = false): string
    {
        $element = $this->getLatestElementBySubspecialty($patient, 'models\Element_OphCiExamination_Investigation', 'AE');
        if (!isset($element->entries)) {
            return '';
        }

        $collection = new \ModelCollection($element->entries);
        $investigations = $collection->pluck('investigationCode');

        return $investigations ? implode(', ', $investigations) : '';
    }

    private function getAEDiganoses(\Patient $patient, $use_context = false, $is_principal = false): string
    {
        $element = $this->getLatestElementBySubspecialty($patient, 'models\Element_OphCiExamination_Diagnoses', 'AE');

        if (!$element) {
            return '';
        }

        $diagnosis = $element->diagnoses([
                'condition' => 'principal = ' . ($is_principal ? 1 : 0)
            ]) ?? [];

        return implode("<br>", array_map(function ($d) {
            return $d->eye->getAdjective() . " " . $d->disorder->term;
        }, $diagnosis));
    }

    /**
     * Most recent Examination Ophthalmic principal Diagnosis in A&E subspecialty
     *
     * Shortcode: apd
     *
     * @param \Patient $patient
     * @param false $use_context
     * @return string
     * @throws \Exception
     */
    public function getAEPrincipalDiagnosis(\Patient $patient, $use_context = false): string
    {
        return $this->getAEDiganoses($patient, $use_context, true);
    }

    /**
     * Most recent Examination Ophthalmic Diagnosis (without principal) in A&E subspecialty
     *
     * Shortcode: asd
     *
     * @param \Patient $patient
     * @param false $use_context
     * @return string
     * @throws \Exception
     */
    public function getAEOtherDiagnosis(\Patient $patient, $use_context = false): string
    {
        return $this->getAEDiganoses($patient, $use_context);
    }

    /**
     * Most recent Examination Intraocular Pressure element first value in A&E subspecialty
     *
     * Shortcode: aif
     *
     * @param \Patient $patient
     * @param false $use_context
     * @return string
     * @throws \Exception
     */
    public function getLastAEIntraocularPressureFirstValue(\Patient $patient, $use_context = false): string
    {
        $element = $this->getLatestElementBySubspecialty($patient, 'models\Element_OphCiExamination_IntraocularPressure', 'AE');

        if (!$element) {
            return '';
        }

        $result = "";
        if ($element->right_values) {
            $right_value = $element->right_values[0];
            $result .= "Intraocular pressure (mmHg), <br>Right: ".$right_value->instrument->name." (".$right_value->reading->name.")";
        }

        if ($element->left_values) {
            $left_value = $element->left_values[0];
            $result .= "\nLeft: ".$left_value->instrument->name." (".$left_value->reading->name.")";
        }

        return $result;
    }

    /**
     * Gets Pressure releasing stat medication in A&E subspecialty
     *
     * Shortcode: aip
     *
     * @param \Patient $patient
     * @param false $use_context
     * @return string
     */
    public function getLastAEIntraocularPressure(\Patient $patient, $use_context = false): string
    {
        $element = $this->getLatestElementBySubspecialty($patient, 'models\Element_OphCiExamination_IntraocularPressure', 'CA');

        if (!$element) {
            return '';
        }

        $result = "";
        foreach(["right", "left"] as $side) {
            $result .= $element->{$side."_values"} ? (ucfirst($side) . " ") : '';
            $result .= implode(", ", array_map(function($item) {
                $ip_value = $item->instrument->scale ? $item->qualitative_reading->name : $item->reading->name;

                return
                    substr($item->reading_time, 0, 5) . '  ' .
                    $ip_value . ' ' .($item->instrument ? $item->instrument->name : '');

            }, $element->{$side."_values"}));

            $result .= $result !== '' ? '<br>' : $result;
        }

        return $result;
    }

    /**
     * Most recent Examination VA element reading in A&E subspecialty
     *
     * Shortcode: aev
     *
     * @param \Patient $patient
     * @param false $use_context
     * @return string
     */
    public function getLastAEVisualAcuity(\Patient $patient, $use_context = false): string
    {
        $element = $this->getLatestElementBySubspecialty($patient, 'models\Element_OphCiExamination_VisualAcuity', 'CA');

        if (!$element) {
            return '';
        }

        $visual_acuity_str = '';

        foreach (['right', 'left'] as $side) {
            $reading = $element->{"{$side}_readings"}([
                'order' => 'id ASC' // the date will be the same, use the id
            ])[0] ?? null;

            $visual_acuity_str = ($visual_acuity_str === '' ? 'Visual Acuity: <br>' : $visual_acuity_str);
            $visual_acuity_str .= ucfirst($side) . ": " . $reading->display_value . ' '. $reading->method->name . "<br>";
        }

        return $visual_acuity_str;
    }

    /**
     * Most recent Examination VA element Snellen metre readings in A&E subspecialty
     *
     * Shortcode: ava
     *
     * @param \Patient $patient
     * @param false $use_context
     * @return string
     */
    public function getLastAEVisualAcuitySnellen(\Patient $patient, $use_context = false): string
    {
        $element = $this->getLatestElementBySubspecialty($patient, 'models\Element_OphCiExamination_VisualAcuity', 'AE');

        if (!$element) {
            return '';
        }
        $unit = OphCiExamination_VisualAcuityUnit::model()->findByAttributes(["name" => "Snellen Metre"]);

        $visual_acuity_str = '';
        foreach (['right', 'left'] as $side) {
            $readings = $element->{"{$side}_readings"}([
                'with' => ['unit', 'method'],
                'condition' => 'unit.name = "Snellen Metre"'
            ]);

            $combined = [];
            foreach ($readings as $reading) {
                $combined[] = $reading->convertTo($reading->value, $unit->id). ' '. $reading->method->name;
            }
            if ($combined) {
                $visual_acuity_str = ($visual_acuity_str === '' ? 'Visual Acuity:<br>' : $visual_acuity_str);
                $visual_acuity_str .= ucfirst($side) . ": " . (implode(', ', $combined)) . "<br>";
            }
        }

        return $visual_acuity_str;
    }

    /**
     * Generate shortcode text from Medication management entries
     *
     * @param array $entries
     * @return string
     */
    private function medicationEntriesToShortcodeText(array $entries): string
    {
        $text = '';
        foreach ($entries as $entry) {
            $text .= $entry->medication->preferred_term . ': ' . $entry->dose;
            $laterality = $entry->getLateralityDisplay(true);
            $text .=  $laterality ? " {$laterality}" : '';

            $text .= ' ' . $entry->route->term.' at '.date('H:i',strtotime($entry->created_date))."\n";
        }

        return $text;
    }

    /**
     * Gets medications with frequency: immediately (stat) in A&E subspecialty
     *
     * Shortcode: asm
     *
     * @param \Patient $patient
     * @param false $use_context
     * @return string
     */
    public function getLastAEStatMedications(\Patient $patient, $use_context = false): string
    {
        $element = $this->getLatestElementBySubspecialty($patient, 'models\MedicationManagement', 'CA');

        if (!$element) {
            return '';
        }

        $entries = $element->visible_entries([
            'with' => ['frequency', 'medication', 'route'],
            'condition' => 'frequency.code = "stat"'
        ]);

        return $this->medicationEntriesToShortcodeText($entries);
    }

    /**
     * Gets Pressure releasing stat medication in A&E subspecialty
     *
     * Shortcode: apl
     *
     * @param \Patient $patient
     * @param false $use_context
     * @return string
     */
    public function getLastAEPressureReleasingStatMedication(\Patient $patient, $use_context = false): string
    {
        $element = $this->getLatestElementBySubspecialty($patient, 'models\MedicationManagement', 'CA');

        if (!$element) {
            return '';
        }

        $usage_code_id = \MedicationUsageCode::model()->findByAttributes(['usage_code' => 'COMMON_EYE_DROPS']);
        if (!$usage_code_id) {
            throw new \Exception("Usage code 'COMMON_EYE_DROPS' not found");
        }

        $entries = $element->visible_entries([
            'with' => ['frequency', 'medication.medicationSets.medicationSetRules', 'route'],
            'condition' => 'frequency.code = "stat" AND usage_code_id = :usage_code_id',
            'params' => [':usage_code_id' => $usage_code_id->id]
        ]);

        return $this->medicationEntriesToShortcodeText($entries);
    }
}
