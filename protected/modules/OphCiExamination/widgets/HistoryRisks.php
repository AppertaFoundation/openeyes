<?php
/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2017
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public
 * License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later
 * version. OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the
 * implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more
 * details. You should have received a copy of the GNU General Public License along with OpenEyes in a file titled
 * COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2017, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */

namespace OEModule\OphCiExamination\widgets;

use OEModule\OphCiExamination\models\HistoryRisks as HistoryRisksElement;
use OEModule\OphCiExamination\models\HistoryRisksEntry;

class HistoryRisks extends \BaseEventElementWidget
{
    public static $moduleName = 'OphCiExamination';

    /**
     * @return HistoryRisksElement
     */
    protected function getNewElement()
    {
        return new HistoryRisksElement();
    }

    /**
     * @param HistoryRisksElement $element
     * @param $data
     * @throws \CException
     */
    protected function updateElementFromData($element, $data)
    {
        if  (!is_a($element, 'OEModule\OphCiExamination\models\HistoryRisks')) {
            throw new \CException('invalid element class ' . get_class($element) . ' for ' . static::class);
        }

        // pre-cache current entries so any entries that remain in place will use the same db row
        $entries_by_id = array();
        foreach ($element->entries as $entry) {
            $entries_by_id[$entry->id] = $entry;
        }

        if (array_key_exists('risk_id', $data)) {
            $entries = array();
            foreach ($data['risk_id'] as $i => $risk_id) {
                $entry = new HistoryRisksEntry();
                $id = $data['id'][$i];
                if ($id && array_key_exists($id, $entries_by_id)) {
                    $entry = $entries_by_id[$id];
                }
                $entry->risk_id = $risk_id;
                $entry->has_risk = $data['has_risk'][$i];
                $entry->other = $data['other'][$i];
                $entry->comments = $data['comments'][$i];
                $entries[] = $entry;
            }
            $element->entries = $entries;
        } else {
            $element->entries = array();
        }
    }
}