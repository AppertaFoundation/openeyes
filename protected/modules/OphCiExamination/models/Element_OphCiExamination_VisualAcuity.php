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
 * This is the model class for table "et_ophciexamination_visualacuity".
 *
 * The followings are the available columns in table:
 *
 * @property string $id
 * @property int $event_id
 * @property int $eye_id
 * @property bool $left_unable_to_assess
 * @property bool $right_unable_to_assess
 * @property bool $left_eye_missing
 * @property bool $right_eye_missing
 * @property bool $cvi_alert_dismissed
 *
 * The followings are the available model relations:
 * @property OphCiExamination_VisualAcuityUnit $unit
 * @property OphCiExamination_VisualAcuity_Reading[] $readings
 * @property OphCiExamination_VisualAcuity_Reading[] $left_readings
 * @property OphCiExamination_VisualAcuity_Reading[] $right_readings
 * @property User $user
 * @property User $usermodified
 * @property Eye eye
 * @property EventType $eventType
 * @property Event $event
 */
class Element_OphCiExamination_VisualAcuity extends \SplitEventTypeElement
{
    use traits\CustomOrdering;

    public $service;
    protected $auto_update_relations = true;
    protected $relation_defaults = array(
        'left_readings' => array(
            'side' => OphCiExamination_VisualAcuity_Reading::LEFT,
        ),
        'right_readings' => array(
            'side' => OphCiExamination_VisualAcuity_Reading::RIGHT,
        ),
    );

