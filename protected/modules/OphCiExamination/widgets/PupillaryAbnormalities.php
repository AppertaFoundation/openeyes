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
 * @copyright Copyright (C) 2019, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

namespace OEModule\OphCiExamination\widgets;

use OEModule\OphCiExamination\models\PupillaryAbnormalities as PupillaryAbnormalitiesElement;
use OEModule\OphCiExamination\models\PupillaryAbnormalityEntry;

class PupillaryAbnormalities extends \BaseEventElementWidget
{
    public static $moduleName = 'OphCiExamination';
    protected $print_view = 'PupillaryAbnormalities_event_print';

    /**
     * @return PupillaryAbnormalitiesElement
     */
    protected function getNewElement()
    {
        return new PupillaryAbnormalitiesElement();
    }

    /**
     * @param PupillaryAbnormalitiesElement $element
     * @param $data
     * @throws \CException
     */
    protected function updateElementFromData($element, $data)
    {
        if (!is_a($element, PupillaryAbnormalitiesElement::class)) {
            throw new \CException('invalid element class ' . get_class($element) . ' for ' . static::class);
        }
        $element->eye_id = $data['eye_id'] ?? \Eye::BOTH;
        $entries_by_id = [];
        $entries = [];
        $sides = $this->resolveSideStringsForElement($element);

        foreach ($sides as $side) {
            if (array_key_exists($side . '_no_pupillaryabnormalities', $data) && $data[$side . '_no_pupillaryabnormalities'] === "1") {
                if (!$element->{'no_pupillaryabnormalities_date_' . $side}) {
                    $element->{'no_pupillaryabnormalities_date_' . $side} = date('Y-m-d H:i:s');
                }
            } else {
                $element->{'no_pupillaryabnormalities_date_' . $side} = null;
            }

            // pre-cache current entries so any entries that remain in place will use the same db row
            foreach ($element->{'entries_' . $side} as $entry) {
                $entries_by_id[$entry->id] = $entry;
            }

            if (array_key_exists('entries_' . $side, $data)) {
                foreach ($data['entries_' . $side] as $i => $entry) {
                    $abnormality_entry = new PupillaryAbnormalityEntry();
                    $id = $entry['id'];
                    if ($id && array_key_exists($id, $entries_by_id)) {
                        $abnormality_entry = $entries_by_id[$id];
                    }
                    $abnormality_entry->abnormality_id = $entry['abnormality_id'];
                    $abnormality_entry->has_abnormality = array_key_exists('has_abnormality', $entry) ? $entry['has_abnormality'] : null;
                    $abnormality_entry->comments = $entry['comments'];
                    $abnormality_entry->eye_id = $entry['eye_id'];
                    $entries[] = $abnormality_entry;
                }
            }

            $element->entries = $entries;
        }
    }

    /**
     * Gets all required pupillary abnormalities
     * @return mixed
     */
    public function getRequiredAbnormalities()
    {
        $exam_api = \Yii::app()->moduleAPI->get('OphCiExamination');
        return $exam_api->getRequiredAbnormalities($this->patient);
    }

    /**
     * Gets all required missing pupillary abnormalities
     * @return array
     */
    public function getMissingRequiredAbnormalities($side)
    {
        $current_ids = array_map(function ($e) {
            return $e->abnormality_id;
        }, $this->element->{'entries_' . $side});

        $missing = [];
        foreach ($this->getRequiredAbnormalities() as $required) {
            if (!in_array($required->id, $current_ids)) {
                $entry = new PupillaryAbnormalityEntry();
                $entry->abnormality_id = $required->id;
                $missing[] = $entry;
            }
        }

        return $missing;
    }

    public function isAbnormalitiesSet($element, $side)
    {
        foreach ($element->{'entries_' . $side} as $entry) {
            if ($entry->has_abnormality === (string)PupillaryAbnormalityEntry::$PRESENT) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param $row
     * @return bool
     */
    public function postedNotChecked($row, $side)
    {
        return \Helper::elementFinder(\CHtml::modelName($this->element) . ".entries_.$side.$row.has_abnormality", $_POST)
            === PupillaryAbnormalityEntry::$NOT_CHECKED;
    }

    private function resolveSideStringsForElement(PupillaryAbnormalitiesElement $element): array
    {
        return [
            \Eye::BOTH => ['right', 'left'],
            \Eye::RIGHT => ['right'],
            \Eye::LEFT => ['left']
        ][(int)$element->eye_id] ?? [];
    }
}
