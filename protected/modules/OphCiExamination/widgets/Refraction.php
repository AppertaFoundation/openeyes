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

use OEModule\OphCiExamination\models\OphCiExamination_Refraction_Reading;

class Refraction extends \BaseEventElementWidget
{
    public function getReadingAttributeLabel($attribute)
    {
        return OphCiExamination_Refraction_Reading::model()->getAttributeLabel($attribute);
    }

    public function renderReadingTemplateForSide(string $side)
    {
        $reading = new OphCiExamination_Refraction_Reading();
        $reading->eye_id = [
            'right' => \Eye::RIGHT,
            'left' => \Eye::LEFT][$side] ?? null;

        $this->render($this->getViewForReading(), $this->getViewDataForReading($reading));
    }

    public function getReadingRefractionTypeOptions(OphCiExamination_Refraction_Reading $reading)
    {
        $options = $reading->type_options;

        return \CHtml::listData($options, 'id', 'name') + ['__other__' => 'Other'];
    }

    public function renderReadingsForElement($readings)
    {
        if (!is_array($readings)) {
            return;
        }

        foreach ($readings as $i => $reading) {
            $this->render(
                $this->getViewForReading(),
                array_merge($this->getViewDataForReading($reading, (string)$i), ['force_type' => true])
            );
        }
    }

    protected function getViewForReading()
    {
        return $this->getViewNameForPrefix('Refraction_Reading');
    }

    protected function getViewDataForReading(OphCiExamination_Refraction_Reading $reading, $index = '{{row_count}}')
    {
        $readings_key = ([
            \Eye::RIGHT => 'right',
            \Eye::LEFT => 'left'
        ][$reading->eye_id] ?? '') . '_readings';

        return [
            'row_count' => $index,
            'field_prefix' => \CHtml::modelName($this->element) . "[{$readings_key}][{$index}]",
            'reading' => $reading,
            'force_type' => false
        ];
    }
}
