<?php
/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2017
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2017, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */

namespace OEModule\OphCiExamination\widgets;

use OEModule\OphCiExamination\models\PastSurgery as PastSurgeryElement;
use OEModule\OphCiExamination\models\PastSurgery_Operation;

class PastSurgery extends \BaseEventElementWidget
{
    /**
     * @return PastSurgeryElement
     */
    protected function getNewElement()
    {
        return new PastSurgeryElement();
    }

    /**
     * @param AllergiesElement $element
     * @param $data
     * @throws \CException
     */
    protected function updateElementFromData($element, $data)
    {
        if  (!is_a($element, 'OEModule\OphCiExamination\models\PastSurgery')) {
            throw new \CException('invalid element class ' . get_class($element) . ' for ' . static::class);
        }

        // pre-cache current entries so any entries that remain in place will use the same db row
        $operations_by_id = array();
        foreach ($element->operations as $entry) {
            $operations_by_id[$entry->id] = $entry;
        }

        if (array_key_exists('operation', $data)) {
            $operations = array();
            foreach ($data['operation'] as $i => $operation) {
                $op_entry = new PastSurgery_Operation();
                $id = $data['id'][$i];
                if ($id && array_key_exists($id, $operations_by_id)) {
                    $op_entry = $operations_by_id[$id];
                }
                $op_entry->operation = $operation;
                $op_entry->side_id = $data['side_id'][$i];
                $op_entry->date = $data['date'][$i];
                $operations[] = $op_entry;
            }
            $element->operations = $operations;
        } else {
            $element->operations = array();
        }
    }
    
    public function popupList()
    {
        $res = array_map(function ($op) { return (string) $op ;}, $this->element->orderedOperations);
        return implode($this->popupListSeparator, $res);
    }

    public function getMergedOperations()
    {
        // map the operations that have been recorded as entries in this element
        $operations = array_map(
            function($op) {
                return array(
                    'date' => $op->date,
                    'object' => $op
                );
            }, $this->element->operations);

        // append operations from op note
        if ($api = $this->getApp()->moduleAPI->get('OphTrOperationnote')) {
            $operations = array_merge($operations, $api->getOperationsSummaryData($this->patient));
        }

        // merge by sorting by date
        uasort($operations, function($a , $b) {
            return $a['date'] >= $b['date'] ? -1 : 1;
        });

        return $operations;
    }

    public  function getViewData()
    {
        return array_merge(parent::getViewData(), array(
            'operations' => $this->getMergedOperations()
        ));
    }
}