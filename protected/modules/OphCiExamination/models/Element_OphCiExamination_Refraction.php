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

/**
 * This is the model class for table "et_ophciexamination_refraction".
 *
 * The followings are the available columns in table:
 *
 * @property string $id
 * @property int $event_id
 * @property int $eye_id
 * @property decimal $left_sphere
 * @property decimal $left_cylinder
 * @property int $left_axis
 * @property string $left_axis_eyedraw
 * @property string $left_type_id
 * @property string $left_type_other
 * @property decimal $right_sphere
 * @property decimal $right_cylinder
 * @property int $right_axis
 * @property string $right_axis_eyedraw
 * @property string $right_type_id
 * @property string $right_type_other
 */
class Element_OphCiExamination_Refraction extends \SplitEventTypeElement
{
    public $service;

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
        return 'et_ophciexamination_refraction';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        return array(
            array('left_sphere, left_cylinder, left_axis, left_axis_eyedraw, left_type_id, left_type_other, right_sphere, right_cylinder, right_axis, right_axis_eyedraw, right_type_id, right_type_other, eye_id, left_notes, right_notes', 'safe'),
            array('left_sphere', 'requiredIfSide', 'side' => 'left'),
            array('left_sphere', 'numerical'),
            array('left_cylinder', 'requiredIfSide', 'side' => 'left'),
            array('left_cylinder', 'numerical'),
            array('left_axis', 'requiredIfSide', 'side' => 'left'),
            array('left_axis', 'numerical','min' => -180, 'max'=>180, 'integerOnly' => true),
            array('left_type_other', 'requiredIfRefractionTypeOther', 'side' => 'left'),
            array('right_sphere', 'requiredIfSide', 'side' => 'right'),
            array('right_sphere', 'numerical'),
            array('right_cylinder', 'requiredIfSide', 'side' => 'right'),
            array('right_cylinder', 'numerical'),
            array('right_axis', 'requiredIfSide', 'side' => 'right'),
            array('right_axis', 'numerical', 'min' => -180, 'max'=>180, 'integerOnly' => true),
            array('right_type_other', 'requiredIfRefractionTypeOther', 'side' => 'right'),
            array('id, event_id, left_sphere, left_cylinder, left_axis, left_axis_eyedraw, left_type_id, right_sphere, right_cylinder, right_axis, right_axis_eyedraw, right_type_id, eye_id', 'safe', 'on' => 'search'),
        );
    }

    public function requiredIfRefractionTypeOther($attribute, $params)
    {
        if (($params['side'] === 'left' && $this->left_type_id === '' && $this->hasLeft()) || ($params['side'] === 'right' && $this->right_type_id === '' && $this->hasRight())) {
            if (empty($this->{$params['side'].'_type_other'})) {
                $this->addError($attribute, ucfirst($params['side']).' Other cannot be blank. Please specify a valid refraction.');
            }
        }
    }

    public function sidedFields()
    {
        return array('sphere', 'cylinder', 'axis', 'axis_eyedraw', 'type_id', 'type_other');
    }

    public function sidedDefaults()
    {
        return array('axis' => 0, 'type_id' => 1);
    }

    public function canCopy()
    {
        return true;
    }

    public function setDefaultOptions(\Patient $patient = null)
    {
        $this->left_axis = 0;
        $this->right_axis = 0;
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
            'left_type' => array(self::BELONGS_TO, 'OEModule\OphCiExamination\models\OphCiExamination_Refraction_Type', 'left_type_id'),
            'right_type' => array(self::BELONGS_TO, 'OEModule\OphCiExamination\models\OphCiExamination_Refraction_Type', 'right_type_id'),
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
            'left_sphere' => 'Sphere',
            'left_cylinder' => 'Cylinder',
            'left_axis' => 'Axis',
            'left_type_id' => 'Type',
            'left_type_other' => 'Other Type',
            'left_notes' => 'Comments',
            'right_sphere' => 'Sphere',
            'right_cylinder' => 'Cylinder',
            'right_axis' => 'Axis',
            'right_type_id' => 'Type',
            'right_type_other' => 'Other Type',
            'right_notes' => 'Comments'
        );
    }

    public function getCombined($side)
    {
        return ($this->{$side . '_sphere'} > 0 ? '+' : '') .
            $this->{$side . '_sphere'} . '/' .
            ($this->{$side . '_cylinder'} > 0 ? '+' : '') .
            $this->{$side . '_cylinder'} . ' X ' .
            $this->{$side . '_axis'} . '° ';
    }

    public function getSplit($side)
    {
        return array('sphere' => $this->{$side.'_sphere'}, 'cylinder' => $this->{$side.'_cylinder'}, 'axis' => $this->{$side.'_axis'}.'° ', 'type' => $this->getType($side));
    }

    public function getType($side)
    {
        if ($this->{$side.'_type_id'}) {
            return $this->{$side.'_type'}->name;
        } else {
            return $this->{$side.'_type_other'};
        }
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

        $criteria->compare('left_sphere', $this->left_sphere);
        $criteria->compare('left_cylinder', $this->left_cylinder);
        $criteria->compare('left_axis', $this->left_axis);
        $criteria->compare('left_type_id', $this->left_type_id);
        $criteria->compare('left_type_other', $this->left_type_other);
        $criteria->compare('right_sphere', $this->right_sphere);
        $criteria->compare('right_cylinder', $this->right_cylinder);
        $criteria->compare('right_axis', $this->right_axis);
        $criteria->compare('right_type_id', $this->right_type_id);
        $criteria->compare('right_type_other', $this->right_type_other);

        return new \CActiveDataProvider(get_class($this), array(
            'criteria' => $criteria,
        ));
    }

    public function getLetter_string()
    {
        return "Refraction:\nright: ".$this->getCombined('right')."\nleft: ".$this->getCombined('left')."\n";
    }
    
    public function getPrint_view()
    {
        return 'print_'.$this->getDefaultView();
    }
}
