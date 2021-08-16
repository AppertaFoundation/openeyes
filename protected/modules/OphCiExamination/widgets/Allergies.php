<?php
/**
 * (C) OpenEyes Foundation, 2019
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2019, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
namespace OEModule\OphCiExamination\widgets;

use OEModule\OphCiExamination\models\Allergies as AllergiesElement;
use OEModule\OphCiExamination\models\AllergyEntry;

class Allergies extends \BaseEventElementWidget
{
    public static $moduleName = 'OphCiExamination';
    protected $print_view = 'Allergies_event_print';
    /**
     * @return FamilyHistoryElement
     */
    protected function getNewElement()
    {
        return new AllergiesElement();
    }

    /**
     * @param AllergiesElement $element
     * @param $data
     * @throws \CException
     */
    protected function updateElementFromData($element, $data)
    {
        if (!is_a($element, 'OEModule\OphCiExamination\models\Allergies')) {
            throw new \CException('invalid element class ' . get_class($element) . ' for ' . static::class);
        }

        // pre-cache current entries so any entries that remain in place will use the same db row
        $entries_by_id = array();
        foreach ($element->entries as $entry) {
            $entries_by_id[$entry->id] = $entry;
        }

        if (array_key_exists('entries', $data)) {
            $entries = array();
            foreach ($data['entries'] as $i => $entry) {
                $allergy_entry = new AllergyEntry();
                $id = $entry['id'];
                if ($id && array_key_exists($id, $entries_by_id)) {
                    $allergy_entry = $entries_by_id[$id];
                }

                $allergy_entry->allergy_id = $entry['allergy_id'];
                if (array_key_exists('reactions', $entry)) {
                    $allergy_entry->reaction_ids = $entry['reactions'];
                }
                $allergy_entry->other = $entry['other'];
                $allergy_entry->comments = $entry['comments'];
                $allergy_entry->has_allergy = array_key_exists('has_allergy', $entry) ? $entry['has_allergy'] : null;
                $entries[] = $allergy_entry;
            }
            $element->entries = $entries;
        } else {
            $element->entries = array();
        }

        if ((array_key_exists('no_allergies', $data) && $data['no_allergies'] == 1) || $element->checkAllAllergiesAreSetNo()) {
            // TODO: Think about the importance of this date information, and therefore whether it should
            // TODO: be preserved across change events for the family history
            if (!$element->no_allergies_date) {
                $element->no_allergies_date = date('Y-m-d H:i:s');
            }
        } elseif ($element->no_allergies_date) {
            $element->no_allergies_date = null;
        }
    }

    /**
     * Gets all required allergies
     * @return mixed
     */
    public function getRequiredAllergies()
    {
        return $this->element->getRequiredAllergies($this->patient);
    }

    /**
     * Gets all required missing allergies
     * @return array
     */
    public function getMissingRequiredAllergies()
    {
        return $this->element->getMissingRequiredAllergies($this->patient);
    }

    public function isAllergiesSetYes($element)
    {
        foreach ($element->entries as $entry) {
            if ($entry->has_allergy === (string) AllergyEntry::$PRESENT) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param $row
     * @return bool
     */
    public function postedNotChecked($row)
    {
        return \Helper::elementFinder(\CHtml::modelName($this->element) . ".entries.$row.has_allergy", $_POST)
            == AllergyEntry::$NOT_CHECKED;
    }

    public function renderAllergies()
    {
        $this->render('Allergies_patient_mode', $this->getViewData());
    }
}
