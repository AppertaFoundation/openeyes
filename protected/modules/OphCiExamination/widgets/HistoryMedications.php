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
 */

namespace OEModule\OphCiExamination\widgets;

use OEModule\OphCiExamination\models\HistoryMedications as HistoryMedicationsElement;
use OEModule\OphCiExamination\models\HistoryMedicationsEntry;

/**
 * Class HistoryMedications
 * @package OEModule\OphCiExamination\widgets
 * @property HistoryMedicationsElement $element
 */
class HistoryMedications extends \BaseEventElementWidget
{
    public static $INLINE_EVENT_VIEW = 256;
    public static $PRESCRIPTION_PRINT_VIEW = 512;

    public static $moduleName = 'OphCiExamination';
    public $notattip_edit_warning = 'OEModule.OphCiExamination.widgets.views.HistoryMedications_edit_nottip';
    public $is_latest_element = null;
    public $missing_prescription_items = null;

    protected $print_view = 'HistoryMedications_event_print';

    /**
     * @return HistoryMedicationsElement
     */
    protected function getNewElement()
    {
        return new HistoryMedicationsElement();
    }

    /**
     * @param $mode
     * @return bool
     * @inheritdoc
     */
    protected function validateMode($mode)
    {
        return in_array($mode,
            array(static::$PRESCRIPTION_PRINT_VIEW, static::$INLINE_EVENT_VIEW), true) || parent::validateMode($mode);
    }

    /**
     * @return bool
     */
    protected function showViewTipWarning()
    {
        return $this->mode === static::$EVENT_VIEW_MODE;
    }

    /**
     * Creates new Entry records for any prescription items that are not in the
     * current element.
     *
     * @return array
     */
    public function getEntriesForUntrackedPrescriptionItems()
    {
        $untracked = array();
        if ($api = $this->getApp()->moduleAPI->get('OphDrPrescription')) {
            $tracked_prescr_item_ids = array_map(
                function ($entry) {
                    return $entry->prescription_item_id;
                },
                $this->element->getPrescriptionEntries()
            );

            if ($untracked_prescription_items = $api->getPrescriptionItemsForPatient(
                $this->patient, $tracked_prescr_item_ids)
            ) {
                foreach ($untracked_prescription_items as $item) {
                    $entry = new HistoryMedicationsEntry();
                    $entry->loadFromPrescriptionItem($item);
                    $untracked[] = $entry;
                }
            }
        }
        return $untracked;
    }

    /**
     * @return bool
     * @inheritdoc
     */
    protected function isAtTip()
    {
        $this->is_latest_element = parent::isAtTip();
        // if it's a new record we trust that the missing prescription items will be added
        // to the element, otherwise we care if there are untracked prescription items
        // in terms of this being considered a tip record.
        if ($this->is_latest_element && $this->element->isNewRecord) {
            return true;
        }
        $this->missing_prescription_items = (bool) $this->getEntriesForUntrackedPrescriptionItems();
        foreach ($this->element->entries as $entry) {
            if ($entry->prescriptionNotCurrent()) {
                return false;
            }
        }
        return !$this->missing_prescription_items;
    }

    /**
     * @inheritdoc
     */
    protected function setElementFromDefaults()
    {
        // because the entries cloned into the new element may contain stale data for related
        // prescription data (or that prescription item might have been deleted)
        // we need to update appropriately.
        $entries = array();
        foreach ($this->element->entries as $entry) {
            if ($entry->prescription_item_id) {
                if ($entry->prescription_event_deleted || !$entry->prescription_item) {
                    continue;
                }
                $entry->loadFromPrescriptionItem($entry->prescription_item);
            }
            $entries[] = $entry;
        }

        if ($untracked = $this->getEntriesForUntrackedPrescriptionItems()) {
            // tracking prescription items.
            $this->element->entries = array_merge(
                $entries,
                $untracked);
        }
    }

