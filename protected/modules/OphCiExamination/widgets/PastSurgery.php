<?php

/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2019
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2019, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

namespace OEModule\OphCiExamination\widgets;

use OEModule\OphCiExamination\models\PastSurgery as PastSurgeryElement;
use OEModule\OphCiExamination\models\PastSurgery_Operation;
use CommonPreviousOperation;
use ReferenceData;

class PastSurgery extends \BaseEventElementWidget
{
    public $pro_theme;

    /**
     * @return PastSurgeryElement
     */
    protected function getNewElement()
    {
        return new PastSurgeryElement();
    }

    public function getRequiredOperation()
    {
        $exam_api = $this->getApp()->moduleAPI->get('OphCiExamination');
        return $exam_api->getRequiredOphthalmicSurgicalHistory($this->patient) ?? [];
    }

    public function getMissingRequiredOperation()
    {
        $current_operations = array_map(function ($e) {
            return $e->operation;
        }, $this->element->operations);
        $missing = [];
        foreach ($this->getRequiredOperation() as $req_operation) {
            if (!in_array($req_operation, $current_operations)) {
                $entry = new PastSurgery_Operation();
                $entry->operation = $req_operation;
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
        return CommonPreviousOperation::model()->findAllAtLevel(ReferenceData::LEVEL_INSTITUTION, ['order' => 'display_order asc']);
    }

    /**
     * @param PastSurgeryElement $element
     * @param $data
     * @throws \CException
     */
    protected function updateElementFromData($element, $data)
    {
        if (!is_a($element, 'OEModule\OphCiExamination\models\PastSurgery')) {
            throw new \CException('invalid element class ' . get_class($element) . ' for ' . static::class);
        }

        if (array_key_exists('no_pastsurgery', $data) && $data['no_pastsurgery'] === '1') {
            if (!$element->no_pastsurgery_date) {
                $element->no_pastsurgery_date = date('Y-m-d H:i:s');
            }
        } else {
            $element->no_pastsurgery_date = null;
        }

        $element->found_previous_op_notes = $data['found_previous_op_notes'] ?? null;
        $element->comments = $data['comments'] ?? null;

        // pre-cache current entries so any entries that remain in place will use the same db row
        $operations_by_id = array();
        foreach ($element->operations as $entry) {
            $operations_by_id[$entry->id] = $entry;
        }

        if (array_key_exists('operation', $data)) {
            $operations = array();
            foreach ($data['operation'] as $i => $operation) {
                $op_entry = new PastSurgery_Operation();
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
            $element->operations = array();
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
        $res = [\Helper::formatFuzzyDate($operation['date'])];
        if (array_key_exists('side', $operation)) {
            $res[] = $operation['side'];
        }
        $res[] = $operation['operation'] .
            ($operation['link'] ? ' <a href="' . $operation['link'] . '"><span class="js-has-tooltip fa oe-i eye small" data-tooltip-content="View operation note"></span></a>' : '');
        return implode(' ', $res);
    }

    /**
     * @return string
     */
    public function popupList()
    {
        $res = array_map(
            function ($op) {
                return array_key_exists('object', $op) ?
                    $op['object']->getDisplayDate() . ' ' . $op['object']->getDisplayOperation(false) :
                    $this->formatExternalOperation($op);
            },
            $this->getMergedOperations()
        );
        return implode($this->popupListSeparator, $res);
    }

    /**
     * @return array
     */
    protected function getMergedOperations($include_no = false)
    {
        // map the operations that have been recorded as entries in this element
        $operations = [];

        foreach ($this->element->operations as $_operation) {
            if ($_operation->had_operation || $include_no) {
                $operations[] = array(
                    'date' => $_operation->date,
                    'object' => $_operation
                );
            }
        }

        // append operations from op note
        $operations = array_merge($operations, $this->getOpnoteSummaryData());

        // merge by sorting by date
        uasort($operations, function ($a, $b) {
            return strtotime($a['date']) >= strtotime($b['date']) ? -1 : 1;
        });

        return $operations;
    }

    public function getRecordedOperationsArray()
    {
        $operations = [];
        $required = [];
        $required_operation_list = $this->getRequiredOperation();

        foreach ($this->element->operations as $i => $op) {
            if (in_array($op->operation, $required_operation_list)) {
                $operations[] = ['op' => $op, 'required' => true,];
            } else {
                $required[] = ['op' => $op, 'required' => false,];
            }
        }

        // append $required to the end of $operations
        return array_merge($operations, $required);
    }

    public function getOtherModulesOperationsSummaryData()
    {
        return $this->getOpnoteSummaryData();
    }

    protected function getOpnoteSummaryData()
    {
        /** @var \OphTrOperationnote_API $api */
        $api = $this->getApp()->moduleAPI->get('OphTrOperationnote');
        return $api ? $api->getOperationsSummaryData($this->patient) : [];
    }

    /**
     * @return array
     */
    public function getViewData()
    {
        return array_merge(parent::getViewData(), array(
            'operations' => $this->getMergedOperations($this->mode !== self::$PATIENT_SUMMARY_MODE),
        ));
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
            == PastSurgery_Operation::$NOT_CHECKED;
    }
}
