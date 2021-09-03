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

use OEModule\OphCiExamination\models\NinePositions as NinePositionsModel;
use OEModule\OphCiExamination\models\NinePositions_AlignmentForGaze;
use OEModule\OphCiExamination\models\NinePositions_HorizontalEDeviation;
use OEModule\OphCiExamination\models\NinePositions_Reading;

class NinePositions extends \BaseEventElementWidget
{
    public static $READING_FORM_MODE = 256; // form for an individual reading
    public $reading_index = 1;

    /**
     * Various fields on readings can be disabled through settings
     * Values are still recorded if they are submitted, but the settings
     * are used to determine if the form should be displayed.
     */
    public const ENABLE_DVD = 'enable_dvd';
    public const ENABLE_HEAD_POSTURE = 'enable_head_posture';
    public const ENABLE_CORRECTION = 'enable_correction';
    public const ENABLE_WONG_SUPINE_POSITIVE = 'enable_wong_supine_positive';
    public const ENABLE_HESS_CHART = 'enable_hess_chart';

    protected array $reading_attribute_map;

    public function run()
    {
        if ($this->inReadingFormMode()) {
            return $this->renderReadingTemplateWithIndex();
        }
        return parent::run();
    }

    public function renderReadingsForElement(NinePositionsModel $element)
    {
        $readings = $element->readings;
        if ($this->inEditMode() && !count($readings)) {
            // always need a reading when editing
            $readings = [new NinePositions_Reading()];
        }

        foreach ($readings as $i => $reading) {
            $this->render($this->getViewForReading(), $this->getViewDataForReading($reading, (string)$i));
        }
    }

    public function renderReadingTemplateWithIndex()
    {
        $this->render(
            $this->getViewForReading(),
            array_merge(
                $this->getViewDataForReading(new NinePositions_Reading(), $this->reading_index),
                [
                    'suppress_ed_globals' => true
                ]
            )
        );
    }

    public function renderReadingAlignment(NinePositions_Reading $reading, string $gazeType)
    {
        $this->render($this->getViewForAlignmentForGaze(), ['alignment' => $reading->getAlignmentForGazeType($gazeType)]);
    }

    public function renderReadingMovement(NinePositions_Reading $reading, string $side, string $gazeType)
    {
        $this->render($this->getViewForMovementForGaze(), ['movement' => $reading->getMovementForGazeType($side, $gazeType)]);
    }

    public function getJsonHorizontalPrismPositionOptions()
    {
        return \CJSON::encode(
            array_map(function ($position) {
                return [
                    'id' => $position,
                    'label' => $position
                ];
            }, NinePositions_AlignmentForGaze::HORIZONTAL_PRISMS)
        );
    }

    public function getJsonHorizontalEDeviationOptions()
    {
        return \CJSON::encode(
            array_map(function ($deviation) {
                return [
                    'id' => $deviation->id,
                    'label' => $deviation->abbreviation
                ];
            }, NinePositions_AlignmentForGaze::model()
                ->horizontal_e_deviation_options)
        );
    }

    public function getJsonHorizontalXDeviationOptions()
    {
        return \CJSON::encode(
            array_map(function ($deviation) {
                return [
                    'id' => $deviation->id,
                    'label' => $deviation->abbreviation
                ];
            }, NinePositions_AlignmentForGaze::model()
                ->horizontal_x_deviation_options)
        );
    }

    public function getJsonVerticalPrismPositionOptions()
    {
        return \CJSON::encode(
            array_map(function ($position) {
                return [
                    'id' => $position,
                    'label' => $position
                ];
            }, NinePositions_AlignmentForGaze::VERTICAL_PRISMS)
        );
    }

    public function getJsonVerticalDeviationOptions()
    {
        return \CJSON::encode(
            array_map(function ($deviation) {
                return [
                    'id' => $deviation->id,
                    'label' => $deviation->abbreviation
                ];
            }, NinePositions_AlignmentForGaze::model()
                ->vertical_deviation_options)
        );
    }

