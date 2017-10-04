<?php
/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2017
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public
 * License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later
 * version. OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the
 * implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more
 * details. You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled
 * COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2017, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
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

        if (array_key_exists('no_risks', $data)  && $data['no_risks'] == 1) {
            if (!$element->no_risks_date) {
                $element->no_risks_date = date('Y-m-d H:i:s');
            }
        } elseif ($element->no_risks_date) {
            $element->no_risks_date = null;
        }

        // pre-cache current entries so any entries that remain in place will use the same db row
        $entries_by_id = array();
        foreach ($element->entries as $entry) {
            $entries_by_id[$entry->id] = $entry;
        }

        if (array_key_exists('entries', $data)) {
            $entries = array();
            foreach ($data['entries'] as $entry_data) {
                $id = $entry_data['id'];
                $entry = ($id && array_key_exists($id, $entries_by_id)) ?
                    $entries_by_id[$id] :
                    new HistoryRisksEntry();

                $entry->risk_id = $entry_data['risk_id'];
                $entry->has_risk = array_key_exists('has_risk', $entry_data) ? $entry_data['has_risk'] : null;
                $entry->other = $entry_data['other'];
                $entry->comments = $entry_data['comments'];
                $entries[] = $entry;
            }
            $element->entries = $entries;
        } else {
            $element->entries = array();
        }
    }

    /**
     * @param $row
     * @return bool
     */
    public function postedNotChecked($row)
    {
        return \Helper::elementFinder(
            \CHtml::modelName($this->element) . ".entries.$row.has_risk", $_POST)
            == HistoryRisksEntry::$NOT_CHECKED;
    }
}
