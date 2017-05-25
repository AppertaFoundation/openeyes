<?php
/**
 * (C) OpenEyes Foundation, 2017
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2017, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
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

        if (array_key_exists('no_family_history', $data)) {
            // TODO: Think about the importance of this date information, and therefore whether it should
            // TODO: be preserved across change events for the family history
            if ($data['no_family_history'] == 1) {
                if (!$element->no_family_history_date) {
                    $element->no_family_history_date = date('Y-m-d H:i:s');
                }
            } else {
                $element->no_family_history_date = null;
            }
        }

        // pre-cache current entries so any entries that remain in place will use the same db row
        $entries_by_id = array();
        if (!$element->isNewRecord) {
            foreach ($element->entries as $entry) {
                $entries_by_id[$entry->id] = $entry;
            }
        }

        if (array_key_exists('relative_id', $data)) {
            $entries = array();
            foreach ($data['relative_id'] as $i => $relative_id) {
                $entry = new FamilyHistory_Entry();
                $id = $data['id'][$i];
                if ($id && array_key_exists($id, $entries_by_id)) {
                    $entry = $entries_by_id[$id];
                }
                $entry->relative_id = $relative_id;
                $entry->other_relative = $data['other_relative'][$i];
                $entry->side_id = $data['side_id'][$i];
                $entry->condition_id = $data['condition_id'][$i];
                $entry->other_condition = $data['other_condition'][$i];
                $entry->comments = $data['comments'][$i];
                $entries[] = $entry;
            }
            $element->entries = $entries;
        }
    }
}