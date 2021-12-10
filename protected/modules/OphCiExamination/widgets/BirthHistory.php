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

use OEModule\OphCiExamination\exceptions\InvalidInputException;
use OEModule\OphCiExamination\models\BirthHistory as BirthHistoryElement;

class BirthHistory extends \BaseEventElementWidget
{
    /**
     * @var BirthHistoryElement
     */
    public $element;
    public static $INPUT_KGS_FLD = 'input_weight_kgs';
    public static $INPUT_LB_PORTION_FLD = 'input_weight_lbs';
    public static $INPUT_OZ_PORTION_FLD = 'input_weight_ozs';
    public static $INPUT_KGS_MODE = 'kgs';
    public static $INPUT_LB_OZS_MODE = 'lbs';

    /**
     * Get the current input weight mode for the element
     * @return string
     */
    public function inputWeightMode()
    {
        return $this->getInputWeightModeFromData() ??
            ($this->getInputWeightModeFromElement() ?? self::$INPUT_KGS_MODE);
    }

    /**
     * Accessor for converting the recorded weight to the input format
     * in kgs
     *
     * @return float|int
     */
    public function getInputWeightKgs()
    {
        return $this->data[self::$INPUT_KGS_FLD] ??
            ($this->element->weight_grams ? $this->element->weight_grams / 1000 : null);
    }

    public function getInputWeightLbsPortion()
    {
        return $this->element->weight_ozs ? $this->element->calc_input_lbs() : null;
    }

    public function getInputWeightOzsPortion()
    {
        return $this->element->weight_ozs ? $this->element->calc_input_ozs() : null;
    }

    /**
     * @return BirthHistoryElement
     */
    protected function getNewElement()
    {
        return new BirthHistoryElement();
    }

    protected function updateElementFromData($element, $data)
    {
        parent::updateElementFromData($element, $data);

        $this->setEmptyWeight($element);

        if ($element->weight_grams = $this->getInputGrams($data)) {
            $element->weight_recorded_units = BirthHistoryElement::$WEIGHT_GRAMS;
        } else {
            try {
                if ($element->weight_ozs = $this->getInputOzs($data)) {
                    $element->weight_recorded_units = BirthHistoryElement::$WEIGHT_OZS;
                };
            } catch (InvalidInputException $e) {
                $element->addExternalError('weight_ozs', $e->getMessage());
            }
        }
    }

    protected function setEmptyWeight($element)
    {
        foreach (['weight_grams', 'weight_recorded_units', 'weight_ozs'] as $attr) {
            $element->$attr = null;
        }
    }

    private function getInputWeightModeFromData()
    {
        return isset($this->data[self::$INPUT_KGS_FLD]) ? self::$INPUT_KGS_MODE :
            (isset($this->data[self::$INPUT_LB_PORTION_FLD]) ? self::$INPUT_LB_OZS_MODE : null);
    }

    private function getInputWeightModeFromElement()
    {
        return [
            BirthHistoryElement::$WEIGHT_GRAMS => self::$INPUT_KGS_MODE,
            BirthHistoryElement::$WEIGHT_OZS => self::$INPUT_LB_OZS_MODE
        ][$this->element->weight_recorded_units] ?? null;
    }

    private function getInputGrams($data)
    {
        $input = $data[self::$INPUT_KGS_FLD] ?? null;
        if ($input !== null) {
            if (is_numeric($input)) {
                $input = $input * 1000;
            }
        }

        return $input;
    }

    private function getInputOzs($data)
    {
        $input_lb = $data[self::$INPUT_LB_PORTION_FLD] ?? '';
        $input_oz = $data[self::$INPUT_OZ_PORTION_FLD] ?? '';
        if ($input_lb === '' && $input_oz === '') {
            return '';
        }

        return $this->parseInputLbOzs("{$input_lb}.{$input_oz}");
    }

    private function parseInputLbOzs($input)
    {
        if (!preg_match("/^(\d+)(\.(\d*)){0,1}$/", $input, $matches)) {
            throw new InvalidInputException('Not a valid value');
        }
        if ($matches[3] && $matches[3] > 15) {
            throw new InvalidInputException('Too many ozs');
        }
        return $matches[1] * 16 + ($matches[3] ?: 0);
    }
}