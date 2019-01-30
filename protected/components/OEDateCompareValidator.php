<?php

/**
 * OpenEyes.
 *
 * Copyright OpenEyes Foundation, 2017
 *
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
class OEDateCompareValidator extends OEBaseDateValidator
{
    /**
     * @var string the name of the attribute to be compared with
     */
    public $compareAttribute;

    /**
     * @var bool whether the attribute value can be null or empty. Defaults to false.
     *           If this is true, it means the attribute is considered valid when it is empty.
     */
    public $allowEmpty = false;
    /**
     * @var bool whether the compare attribute value can be null or empty. Defaults to false.
     *           If this is true, it means the attribute is considered valid when the compare value is empty.
     */
    public $allowCompareEmpty = false;
    /**
     * @var string the operator for comparison. Defaults to '='.
     *             The followings are valid operators:
     *             <ul>
     *             <li>'=' or '==': validates to see if the two values are equal. If {@link strict} is true, the comparison
     *             will be done in strict mode (i.e. checking value type as well).</li>
     *             <li>'!=': validates to see if the two values are NOT equal. If {@link strict} is true, the comparison
     *             will be done in strict mode (i.e. checking value type as well).</li>
     *             <li>'>': validates to see if the value being validated is greater than the value being compared with.</li>
     *             <li>'>=': validates to see if the value being validated is greater than or equal to the value being compared with.</li>
     *             <li>'<': validates to see if the value being validated is less than the value being compared with.</li>
     *             <li>'<=': validates to see if the value being validated is less than or equal to the value being compared with.</li>
     *             </ul>
     */
    public $operator = '=';

    /**
     * @param CModel $object
     * @param string $attribute
     *
     * @throws CException
     */
    protected function validateAttribute($object, $attribute)
    {
        $message = null;

        if (!$object->$attribute) {
            if (!$this->allowEmpty) {
                $message = $this->message ?: Yii::t('yii', '{attribute} cannot be empty.');
            }
        } elseif (!$object->{$this->compareAttribute}) {
            if (!$this->allowCompareEmpty) {
                $message = $this->message ?: Yii::t('yii', '{compareAttribute} cannot be empty.');
            }
        } else {
            $value = $this->parseDateValue($object->$attribute);
            $compareValue = $this->parseDateValue($object->{$this->compareAttribute});

            if ($value && $compareValue) {
                $message = $this->doComparison($value, $compareValue);
            } else {
                $message = $this->message ?: Yii::t('yii', '{attribute} and {compareAttribute} must be dates for comparison');
            }
        }

        if ($message) {
            $this->addError($object, $attribute, $message, array(
                '{compareAttribute}' => $object->getAttributeLabel($this->compareAttribute),
                '{compareValue}' => $object->{$this->compareAttribute}, ));
        }
    }

    public function doComparison($value, $compareValue)
    {
        $message = null;

        switch ($this->operator) {
            case '=':
            case '==':
                if ($value != $compareValue) {
                    $message = $this->message !== null ? $this->message : Yii::t('yii', '{attribute} must be repeated exactly.');
                }
                break;
            case '!=':
                if ($value == $compareValue) {
                    $message = $this->message !== null ? $this->message : Yii::t('yii', '{attribute} must not be equal to "{compareValue}".');
                }
                break;
            case '>':
                if ($value <= $compareValue) {
                    $message = $this->message !== null ? $this->message : Yii::t('yii', '{attribute} must be greater than "{compareValue}".');
                }
                break;
            case '>=':
                if ($value < $compareValue) {
                    $message = $this->message !== null ? $this->message : Yii::t('yii', '{attribute} must be greater than or equal to "{compareValue}".');
                }
                break;
            case '<':
                if ($value >= $compareValue) {
                    $message = $this->message !== null ? $this->message : Yii::t('yii', '{attribute} must be less than "{compareValue}".');
                }
                break;
            case '<=':
                if ($value > $compareValue) {
                    $message = $this->message !== null ? $this->message : Yii::t('yii', '{attribute} must be less than or equal to "{compareValue}".');
                }
                break;
            default:
                throw new CException(Yii::t('yii', 'Invalid operator "{operator}".', array('{operator}' => $this->operator)));
        }

        return $message;
    }
}
