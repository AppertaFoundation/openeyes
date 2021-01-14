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

use OEModule\OphCiExamination\models\OphCiExamination_ColourVision_Method;
use OEModule\OphCiExamination\models\OphCiExamination_ColourVision_Reading;
use OEModule\OphCiExamination\models\OphCiExamination_ColourVision_Value;

class ColourVision extends \BaseEventElementWidget
{

    protected $temp_reading;
    protected $available_values;
    protected $available_methods;

    public function setTempReading(OphCiExamination_ColourVision_Reading $reading)
    {
        $this->temp_reading = $reading;
    }

    public function renderReadingsForElement($readings)
    {
        if (!is_array($readings)) {
            return;
        }

        foreach ($readings as $i => $reading) {
            $this->render($this->getViewForReading(), $this->getViewDataForReading($reading, (string) $i));
        }
    }

    public function renderReadingTemplateForSide($side)
    {
        $this->render($this->getViewForReading(), $this->getViewDataForReading($this->getReadingForSide($side)));
    }

    /**
     * @param $attr
     * @return mixed|string
     */
    public function getReadingAttributeLabel($attr)
    {
        return OphCiExamination_ColourVision_Reading::model()->getAttributeLabel($attr);

    }

    public function getMethods()
    {
        if (!$this->available_methods) {
            $this->available_methods = OphCiExamination_ColourVision_Method::model()->findAll();;
        }
        return $this->available_methods;
    }

    /**
     * Returns the possible values that can be recorded for a reading
     *
     * @return array|mixed|null
     */
    public function getReadingValues()
    {
        if (!$this->available_values) {
            $this->available_values = OphCiExamination_ColourVision_Value::model()->findAll();
        }

        return $this->available_values;
    }

    protected function updateElementFromData($element, $data)
    {
        $this->ensureRequiredDataKeysSet($data);

        return parent::updateElementFromData($element, $data);
    }

    protected function ensureRequiredDataKeysSet(&$data)
    {
        foreach (['right', 'left'] as $side) {
            if (!isset($data["{$side}_readings"]) && \SplitEventTypeElement::eyeHasSide('right', $data['eye_id'])) {
                $data["{$side}_readings"] = [];
            }
        }
    }

    protected function getViewForReading()
    {
        return $this->getViewNameForPrefix('ColourVision_Reading');
    }

    protected function getViewDataForReading(OphCiExamination_ColourVision_Reading $reading, $index = '{{row_count}}')
    {
        $side = '{{side}}';
        if ($reading->eye_id) {
            $side = $reading->isRight() ? 'right' : 'left';
        }
        return [
            'row_count' => $index,
            'field_prefix' => \CHtml::modelName($this->element) . "[{$side}_readings][{$index}]",
            'reading' => $reading
        ];
    }

    /**
     * @param string $side right|left
     * @return OphCiExamination_ColourVision_Reading
     */
    protected function getReadingForSide($side)
    {
        $reading = new OphCiExamination_ColourVision_Reading();
        $reading->eye_id = ($side === 'right') ? \Eye::RIGHT : \Eye::LEFT;
        return $reading;
    }
}