    /**
     * @param HistoryMedicationsElement $element
     * @param $data
     * @throws \CException
     */
    protected function updateElementFromData($element, $data)
    {
        if  (!is_a($element, 'OEModule\OphCiExamination\models\HistoryMedications')) {
            throw new \CException('invalid element class ' . get_class($element) . ' for ' . static::class);
        }

        // pre-cache current entries so any entries that remain in place will use the same db row
        $entries_by_id = array();
        foreach ($element->entries as $entry) {
            $entries_by_id[$entry->id] = $entry;
        }

        if (array_key_exists('entries', $data)) {
            $entries = array();
            foreach ($data['entries'] as $entry_data) {
                $id = array_key_exists('id', $entry_data) ? $entry_data['id'] : null;
                $entry = ($id && array_key_exists($id, $entries_by_id)) ?
                    $entries_by_id[$id] :
                    new HistoryMedicationsEntry();

                foreach (array('originallyStopped', 'start_date', 'end_date', 'drug_id', 'medication_drug_id',
                             'medication_name', 'dose', 'units', 'frequency_id', 'route_id', 'option_id',
                             'stop_reason_id', 'prescription_item_id') as $k) {
                    $entry->$k = array_key_exists($k, $entry_data) ? $entry_data[$k] : null;
                }
                if ($entry_data['start_date']){
                    list($start_year, $start_month, $start_day) = array_pad(explode('-', $entry_data['start_date']), 3, null);
                    $entry->start_date = \Helper::padFuzzyDate($start_year, $start_month, $start_day);
                }
                if ($entry_data['end_date']){
                    list($end_year, $end_month, $end_day) = array_pad(explode('-', $entry_data['end_date']), 3, null);
                    $entry->end_date = \Helper::padFuzzyDate($end_year, $end_month, $end_day);
                }

                $entries[] = $entry;
            }
            $element->entries = $entries;
        } else {
            $element->entries = array();
        }
    }

    /**
     * Merges any missing (i.e. created since the element) prescription items into lists of
     * current and stopped medications for patient level rendering.
     *
     * Expected to only be used with the at tip element, but would work with older elements as well
     * should the need arise.
     *
     * @return array
     */
    public function getMergedEntries()
    {

        //$this->element->currentOrderedEntries and stoppedOrderedEntries relations are not uses here as we
        //need to include the untracked Prescription Items as well and those are already loaded into the
        //$this->element->entries (alongside with tracked Prescription Items)

        // setElementFromDefaults() only called when the element is a new record (BaseEventElementWidget like ~166)
        // and this is where the untracked elements are loaded into the $this->element->entries
        // so if it isn't a new element ->entries only contains tracked medications
        if (!$this->element->isNewRecord) {
            if ($untracked = $this->getEntriesForUntrackedPrescriptionItems()) {
                // tracking prescription items.
                $this->element->entries = array_merge(
                    $this->element->entries,
                    $untracked
                );
            }
        }

        $result['current'] = array();
        $result['stopped'] = array();

        if ($this->element->entries) {
            $stopped = array();
            $current = array();
            foreach ($this->element->entries as $entry) {
                if ($entry->end_date && $entry->end_date <= date("Y-m-d")) {
                    $stopped[] = $entry;
                } else {
                    $current[] = $entry;
                }
            }
            $sorter = function ($a, $b) {
                return $a['start_date'] >= $b['start_date'] ? -1 : 1;
            };
            uasort($current, $sorter);
            uasort($stopped, $sorter);

            $result['current'] = $current;
            $result['stopped'] = $stopped;
        }

        // now remove any that are no longer relevant because the prescription item
        // has been deleted
        $filter = function ($entry) {
            return !($entry->prescription_item_deleted || $entry->prescription_event_deleted);
        };

        $result['current'] = array_filter($result['current'], $filter);
        $result['stopped'] = array_filter($result['stopped'], $filter);

        return $result;
    }

    /**
     * @param $entry
     * @return string
     */
    public function getPrescriptionLink($entry)
    {
        return '/OphDrPrescription/Default/view/' . $entry->prescription_item->prescription->event_id;
    }

    /**
     * @return string
     */
    public function popupList()
    {
        return $this->render($this->getView(), $this->getViewData());
    }

    /**
     * @return string
     * @inheritdoc
     */
    protected function getView()
    {
        // custom mode for rendering in the patient popup because the data is more complex
        // for this history element than others which just provide a list.
        $short_name = substr(strrchr(get_class($this), '\\'),1);
        if ($this->mode === static::$PATIENT_POPUP_MODE) {
            return  $short_name . '_patient_popup';
        }
        if ($this->mode === static::$INLINE_EVENT_VIEW) {
            return $short_name . '_inline_event_view';
        }
        if ($this->mode === static::$PRESCRIPTION_PRINT_VIEW) {
            return $short_name . '_prescription_print_view';
        }
        return parent::getView();
    }

    /**
     * @return array
     */
    public  function getViewData()
    {
        if (in_array($this->mode, array(static::$PATIENT_POPUP_MODE, static::$PATIENT_SUMMARY_MODE, static::$PATIENT_LANDING_PAGE_MODE)) ) {
            return array_merge(parent::getViewData(), $this->getMergedEntries());
        }
        return parent::getViewData();

    }
}