    public $cvi_alert_dismissed;

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
        return 'et_ophciexamination_visualacuity';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array(' right_readings, left_readings, eye_id, unit_id, left_unable_to_assess,right_unable_to_assess,
             left_eye_missing, right_eye_missing, cvi_alert_dismissed, left_notes, right_notes', 'safe'),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, event_id, , eye_id', 'safe', 'on' => 'search'),
        );
    }

    public function sidedFields()
    {
        return array('unable_to_assess', 'eye_missing', 'readings');
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
            'eye' => array(self::BELONGS_TO, 'Eye', 'eye_id'),
            'user' => array(self::BELONGS_TO, 'User', 'created_user_id'),
            'usermodified' => array(self::BELONGS_TO, 'User', 'last_modified_user_id'),
            'unit' => array(self::BELONGS_TO, 'OEModule\OphCiExamination\models\OphCiExamination_VisualAcuityUnit', 'unit_id', 'on' => 'unit.is_near = 0'),
            'readings' => array(self::HAS_MANY, 'OEModule\OphCiExamination\models\OphCiExamination_VisualAcuity_Reading', 'element_id'),
            'right_readings' => array(self::HAS_MANY, 'OEModule\OphCiExamination\models\OphCiExamination_VisualAcuity_Reading', 'element_id', 'on' => 'right_readings.side = '.OphCiExamination_VisualAcuity_Reading::RIGHT),
            'left_readings' => array(self::HAS_MANY, 'OEModule\OphCiExamination\models\OphCiExamination_VisualAcuity_Reading', 'element_id', 'on' => 'left_readings.side = '.OphCiExamination_VisualAcuity_Reading::LEFT),
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
            'left_unable_to_assess' => 'Unable to assess',
            'right_unable_to_assess' => 'Unable to assess',
            'left_eye_missing' => 'Eye missing',
            'right_eye_missing' => 'Eye missing',
            'cvi_alert_dismissed' => 'Is CVI alert dismissed',
            'right_notes' => 'Comments',
            'left_notes' => 'Comments'
        );
    }

    /**
     * Perform dependent validation for readings and flags.
     */
    protected function afterValidate()
    {
        $model = str_replace('\\', '_', $this->elementType->class_name);

        if (array_key_exists($model, $_POST)) {
                    $va = $_POST[$model];
            foreach (array('left', 'right') as $side) {
                if (!$this->eyeHasSide($side, $va['eye_id'])) {
                    continue;
                }
                $isAssessable = !($va[$side . '_unable_to_assess'] || $va[$side . '_eye_missing']);
                $hasReadings = array_key_exists($side . '_readings', $va);

                if (($isAssessable && $hasReadings) || (!$isAssessable && !$hasReadings)) {
                    if ($hasReadings) {
                        // pick out the method_id's from the submitted readings and tally them up
                        $method_ids = array_column($va[$side . '_readings'], 'method_id');

                        // change values to keys. dupicates keys are dropped as keys must be unique
                        if (count($method_ids) !== count(array_flip($method_ids))) {
                            $this->addError($side, 'Each method type can only be added once per eye');
                        }
                    } else {
                        continue;
                    }
                } elseif ($isAssessable && !$hasReadings) {
                    $this->addError($side, ucfirst($side) . ' side has no data.');
                } else {
                    if ($va[$side . '_unable_to_assess']) {
                        $this->addError($side . '_unable_to_assess', 'Cannot be ' . $this->getAttributeLabel($side . '_unable_to_assess') . ' with VA readings.');
                    }
                    if ($va[$side . '_eye_missing']) {
                        $this->addError($side . '_eye_missing', 'Cannot be ' . $this->getAttributeLabel($side . '_eye_missing') . ' with VA readings.');
                    }
                }
            }
        } else {
            \OELog::log("Visual acuity element not found in POST data. Assuming submission without view and skipping POST validation.");
        }

        parent::afterValidate();
    }

    public function afterSave()
    {
        foreach (array('left', 'right') as $eye_side) {
            if ($this->{$eye_side .'_unable_to_assess'} || $this->{$eye_side .'_eye_missing'}) {
                foreach ($this->{$eye_side .'_readings'} as $reading) {
                    $reading->delete();
                }
            }
        }
        parent::afterSave();
    }

    public function setDefaultOptions(\Patient $patient = null)
    {
        $this->unit_id = $this->getSetting('unit_id');
        if ($rows = $this->getSetting('default_rows')) {
            $left_readings = array();
            $right_readings = array();
            for ($i = 0; $i < $rows; ++$i) {
                $left_readings[] = new OphCiExamination_VisualAcuity_Reading();
                $right_readings[] = new OphCiExamination_VisualAcuity_Reading();
            }
            $this->left_readings = $left_readings;
            $this->right_readings = $right_readings;
        }
    }

    /**
     * Array of unit values for dropdown.
     *
     * @param int  $unit_id
     * @param bool $selectable - whether want selectable values or all unit values
     *
     * @return array
     */
    public function getUnitValues($unit_id = null, $selectable = true)
    {
        if ($unit_id) {
            $unit = OphCiExamination_VisualAcuityUnit::model()->findByPk($unit_id);
        } else {
            $unit = $this->unit;
        }
        if ($selectable) {
            return \CHtml::listData($unit->selectableValues, 'base_value', 'value');
        } else {
            return \CHtml::listData($unit->values, 'base_value', 'value');
        }
    }

    /**
     * @param $excluded_unit_id
     * @param bool $is_near
     * @return \CActiveRecord[]
     */

    public function getUnits($excluded_unit_id, $is_near)
    {
        $criteria = new \CDbCriteria();
        $criteria->condition = 'id <> :unit_id AND active = 1 AND is_near = :is_near';
        $criteria->params = array(':unit_id' => $excluded_unit_id, 'is_near' => $is_near);
        $criteria->order = 'name';
        return OphCiExamination_VisualAcuityUnit::model()->findAll($criteria);
    }

    /**
     * @param int|null $unit_id
     * @param bool     $is_near
     *
     * @return array
     */
    public function getUnitValuesForForm($unit_id = null, $is_near = false)
    {
        if ($unit_id) {
            $unit = OphCiExamination_VisualAcuityUnit::model()->findByPk($unit_id);
        } else {
            $unit = $this->unit;
        }

        $unit_values = $unit->selectableValues;

        $tooltip_units = $this->getUnits($unit->id, $is_near);

        $options = array();


        // getting the conversion values
        foreach ($unit_values as $uv) {
            $idx = (string) $uv->base_value;
            $options[$idx] = array('data-tooltip' => array());
            foreach ($tooltip_units as $tt) {
                $last = null;
                foreach ($tt->values as $tt_val) {
                    if ($tt_val->base_value >= $uv->base_value) {
                        $val = $tt_val->value;

                        if ($last != null && (abs($uv->base_value - $tt_val->base_value) > abs($uv->base_value - $last->base_value))) {
                            $val = $last->value;
                        }
                        $map = array('name' => $tt->name, 'value' => $val, 'approx' => false);
                        if ($tt_val->base_value < $uv->base_value) {
                            $map['approx'] = true;
                        }
                        $options[$idx]['data-tooltip'][] = $map;
                        break;
                    }

                    $last = $tt_val;
                }
            }
            // need to JSONify the options data
            $options[$idx]['data-tooltip'] = \CJSON::encode($options[$idx]['data-tooltip']);
        }

        return array(\CHtml::listData($unit_values, 'base_value', 'value'), $options);
    }

    /**
     * Get a combined string of the different readings. If a unit_id is given, the readings will
     * be converted to unit type of that id.
     *
     * @param string $side
     * @param null   $unit_id
     *
     * @return string
     */
    public function getCombined($side, $unit_id = null)
    {
        $combined = array();
        foreach ($this->getNamedReadings($side, $unit_id) as $method => $value) {
            $combined[] = $value.' '.$method;
        }

        return implode(', ', $combined);
    }

    public function getNamedReadings($side, $unit_id = null){
        $readings = array();
        foreach ($this->{$side.'_readings'} as $reading) {
            $readings[$reading->method->name] = $reading->convertTo($reading->value, $unit_id);
        }
        return $readings;
    }

    /**
     * Get the best reading for the given side.
     *
     * @param string $side
     *
     * @return OphCiExamination_VisualAcuity_Reading|null
     */
    public function getBestReading($side)
    {
        $best = null;
        foreach ($this->{$side.'_readings'} as $reading) {
            if (!$best || $reading->value >= $best->value) {
                $best = $reading;
            }
        }

        return $best;
    }

    /**
     * Get the best reading based on the type
     *
     * @param string $side
     * @param $method
     */
    public function getBestReadingByMethods($side, $methods)
    {
        $best = null;
        foreach ($methods as $method) {
            foreach ($this->{$side.'_readings'} as $reading) {
                if ($reading->method->id == $method->id) {
                    if (!$best || $reading->value >= $best->value) {
                        $best = $reading;
                    }
                }
            }
        }
        if ($best) {
            return $best->convertTo($best->value);
        }
        return $best;
    }

    /**
     * Get the best reading for the given side.
     *
     * @param string $side
     *
     * @return OphCiExamination_VisualAcuity_Reading|null
     */
    public function getAllReadings($side)
    {
        $r = array();
        foreach ($this->{$side.'_readings'} as $reading) {
            $r[] = $reading;
        }

        return $r;
    }

    /**
     * Get the best reading for the specified side in current units.
     *
     * @param string $side
     *
     * @return string
     */
    public function getBest($side)
    {
        $best = $this->getBestReading($side);
        if ($best) {
            return $best->convertTo($best->value);
        }
    }

    /**
     * Convenience function for generating string of why a reading wasn't recorded for a side.
     *
     * @param $side
     *
     * @return string
     */
    public function getTextForSide($side)
    {
        if ($this->hasEye($side) && !$this->{$side.'_readings'}) {
            if ($this->{$side.'_unable_to_assess'}) {
                $text = $this->getAttributeLabel($side.'_unable_to_assess');
                if ($this->{$side.'_eye_missing'}) {
                    $text .= ', '.$this->getAttributeLabel($side.'_eye_missing');
                }

                return $text;
            }

            if ($this->{$side.'_eye_missing'}) {
                return $this->getAttributeLabel($side.'_eye_missing');
            }

            return 'not recorded';
        }
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     *
     * @return \CActiveDataProvider
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

    /**
     * returns the default letter string for the va readings. Converts all readings to standard unit
     * configured for VA.
     *
     * @return string
     */
    public function getLetter_string()
    {
        $va_unit = OphCiExamination_VisualAcuityUnit::model()->findByPk($this->getSetting('unit_id'));

        return Yii::app()->controller->renderPartial(
            'application.modules.OphCiExamination.views.default.letter.va',
            array(
                'left' => $this->getNamedReadings('left', $va_unit->id),
                'right' => $this->getNamedReadings('right', $va_unit->id),
                'va_unit' => $va_unit->name
            ),
            true
        );
    }

    /**
     * Get the list of currently used method ids.
     */
    public function getMethodValues()
    {
        $method_values = array();

        foreach ($this->readings as $reading) {
            $method_values[] = $reading->method_id;
        }

        return $method_values;
    }

    public function canViewPrevious()
    {
        return true;
    }

    public function canCopy()
    {
        return true;
    }

    public function getViewTitle()
    {
        return $this->getElementTypeName() . ' <small>' . $this->unit->name . '</small>';
    }

    /***
     * @param $eye_side 'left'|'right' which side
     * @return bool whether the eye is marked as assessable (not missing or otherwise unassessable)
     */
    public function eyeAssesable($eye_side){
        return !($this->{$eye_side.'_unable_to_assess'} || $this->{$eye_side.'_eye_missing'});
    }

    public function getPrint_view()
    {
        return 'print_'.$this->getDefaultView();
    }
    public function getReading($side)
    {
        if (!$values = $this->{"{$side}_readings"}) {
            return;
        }
        $sum = 0;
        foreach ($values as $value) {
            if ($value->value) {
                $sum += $value->value;
            }
        }
        return round($sum / count($values));
    }
}
