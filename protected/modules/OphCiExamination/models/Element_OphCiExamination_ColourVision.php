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

use OEModule\OphCiExamination\widgets\ColourVision as ColourVisionWidget;
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
 * @property string $left_notes
 * @property string $right_notes
 * @property OphCiExamination_ColourVision_Reading $readings
 * @property OphCiExamination_ColourVision_Reading $left_readings
 * @property OphCiExamination_ColourVision_Reading $right_readings
 */
class Element_OphCiExamination_ColourVision extends \SplitEventTypeElement
{
    use traits\CustomOrdering;

    protected $auto_update_relations = true;
    protected $relation_defaults = array(
        'left_readings' => array(
            'eye_id' => \Eye::LEFT,
        ),
        'right_readings' => array(
            'eye_id' => \Eye::RIGHT,
        ),
    );

    protected $auto_validate_relations = false; // cannot auto validate with split relations
    protected $widgetClass = ColourVisionWidget::class;

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
                array('event_id, eye_id, left_notes, right_notes', 'safe'),
                array('id, event_id, eye_id, left_notes, right_notes', 'safe', 'on' => 'search'),
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
            'eventType' => array(self::BELONGS_TO, 'EventType', 'event_type_id'), // TODO: does this relation actually exist
            'event' => array(self::BELONGS_TO, \Event::class, 'event_id'),
            'user' => array(self::BELONGS_TO, \User::class, 'created_user_id'),
            'usermodified' => array(self::BELONGS_TO, \User::class, 'last_modified_user_id'),
            'eye' => array(self::BELONGS_TO, \Eye::class, 'eye_id'),
            'readings' => array(self::HAS_MANY, OphCiExamination_ColourVision_Reading::class, 'element_id'),
            'right_readings' => array(self::HAS_MANY, OphCiExamination_ColourVision_Reading::class, 'element_id', 'on' => 'right_readings.eye_id = '.\Eye::RIGHT),
            'left_readings' => array(self::HAS_MANY, OphCiExamination_ColourVision_Reading::class, 'element_id', 'on' => 'left_readings.eye_id = '.\Eye::LEFT),
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
            'left_notes' => 'Comments',
            'right_notes' => 'Comments',
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
        $criteria->compare('left_notes', $this->left_notes, true);
        $criteria->compare('right_notes', $this->right_notes, true);

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
     * @deprecated
     * @return OphCiExamination_ColourVision_Reading|null
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
                if (!is_array($this->{$side.'_readings'})) {
                    continue;
                }

                $method_ids = [];
                $methods_not_unique = false;
                foreach ($this->{$side.'_readings'} as $i => $reading) {
                    if ($method = $reading->method) {
                        $methods_not_unique = $methods_not_unique || in_array($method->id, $method_ids);
                        $method_ids[] = $method->id;
                    }
                    if (!$reading->validate()) {
                        foreach ($reading->getErrors() as $fld => $err) {
                            $this->addError($side.'_readings', ucfirst($side).' reading ('.($i + 1).'): '.implode(', ', $err));
                        }
                    }
                }
                if ($methods_not_unique) {
                    $this->addError($side.'_readings', ucfirst($side).' readings must only have unique reading methods');
                }
            }
        }
    }

    public function canViewPrevious()
    {
        return true;
    }
}
