<?php
/**
* OpenEyes.
*
* (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
* (C) OpenEyes Foundation, 2011-2013
* This file is part of OpenEyes.
* OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
* OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
* You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
*
* @link http://www.openeyes.org.uk
*
* @author OpenEyes <info@openeyes.org.uk>
* @copyright Copyright (c) 2011-2013, OpenEyes Foundation
* @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
*/

namespace OEModule\OphCiExamination\models;

use OE\factories\models\traits\HasFactory;

/**
 * This is the model class for table "et_ophciexamination_intraocularpressure".
 *
 * The followings are the available columns in table:
 *
 * @property string $id
 * @property int $event_id
 * @property int $eye_id
 * @property OphCiExamination_Instrument $left_instrument
 * @property OphCiExamination_Instrument $right_instrument
 * @property OphCiExamination_IntraocularPressure_Reading $left_reading
 * @property OphCiExamination_IntraocularPressure_Reading $right_reading
 */
class Element_OphCiExamination_IntraocularPressure extends \SplitEventTypeElement
{
    use traits\CustomOrdering;
    use HasFactory;

    public $service;

    protected $errorExceptions = array(
    'OEModule_OphCiExamination_models_Element_OphCiExamination_IntraocularPressure_right_values.reading_id' => 'OEModule_OphCiExamination_models_OphCiExamination_IntraocularPressure_Value_right_values',
    'OEModule_OphCiExamination_models_Element_OphCiExamination_IntraocularPressure_left_values.reading_id' => 'OEModule_OphCiExamination_models_OphCiExamination_IntraocularPressure_Value_left_values',
    );
    /**
     * Returns the static model of the specified AR class.
     *
     * @return the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'et_ophciexamination_intraocularpressure';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        return array(
            array('eye_id', 'required'),
            array('eye_id, left_comments, right_comments, right_values, left_values, right_integer_values, left_integer_values, right_qualitative_values, left_qualitative_values', 'safe'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        return array(
            'event' => array(self::BELONGS_TO, 'Event', 'event_id'),
            'eye' => array(self::BELONGS_TO, 'Eye', 'eye_id'),
            'user' => array(self::BELONGS_TO, 'User', 'created_user_id'),
            'usermodified' => array(self::BELONGS_TO, 'User', 'last_modified_user_id'),
            'right_values' => array(self::HAS_MANY, 'OEModule\OphCiExamination\models\OphCiExamination_IntraocularPressure_Value', 'element_id', 'on' => 'right_values.eye_id = '.\Eye::RIGHT),
            'left_values' => array(self::HAS_MANY, 'OEModule\OphCiExamination\models\OphCiExamination_IntraocularPressure_Value', 'element_id', 'on' => 'left_values.eye_id = '.\Eye::LEFT),
            'right_integer_values' => array(self::HAS_MANY, 'OEModule\OphCiExamination\models\OphCiExamination_IntraocularPressure_Value', 'element_id', 'on' => 'right_integer_values.eye_id = '.\Eye::RIGHT.' and right_integer_values.reading_id is not null'),
            'left_integer_values' => array(self::HAS_MANY, 'OEModule\OphCiExamination\models\OphCiExamination_IntraocularPressure_Value', 'element_id', 'on' => 'left_integer_values.eye_id = '.\Eye::LEFT.' and left_integer_values.reading_id is not null'),
            'right_qualitative_values' => array(self::HAS_MANY, 'OEModule\OphCiExamination\models\OphCiExamination_IntraocularPressure_Value', 'element_id', 'on' => 'right_qualitative_values.eye_id = '.\Eye::RIGHT.' and right_qualitative_values.qualitative_reading_id is not null'),
            'left_qualitative_values' => array(self::HAS_MANY, 'OEModule\OphCiExamination\models\OphCiExamination_IntraocularPressure_Value', 'element_id', 'on' => 'left_qualitative_values.eye_id = '.\Eye::LEFT.' and left_qualitative_values.qualitative_reading_id is not null'),
        );
    }

    public function afterValidate()
    {
        foreach (array('right' => 'hasRight', 'left' => 'hasLeft') as $side => $checker) {
            if ($this->$checker()) {
                if ($this->{"{$side}_values"}) {
                    foreach ($this->{"{$side}_values"} as $value) {
                        if (!$value->validate()) {
                            foreach ($value->getErrors() as $field => $errors) {
                                foreach ($errors as $error) {
                                    $this->addError("{$side}_values.{$field}", $error);
                                }
                            }
                        }
                    }
                } else {
                    if (!$this->{"{$side}_comments"}) {
                        $this->addError("{$side}_comments", "Comments are required when no readings are recorded ($side)");
                    }
                }
            }
        }
    }

    public function beforeDelete()
    {
        OphCiExamination_IntraocularPressure_Value::model()->deleteAll('element_id = ?', array($this->id));

        return parent::beforeDelete();
    }

    public function getLetter_reading($side)
    {
        $reading = $this->getReading($side);

        if (!$reading) {
            if ($this->{"{$side}_qualitative_values"}) {
                return 'Qualitative readings: '.implode(',', $this->getQualitativeReadings($side));
            }

            return 'Not recorded';
        }

        $return = "{$reading} mmHg".($this->isReadingAverage($side) ? ' (average)' : '');

        if ($this->{"{$side}_qualitative_values"}) {
            $return .= ', qualitative readings: '.implode(',', $this->getQualitativeReadings($side));
        }

        return $return;
    }

    public function getLetter_reading_first($side)
    {
        $reading = $this->getFirstReading($side);

        if (!$reading) {
            if ($this->{"{$side}_qualitative_values"}) {
                return 'Qualitative readings: '.implode(',', $this->getQualitativeReadings($side));
            }

            return 'Not recorded';
        }

        return "{$reading} mmHg";
    }

    public function getFirstReading($side)
    {
        if (!$values = $this->{"{$side}_integer_values"}) {
            return;
        }

        return $values[0]->reading->value;
    }

    public function getQualitativeReadings($side)
    {
        if ($this->{"{$side}_qualitative_values"}) {
            $qualitative_values = array();

            foreach ($this->{"{$side}_qualitative_values"} as $value) {
                $qualitative_values[] = $value->qualitative_reading->name;
            }

            return $qualitative_values;
        }

        return false;
    }

    public function getReadings($side)
    {
        $return_readings = array();
        $values = $this->{"{$side}_integer_values"};
        if (!$values) {
            return $return_readings;
        }

        foreach ($values as $value) {
            if ($value->reading) {
                $return_readings[] = $value->reading->value;
            }
        }

        return $return_readings;
    }

    public function getReading($side)
    {
        $values = $this->{"{$side}_integer_values"};
        if (!$values) {
            return;
        }

        $sum = 0;
        foreach ($values as $value) {
            if ($value->reading) {
                $sum += $value->reading->value;
            }
        }

        return round($sum / count($values));
    }

    /**
     * @param string $side
     *
     * @return bool
     */
    public function isReadingAverage($side)
    {
        return count($this->{"{$side}_values"}) > 1;
    }

    public function getLetter_string()
    {
        return "Intra-ocular pressure:\nright: ".$this->getLetter_reading('right')."\nleft: ".$this->getLetter_reading('left')."\n";
    }

    public function canViewPrevious()
    {
        return true;
    }

    public function canCopy()
    {
        return true;
    }

    public function getValues()
    {
        return array('right' => $this->right_values, 'left' => $this->left_values);
    }

    public function afterFind()
    {
        foreach (array('left', 'right') as $eye_side) {
            if (!$this->hasEye($eye_side)) {
                foreach ($this->{$eye_side .'_values'} as $value) {
                    $value->delete();
                }
            }
        }
        return parent::afterFind();
    }
}
