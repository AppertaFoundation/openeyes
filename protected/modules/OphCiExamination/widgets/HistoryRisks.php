<?php
/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2019
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
 * @copyright Copyright (c) 2019, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

namespace OEModule\OphCiExamination\widgets;

use OEModule\OphCiExamination\models\HistoryRisks as HistoryRisksElement;
use OEModule\OphCiExamination\models\HistoryRisksEntry;
use OEModule\OphCiExamination\models\OphCiExaminationRisk;

class HistoryRisks extends \BaseEventElementWidget
{
    public static $moduleName = 'OphCiExamination';
    protected $print_view = 'HistoryRisks_event_print';

    const NOT_CHECKED_REQUIRED_RISKS = [
        'Anticoagulants',
        'Alpha blockers',
    ];

    /**
     * @return HistoryRisksElement
     */
    protected function getNewElement()
    {
        return new HistoryRisksElement();
    }

    public function getRequiredRisks()
    {
        $exam_api = \Yii::app()->moduleAPI->get('OphCiExamination');
        return $exam_api->getRequiredRisks($this->patient);
    }

    public function getRiskOptions()
    {
        $force = array();
        foreach ($this->element->entries as $entry) {
            $force[] = $entry->risk_id;
        }

        $ignore = array_map(function($r) { return $r->id;
        }, $this->getRequiredRisks());

        $criteria = new \CDbCriteria();
        $criteria->addNotInCondition('id', $ignore);

        return OphCiExaminationRisk::model()->activeOrPk($force)->findAll($criteria);
    }

    public function getMissingRequiredRisks()
    {
        $current_ids = array_map(function ($e) { return $e->risk_id;
        }, $this->element->entries);
        $missing = array();
        foreach ($this->getRequiredRisks() as $required) {
            if (!in_array($required->id, $current_ids)) {
                $entry = new HistoryRisksEntry();
                $entry->risk_id = $required->id;
                $missing[] = $entry;
            }
        }
        return $missing;

    }

    /**
     * @param HistoryRisksElement $element
     * @param $data
     * @throws \CException
     */
    protected function updateElementFromData($element, $data)
    {
        if (!is_a($element, 'OEModule\OphCiExamination\models\HistoryRisks')) {
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
                $id = $entry_data['id'] ?? null;
                $entry = ($id && array_key_exists($id, $entries_by_id)) ?
                    $entries_by_id[$id] :
                    new HistoryRisksEntry();

                $entry->risk_id = $entry_data['risk_id'];
                $entry->has_risk = array_key_exists('has_risk', $entry_data) ? $entry_data['has_risk'] : null;
                $entry->other = $entry_data['other'] ?? null;
                $entry->comments = $entry_data['comments'] ?? null;
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
            \CHtml::modelName($this->element) . ".entries.$row.has_risk",
            $_POST
        )
            == HistoryRisksEntry::$NOT_CHECKED;
    }

    public function getNotCheckedRequiredRisks($element) {
        // Anticoagulants and alpha blockers being mandatory risk items to be displayed,
        // we check if $element contains these in either yes, or no and if it doesn't in either,
        // we display it as unchecked forcefully
        $entries = array_merge($element->getEntriesDisplay('present'), $element->getEntriesDisplay('not_present'));
        $recorded_risks = [];

        foreach ($entries as $entry) {
            foreach (self::NOT_CHECKED_REQUIRED_RISKS as $risk) {
                if (strpos($entry['risk'], $risk) !== false) {
                    $recorded_risks[$risk] = true;
                }
            }
        }

        $not_checked_required_risks = [];
        foreach (self::NOT_CHECKED_REQUIRED_RISKS as $risk) {
            if (!isset($recorded_risks[$risk]) || !$recorded_risks[$risk]) {
                $not_checked_required_risks[] = $risk;
            }
        }

        return $not_checked_required_risks;
    }
}
