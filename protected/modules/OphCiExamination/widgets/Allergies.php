<?php
/**
 * (C) OpenEyes Foundation, 2017
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2017, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
namespace OEModule\OphCiExamination\widgets;

use OEModule\OphCiExamination\models\Allergies as AllergiesElement;
use OEModule\OphCiExamination\models\AllergyEntry;

class Allergies extends \BaseEventElementWidget
{
    public static $moduleName = 'OphCiExamination';

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
        if  (!is_a($element, 'OEModule\OphCiExamination\models\Allergies')) {
            throw new \CException('invalid element class ' . get_class($element) . ' for ' . static::class);
        }

        if (array_key_exists('no_allergies', $data)  && $data['no_allergies'] == 1) {
            // TODO: Think about the importance of this date information, and therefore whether it should
            // TODO: be preserved across change events for the family history
            if (!$element->no_allergies_date) {
                $element->no_allergies_date = date('Y-m-d H:i:s');
            }
        } elseif ($element->no_allergies_date) {
            $element->no_allergies_date = null;
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
                $allergy_entry->other = $entry['other'];
                $allergy_entry->comments = $entry['comments'];
                $entries[] = $allergy_entry;
            }
            $element->entries = $entries;
        } else {
            $element->entries = array();
        }
    }
}