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

use OEModule\OphCiExamination\models\FamilyHistory as FamilyHistoryElement;
use OEModule\OphCiExamination\models\FamilyHistory_Entry;

class FamilyHistory extends \BaseEventElementWidget
{
    public static $moduleName = 'OphCiExamination';

    /**
     * @return FamilyHistoryElement
     */
    protected function getNewElement()
    {
        return new FamilyHistoryElement();
    }

    /**
     * @param FamilyHistoryElement $element
     * @param $data
     * @throws \CException
     */
    protected function updateElementFromData($element, $data)
    {
        if  (!is_a($element, 'OEModule\OphCiExamination\models\FamilyHistory')) {
            throw new \CException('invalid element class ' . get_class($element) . ' for ' . static::class);
        }

        if (array_key_exists('no_family_history', $data) && $data['no_family_history'] == 1) {
            // TODO: Think about the importance of this date information, and therefore whether it should
            // TODO: be preserved across change events for the family history

                if (!$element->no_family_history_date) {
                    $element->no_family_history_date = date('Y-m-d H:i:s');
                }
        } elseif ($element->no_family_history_date) {
            $element->no_family_history_date = null;
        }

        // pre-cache current entries so any entries that remain in place will use the same db row
        $entries_by_id = array();
        if (!$element->isNewRecord) {
            foreach ($element->entries as $entry) {
                $entries_by_id[$entry->id] = $entry;
            }
        }

        if (array_key_exists('entries', $data)) {
            $entries = array();
            foreach ($data['entries'] as $i => $history_entry) {
                $entry = new FamilyHistory_Entry();
                $id = $history_entry['id'];
                if ($id && array_key_exists($id, $entries_by_id)) {
                    $entry = $entries_by_id[$id];
                }
                $entry->relative_id = $history_entry['relative_id'];
                $entry->other_relative = $history_entry['other_relative'];
                $entry->side_id = $history_entry['side_id'];
                $entry->condition_id = $history_entry['condition_id'];
                $entry->other_condition = $history_entry['other_condition'];
                $entry->comments = $history_entry['comments'];
                $entries[] = $entry;
            }
            $element->entries = $entries;
        }else {
            $element->entries = array();
        }
    }
}