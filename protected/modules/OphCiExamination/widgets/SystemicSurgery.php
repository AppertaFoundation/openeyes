<?php

/**
 * OpenEyes.
 *
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

use OEModule\OphCiExamination\models\SystemicSurgery as SystemicSurgeryElement;
use OEModule\OphCiExamination\models\SystemicSurgery_Operation;
use CommonPreviousSystemicOperation;
use ReferenceData;

class SystemicSurgery extends \BaseEventElementWidget
{
    /**
     * @return SystemicSurgeryElement
     */
    protected function getNewElement()
    {
        return new SystemicSurgeryElement();
    }

    public function getRequiredOperations()
    {
        $exam_api = \Yii::app()->moduleAPI->get('OphCiExamination');
        return $exam_api->getRequiredSystemicSurgeries($this->patient);
    }

    public function getMissingRequiredOperations()
    {
        $current_operations = array_map(function ($e) {
            return $e->operation;
        }, $this->element->operations);
        $missing = [];
        foreach ($this->getRequiredOperations() as $required_operation) {
            if (!in_array($required_operation, $current_operations)) {
                $entry = new SystemicSurgery_Operation();
                $entry->operation = $required_operation;
                $missing[] = $entry;
            }
        }

        return $missing;
    }

    /**
     * @return array
     */
    public function getPreviousOperationOptions()
    {
        return CommonPreviousSystemicOperation::model()->findAllAtLevel(ReferenceData::LEVEL_INSTITUTION, ['order' => 'display_order asc']);
    }

    /**
     * @param SystemicSurgery $element
     * @param $data
     * @throws \CException
     */
    protected function updateElementFromData($element, $data)
    {
        if (!is_a($element, 'OEModule\OphCiExamination\models\SystemicSurgery')) {
            throw new \CException('invalid element class ' . get_class($element) . ' for ' . static::class);
        }

        if (array_key_exists('no_systemicsurgery', $data) && $data['no_systemicsurgery'] === '1') {
            $element->no_systemicsurgery_date = $element->no_systemicsurgery_date ? : date('Y-m-d H:i:s');
        } elseif ($element->no_systemicsurgery_date) {
            $element->no_systemicsurgery_date = null;
        }

        $element->comments = $data['comments'];

        // pre-cache current entries so any entries that remain in place will use the same db row
        $operations_by_id = [];
        foreach ($element->operations as $entry) {
            $operations_by_id[$entry->id] = $entry;
        }

        if (array_key_exists('operation', $data)) {
            $operations = [];
            foreach ($data['operation'] as $i => $operation) {
                $op_entry = new SystemicSurgery_Operation();
                $id = $operation['id'];
                if ($id && array_key_exists($id, $operations_by_id)) {
                    $op_entry = $operations_by_id[$id];
                }
                $op_entry->operation = $operation['operation'];
                $op_entry->side_id = $this->getEyeIdFromPost($operation);
                $op_entry->had_operation = array_key_exists('had_operation', $operation) ? $operation['had_operation'] : null;
                if ($operation['date']) {
                    list($year, $month, $day) = array_pad(explode('-', $operation['date']), 3, 0);
                    $op_entry->date = \Helper::padFuzzyDate($year, $month, $day);
                }

                $operations[] = $op_entry;
            }
            $element->operations = $operations;
        } else {
            $element->operations = [];
        }
    }

    /**
     * Simple function to format a given array data object into a string for display in the
     * popup list.
     *
     * @param array $operation ['date' => fuzzydatestring, 'side' => string, 'operation' => string]
     * @return string
     */
    protected function formatExternalOperation($operation)
    {
        $result = [\Helper::formatFuzzyDate($operation['date'])];
        if (array_key_exists('side', $operation)) {
            $result[] = $operation['side'];
        }
        $result[] = $operation['operation'] .
            ($operation['link'] ? ' <a href="' . $operation['link'] . '"><span class="js-has-tooltip fa oe-i eye small" data-tooltip-content="View operation note"></span></a>' : '');
        return implode(' ', $result);
    }

    /**
     * @return string
     */
    public function popupList()
    {
        $result = array_map(
            function ($operation) {
                return array_key_exists('object', $operation) ?
                    $operation['object']->getDisplayDate() . ' ' . $operation['object']->getDisplayOperation(false) :
                    $this->formatExternalOperation($operation);
            },
            $this->getMergedOperations()
        );
        return implode($this->popupListSeparator, $result);
    }

    /**
     * @return array
     */
    protected function getMergedOperations($include_non_existing_operations = false)
    {
        // map the operations that have been recorded as entries in this element
        $operations = [];

        foreach ($this->element->operations as $operation) {
            if ($operation->had_operation || $include_non_existing_operations) {
                $operations[] = [
                    'date' => $operation->date,
                    'object' => $operation
                ];
            }
        }

        // merge by sorting by date
        uasort($operations, function ($a, $b) {
            return $a['date'] >= $b['date'] ? -1 : 1;
        });

        return $operations;
    }

    public function getCurrentAndRequiredOperations()
    {
        $current_operations = [];
        $required_operations = [];
        $required_operation_list = $this->getRequiredOperations();

        foreach ($this->element->operations as $operation) {
            if (in_array($operation->operation, $required_operation_list)) {
                $current_operations[] = ['op' => $operation, 'required' => true, ];
            } else {
                $required_operations[] = ['op' => $operation, 'required' => false, ];
            }
        }

        // append $required to the end of $operations
        return array_merge($current_operations, $required_operations);
    }

    /**
     * @return array
     */
    public function getViewData()
    {
        return array_merge(parent::getViewData(), [
            'operations' => $this->getMergedOperations($this->mode !== self::$PATIENT_SUMMARY_MODE),
        ]);
    }

    /**
     * @param $row
     * @return bool
     */
    public function postedNotChecked($row)
    {
        return \Helper::elementFinder(
            \CHtml::modelName($this->element) . ".operation.$row.had_operation",
            $_POST
        )
            == SystemicSurgery_Operation::$NOT_CHECKED;
    }
}
