<?php
/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2017
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2017, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
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
    public static $moduleName = 'OphCiExamination';

    /**
     * @return HistoryMedicationsElement
     */
    protected function getNewElement()
    {
        return new HistoryMedicationsElement();
    }

    /**
     * Creates new Entry records for any prescription items that are not in the
     * current element.
     *
     * @return array
     */
    private function getEntriesForUntrackedPrescriptionItems()
    {
        $untracked = array();
        if ($api = $this->getApp()->moduleAPI->get('OphDrPrescription')) {
            $tracked_prescr_item_ids = array_map(
                function ($item) {
                    return $item->id;
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
     * @inheritdoc
     */
    protected function setElementFromDefaults()
    {
        parent::setElementFromDefaults();

        if ($untracked = $this->getEntriesForUntrackedPrescriptionItems()) {
            // tracking prescription items.
            $entries = $this->element->entries;
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
                foreach (array('originallyStopped', 'start_date', 'end_date', 'drug_id', 'medication_drug_id', 'medication_name', 'dose',
                             'frequency_id', 'route_id', 'option_id', 'stop_reason_id') as $k) {
                    $entry->{$k} = array_key_exists($k, $entry_data) ? $entry_data[$k] : null;
                }
                $entries[] = $entry;
            }
            $element->entries = $entries;
        } else {
            $element->entries = array();
        }
    }

    public function getMergedEntries()
    {
        // determine if there are any prescription items that are not tracked by the element
        if ($untracked = $this->getEntriesForUntrackedPrescriptionItems()) {
            $current = $this->element->currentOrderedEntries;
            $stopped = $this->element->stoppedOrderedEntries;
            foreach ($untracked as $u) {
                if ($u->end_date) {
                   $stopped[] = $u;
                } else {
                    $current[] = $u;
                }
            }

            uasort($current, function($a , $b) {
                return $a['start_date'] >= $b['start_date'] ? -1 : 1;
            });
            uasort($stopped, function($a , $b) {
                return $a['start_date'] >= $b['start_date'] ? -1 : 1;
            });

            return array(
                'current' => $current,
                'stopped' => $stopped
            );
        } else {
            return array(
                'current' => $this->element->currentOrderedEntries,
                'stopped' => $this->element->stoppedOrderedEntries);
        }
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

    protected function getView()
    {
        if ($this->mode === static::$PATIENT_POPUP_MODE) {
            return substr(strrchr(get_class($this), '\\'),1) . '_patient_popup';
        }
        return parent::getView();
    }

    /**
     * @return array
     */
    public  function getViewData()
    {
        if (in_array($this->mode, array(static::$PATIENT_POPUP_MODE, static::$PATIENT_SUMMARY_MODE)) ) {
            return array_merge(parent::getViewData(), $this->getMergedEntries());
        }
        return parent::getViewData();

    }
}