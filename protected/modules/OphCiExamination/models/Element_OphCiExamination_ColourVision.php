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

use Yii;

/**
 * This is the model class for table "et_ophciexamination_colourvision".
 *
 * The followings are the available columns in table:
 *
 * @property string $id
 * @property int $event_id
 * @property int $eye_id
 * @property \EventType $eventType
 * @property \Event $event
 * @property \User $user
 * @property \User $usermodified
 * @property \Eye $eye
 * @property OphCiExamination_ColourVision_Reading $readings
 * @property OphCiExamination_ColourVision_Reading $left_readings
 * @property OphCiExamination_ColourVision_Reading $right_readings
 */
class Element_OphCiExamination_ColourVision extends \SplitEventTypeElement
{
    /**
     * Returns the static model of the specified AR class.
     *
     * @return Element_OphCiExamination_Dilation
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
        return 'et_ophciexamination_colourvision';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
                array('event_id, eye_id', 'safe'),
                array('id, event_id, eye_id', 'safe', 'on' => 'search'),
                array('left_readings', 'requiredIfSide', 'side' => 'left'),
                array('right_readings', 'requiredIfSide', 'side' => 'right'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'eventType' => array(self::BELONGS_TO, 'EventType', 'event_type_id'),
            'event' => array(self::BELONGS_TO, 'Event', 'event_id'),
            'user' => array(self::BELONGS_TO, 'User', 'created_user_id'),
            'usermodified' => array(self::BELONGS_TO, 'User', 'last_modified_user_id'),
            'eye' => array(self::BELONGS_TO, 'Eye', 'eye_id'),
            'readings' => array(self::HAS_MANY, 'OEModule\OphCiExamination\models\OphCiExamination_ColourVision_Reading', 'element_id'),
            'right_readings' => array(self::HAS_MANY, 'OEModule\OphCiExamination\models\OphCiExamination_ColourVision_Reading', 'element_id', 'on' => 'right_readings.eye_id = '.\Eye::RIGHT),
            'left_readings' => array(self::HAS_MANY, 'OEModule\OphCiExamination\models\OphCiExamination_ColourVision_Reading', 'element_id', 'on' => 'left_readings.eye_id = '.\Eye::LEFT),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'event_id' => 'Event',
            'eye_id' => 'Eye',
            'left_readings' => 'Readings',
            'right_readings' => 'Readings',
        );
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     *
     * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
     */
    public function search()
    {
        // Warning: Please modify the following code to remove attributes that
        // should not be searched.

        $criteria = new \CDbCriteria();

        $criteria->compare('id', $this->id, true);
        $criteria->compare('event_id', $this->event_id, true);

        return new \CActiveDataProvider(get_class($this), array(
                'criteria' => $criteria,
        ));
    }

    protected $_readings_by_method;

    /**
     * Get the colour vision reading for the given side and method if it's defined.
     *
     * @param string                               $side   - left or right
     * @param OphCiExamination_ColourVision_Method $method
     *
     * @return OphCiExamination_ColourVision_Readin|null
     */
    public function getReading($side, $method)
    {
        if (!$this->_readings_by_method) {
            $this->_readings_by_method = array(
            );
            foreach (array('left', 'right') as $side) {
                $this->_readings_by_method[$side] = array();
                foreach ($this->{$side.'_readings'} as $reading) {
                    $this->_readings_by_method[$side][$reading->method_id] = $reading;
                }
            }
        }

        return @$this->_readings_by_method[$side][$method->id];
    }

    /**
     * Get the colour vision reading methods that have not been used for this element.
     *
     * @param string $side
     *
     * @return OphCiExamination_ColourVision_Method[]
     */
    public function getUnusedReadingMethods($side)
    {
        $readings = $this->{$side.'_readings'};
        $criteria = new \CDbCriteria();
        $curr = array();
        foreach ($readings as $reading) {
            if ($meth = $reading->method) {
                $curr[] = $meth->id;
            }
        }

        $criteria->addNotInCondition('id', $curr);
        $criteria->order = 'display_order asc';

        return OphCiExamination_ColourVision_Method::model()->findAll($criteria);
    }

    /**
     * Get all the colour vision reading methods for this element.
     *
     * @return OphCiExamination_ColourVision_Method[]
     */
    public function getAllReadingMethods()
    {
        $criteria = new \CDbCriteria();
        $criteria->order = 'display_order asc';
        return OphCiExamination_ColourVision_Method::model()->findAll($criteria);
    }

    /**
     * Validate each of the readings.
     */
    protected function afterValidate()
    {
        foreach (array('left' => 'hasLeft', 'right' => 'hasRight') as $side => $checkFunc) {
            if ($this->$checkFunc()) {
                foreach ($this->{$side.'_readings'} as $i => $reading) {
                    if (!$reading->validate()) {
                        foreach ($reading->getErrors() as $fld => $err) {
                            $this->addError($side.'_readings', ucfirst($side).' reading ('.($i + 1).'): '.implode(', ', $err));
                        }
                    }
                }
            }
        }
    }

    /**
     * extends standard delete method to remove all the treatments.
     *
     * (non-PHPdoc)
     *
     * @see CActiveRecord::delete()
     */
    public function delete()
    {
        $transaction = Yii::app()->db->getCurrentTransaction() === null
                ? Yii::app()->db->beginTransaction()
                : false;

        try {
            foreach ($this->readings as $reading) {
                if (!$reading->delete()) {
                    throw new Exception('Delete reading failed: '.print_r($reading->getErrors(), true));
                }
            }
            if (parent::delete()) {
                if ($transaction) {
                    $transaction->commit();
                }
            } else {
                throw new \Exception('unable to delete');
            }
        } catch (\Exception $e) {
            if ($transaction) {
                $transaction->rollback();
            }
            throw $e;
        }
    }

    /**
     * Update the dilation treatments - depends on their only being one treatment of a particular drug on a given side.
     *
     * @param $side \Eye::LEFT or \Eye::RIGHT
     * @param array $readings
     *
     * @throws Exception
     */
    public function updateReadings($side, $readings)
    {
        $curr_by_id = array();
        $save = array();

        foreach ($this->readings as $r) {
            if ($r->eye_id == $side) {
                $curr_by_id[$r->method->id] = $r;
            }
        }

        foreach ($readings as $reading) {
            if (!$method_id = $reading['method_id']) {
                $method_id = OphCiExamination_ColourVision_Value::model()->findByPk($reading['value_id'])->method->id;
            }
            if (!array_key_exists($method_id, $curr_by_id)) {
                $obj = new OphCiExamination_ColourVision_Reading();
            } else {
                $obj = $curr_by_id[$method_id];
                unset($curr_by_id[$method_id]);
            }
            $obj->attributes = $reading;
            $obj->element_id = $this->id;
            $obj->eye_id = $side;
            $save[] = $obj;
        }

        foreach ($save as $s) {
            if (!$s->save()) {
                throw new \Exception('unable to save reading:'.print_r($s->getErrors(), true));
            };
        }

        foreach ($curr_by_id as $curr) {
            if (!$curr->delete()) {
                throw new \Exception('unable to delete reading:'.print_r($curr->getErrors(), true));
            }
        }
    }

    public function canViewPrevious()
    {
        return true;
    }
}
