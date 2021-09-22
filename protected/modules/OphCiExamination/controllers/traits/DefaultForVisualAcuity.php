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

namespace OEModule\OphCiExamination\controllers\traits;

use OEModule\OphCiExamination\models\Element_OphCiExamination_NearVisualAcuity;
use OEModule\OphCiExamination\models\Element_OphCiExamination_VisualAcuity;

trait DefaultForVisualAcuity
{
    /**
     * Set record mode for VA
     *
     * @param $element
     * @param $action
     * @phpcs:disable PSR1.Methods.CamelCapsMethodName.NotCamelCaps
     */
    protected function setElementDefaultOptions_Element_OphCiExamination_VisualAcuity(Element_OphCiExamination_VisualAcuity $element, $action)
    {
        if ($action === 'create') {
            $this->setVADefaultCreateOptions($element);
        }
    }

    /**
     * Set record mode for VA
     *
     * @param $element
     * @param $action
     * @phpcs:disable PSR1.Methods.CamelCapsMethodName.NotCamelCaps
     */
    protected function setElementDefaultOptions_Element_OphCiExamination_NearVisualAcuity(Element_OphCiExamination_NearVisualAcuity $element, $action)
    {
        if ($action === 'create') {
            $this->setVADefaultCreateOptions($element);
        }
    }

    /**
     * Sets the record mode and default sided-ness for that mode on the given VA element
     *
     * @param $element
     */
    protected function setVADefaultCreateOptions($element)
    {
        $element->record_mode =
            \SettingMetadata::model()->getSetting('record_mode', $element->getElementType())
            ?? Element_OphCiExamination_VisualAcuity::RECORD_MODE_SIMPLE;

        $element->setHasLeft();
        $element->setHasRight();

        if ($element->record_mode === Element_OphCiExamination_VisualAcuity::RECORD_MODE_COMPLEX) {
            $element->setHasBeo();
        }
    }


    /**
     * Ensure that reading values are set to empty if none are provided in data.
     *
     * @param $element
     * @param $data
     * @param $index
     * @phpcs:disable PSR1.Methods.CamelCapsMethodName.NotCamelCaps
     */
    protected function setComplexAttributes_Element_OphCiExamination_VisualAcuity($element, $data, $index)
    {
        $this->setVaComplexAttributes($element, $data);
    }

    /**
     * Ensure that reading values are set to empty if none are provided in data.
     *
     * @param $element
     * @param $data
     * @param $index
     * @phpcs:disable PSR1.Methods.CamelCapsMethodName.NotCamelCaps
     */
    protected function setComplexAttributes_Element_OphCiExamination_NearVisualAcuity($element, $data, $index)
    {
        $this->setVaComplexAttributes($element, $data);
    }

    protected function setVaComplexAttributes($element, $data)
    {
        $model_name = \CHtml::modelName($element);
        foreach (['left_readings', 'right_readings', 'beo_readings'] as $reading_relation) {
            if (!isset($data[$model_name][$reading_relation])) {
                $element->$reading_relation = [];
            }
        }
    }
}