    /**
     * Generates the string of data attributes that should be displayed for the reading
     * Note that this does not check what attribute are enabled in the system, to ensure it displays
     * what is recorded, irrespective of what is currently configured on the system (which may have
     * changed since the reading was recorded)
     */
    public function getReadingMiscDataDisplayList(NinePositions_Reading $reading)
    {
        $attrs = [
            'display_with_correction',
            'display_with_head_posture',
            'display_wong_supine_positive',
            'display_hess_chart',
            'display_full_ocular_movement',
            'display_comments'
        ];
        return array_filter(
            array_map(
                function ($attr) use ($reading) {
                    return $reading->$attr;
                },
                $attrs
            ),
            function ($display_value) {
                return strlen($display_value) > 0;
            }
        );
    }

    /**
     * @param $attributes
     * @return bool
     */
    public function areReadingAttributesEnabled($attributes)
    {
        foreach ($attributes as $attribute) {
            if ($this->isReadingAttributeEnabled($attribute)) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param $attribute
     * @return bool|mixed
     */
    public function isReadingAttributeEnabled($attribute)
    {
        return $this->getReadingAttributeEnabledMap()[$attribute] ?? true;
    }

    /**
     * Build and cache the reading attribute map to determine which fields are enabled.
     *
     * @return array
     */
    protected function getReadingAttributeEnabledMap()
    {
        if (!isset($this->reading_attribute_map)) {
            $this->reading_attribute_map = [];
            $element_type = NinePositionsModel::model()->getElementType();
            array_map(
                function ($key) use ($element_type) {
                    $this->reading_attribute_map[$key] = \SettingMetadata::model()
                            ->getSetting($key, $element_type) === '1'; // ensure is a boolean
                },
                [
                    static::ENABLE_DVD, static::ENABLE_CORRECTION, static::ENABLE_HEAD_POSTURE,
                    static::ENABLE_WONG_SUPINE_POSITIVE, static::ENABLE_HESS_CHART]
            );
        }

        return $this->reading_attribute_map;
    }

    protected function inReadingFormMode(?int $mode = null)
    {
        if (is_null($mode)) {
            $mode = $this->mode;
        }
        return $mode === static::$READING_FORM_MODE;
    }

    protected function updateElementFromData($element, $data)
    {
        // movements are submitted regardless of whether a value is set or not.
        // if the movement value is empty, then we discard it
        $data['readings'] = $this->removeMovementDataWithNoValueFromReadings($data['readings']);
        return parent::updateElementFromData($element, $data); // TODO: Change the autogenerated stub
    }

    protected function getNewElement()
    {
        return new NinePositionsModel();
    }

    protected function ensureRequiredDataKeysSet(&$data)
    {
        if (!isset($data['readings'])) {
            $data['readings'] = [];
        }
    }

    protected function getViewForReading()
    {
        return $this->getViewNameForPrefix('NinePositions_Reading');
    }

    protected function getViewForAlignmentForGaze()
    {
        return $this->getViewNameForPrefix('NinePositions_AlignmentForGaze');
    }

    protected function getViewForMovementForGaze()
    {
        return $this->getViewNameForPrefix('NinePositions_MovementForGaze');
    }

    protected function getViewDataForReading(NinePositions_Reading $reading, $index = '{{row_count}}')
    {
        return [
            'row_count' => $index,
            'field_prefix' => \CHtml::modelName($this->element) . "[readings][{$index}]",
            'element' => $this->element,
            'reading' => $reading,
            'form' => $this->form,
            'suppress_ed_globals' => false,
            'data_list' => $this->getReadingMiscDataDisplayList($reading)
        ];
    }

    protected function getViewNameForPrefix($prefix)
    {
        if ($this->inReadingFormMode()) {
            return $prefix . '_event_edit';
        }
        return parent::getViewNameForPrefix($prefix);
    }

    protected function validateMode($mode)
    {
        return $this->inReadingFormMode($mode) || parent::validateMode($mode);
    }

    private function removeMovementDataWithNoValueFromReadings($readings)
    {
        return array_map(function ($reading) {
            $reading['movements'] = array_filter($reading['movements'], function ($movement) {
                return !empty($movement['movement_id']);
            });
            return $reading;
        }, $readings);
    }
}
