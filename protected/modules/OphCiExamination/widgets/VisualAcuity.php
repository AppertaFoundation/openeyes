<?php
/**
 * (C) Apperta Foundation, 2020
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2020, Apperta Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

namespace OEModule\OphCiExamination\widgets;

use OEModule\OphCiExamination\models\Element_OphCiExamination_NearVisualAcuity;
use OEModule\OphCiExamination\models\OphCiExamination_NearVisualAcuity_Reading;
use OEModule\OphCiExamination\models\OphCiExamination_VisualAcuity_Reading;
use OEModule\OphCiExamination\models\OphCiExamination_VisualAcuityUnit;

class VisualAcuity extends \BaseEventElementWidget
{
    protected $is_for_near_state;

    public function renderReadingsForElement($readings)
    {
        if (!is_array($readings)) {
            return;
        }

        foreach ($readings as $i => $reading) {
            $this->render($this->getViewForReading(), $this->getViewDataForReading($reading, (string)$i));
        }
    }

    public function getJsonUnitOptions($for_element)
    {
        $is_near = get_class($for_element) === Element_OphCiExamination_NearVisualAcuity::class;

        $result = [];
        foreach (
            OphCiExamination_VisualAcuityUnit::model()
                 ->with('selectableValues')
                 ->findAllByAttributes([($is_near ? 'is_near' : 'is_va') => 1, 'active' => true]) as $unit
        ) {
            // logMar of 0.3 is the most likely region of VA to be recorded,
            // which equates to a base_value of 95. Set the closest to this to be
            // the default
            $default_base_value = $this->getClosestBaseValue($unit->selectableValues, 95);
            $result[] = $this->structureVAUnitWithDefaultBaseValue($unit, $default_base_value);
        }

        return \CJSON::encode($result);
    }

    public function getReadingAttributeLabel($attribute)
    {
        return ($this->getReadingClass())::model()->getAttributeLabel($attribute);
    }

    public function renderReadingTemplateForSide($side)
    {
        $this->render(
            $this->getViewForReading(),
            $this->getViewDataForReading($this->getReadingForSide($side))
        );
    }

    public function readingsHaveFixation()
    {
        return $this->getReadingClass() === OphCiExamination_VisualAcuity_Reading::class;
    }

    /**
     * @return bool
     */
    public function shouldTrackCviAlert()
    {
        return !$this->isForNear() && $this->getApp()->moduleAPI->get('OphCoCvi') !== false;
    }

    protected function isForNear()
    {
        if ($this->is_for_near_state === null) {
            $element = $this->element;
            if (!$element) {
                $element = $this->getNewElement();
            }
            $this->is_for_near_state = strpos(get_class($element), 'Near');
        }


        return $this->is_for_near_state;
    }

    protected function getViewForReading()
    {
        return $this->getViewNameForPrefix('VisualAcuity_Reading');
    }

    protected function getViewDataForReading(OphCiExamination_VisualAcuity_Reading $reading, $index = '{{row_count}}')
    {
        $side = $reading->getSideString();

        return [
            'row_count' => $index,
            'field_prefix' => \CHtml::modelName($this->element) . "[{$side}_readings][{$index}]",
            'reading' => $reading
        ];
    }

    protected function getReadingForSide($side)
    {
        $cls = $this->getReadingClass();
        $reading = new $cls();
        $reading->setSideByString($side);

        return $reading;
    }

    protected function getReadingClass()
    {
        $element = $this->element;

        if (!$element) {
            $element = $this->getNewElement();
        }

        return strpos(get_class($element), 'Near')
            ? OphCiExamination_NearVisualAcuity_Reading::class
            : OphCiExamination_VisualAcuity_Reading::class;
    }

    /**
     * @param $unit_values
     * @param $base_value
     * @return mixed|null
     */
    protected function getClosestBaseValue($unit_values, $base_value)
    {
        return array_reduce(
            $unit_values,
            function ($carry, $unit_value) use ($base_value) {
                return ($carry === null || abs($base_value - $carry) > abs($base_value - $unit_value['base_value']))
                    ? $unit_value['base_value'] : $carry;
            }
        );
    }

    /**
     * @param $unit
     * @param $default_base_value
     * @return array
     */
    protected function structureVAUnitWithDefaultBaseValue($unit, $default_base_value)
    {
        return [
            'id' => $unit->id,
            'name' => $unit->name,
            'values' => array_map(function ($unit_value) use ($default_base_value) {
                return [
                    'base_value' => $unit_value->base_value,
                    'value' => $unit_value->value,
                    'default' => $unit_value->base_value === $default_base_value
                ];
            }, $unit->selectableValues)
        ];
    }
}
