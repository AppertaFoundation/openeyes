<?php
/**
 * OpenEyes
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2008-2011, Moorfields Eye Hospital NHS Foundation Trust
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */

namespace OEModule\OphCiExamination\models;

/**
 * This is the model class for table "et_ophciexamination_opticdisc".
 *
 * The followings are the available columns in table 'et_ophciexamination_opticdisc':
 * @property integer $id
 * @property integer $eye_id
 * @property integer $event_id
 * @property string $left_description
 * @property string $right_description
 * @property float $left_diameter
 * @property float $right_diameter
 * @property string $left_eyedraw
 * @property string $right_eyedraw
 *
 * The followings are the available model relations:
 * @property Event $event
 * @property OphCiExamination_OpticDisc_Lens $left_lens
 * @property OphCiExamination_OpticDisc_Lens $right_lens
 * @property OphCiExamination_OpticDisc_CDRatio $left_cd_ratio
 * @property OphCiExamination_OpticDisc_CDRatio $right_cd_ratio
 */
class Element_OphCiExamination_OpticDisc extends \SplitEventTypeElement
{
    /**
     * Returns the static model of the specified AR class.
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
        return 'et_ophciexamination_opticdisc';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        return array(
                array('eye_id, left_description, right_description, left_eyedraw, right_eyedraw, left_cd_ratio_id, right_cd_ratio_id', 'safe'),
                array('left_diameter, right_diameter', 'type', 'type' => 'float'),
                array('left_diameter, right_diameter', 'numerical', 'max' => 9.9, 'min' => 0.1),
                array('left_lens_id, right_lens_id', 'checkDiameter'),
                array('eye_id, event_id, left_description, right_description, left_eyedraw, right_eyedraw, left_diameter, right_diameter, left_cd_ratio_id, right_cd_ratio_id, left_lens_id, right_lens_id', 'safe', 'on' => 'search'),
                array('left_description', 'requiredIfSide', 'side' => 'left'),
                array('right_description', 'requiredIfSide', 'side' => 'right'),
        );
    }

    /**
     * Vertical diameter requires that lens is also specified
     * @param string $attribute
     */
    public function checkDiameter($attribute)
    {
        $side = explode('_', $attribute, 2);
        $side = $side[0];
        if ($this->{$side.'_diameter'} && !$this->$attribute) {
            $this->addError($attribute, ucfirst($side).' vertical diameter requires lens');
        }
    }

    public function sidedFields()
    {
        return array('diameter', 'description', 'eyedraw', 'cd_ratio_id', 'lens_id');
    }

    public function canCopy()
    {
        return true;
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
                'left_cd_ratio' => array(self::BELONGS_TO, 'OEModule\OphCiExamination\models\OphCiExamination_OpticDisc_CDRatio', 'left_cd_ratio_id'),
                'right_cd_ratio' => array(self::BELONGS_TO, 'OEModule\OphCiExamination\models\OphCiExamination_OpticDisc_CDRatio', 'right_cd_ratio_id'),
                'left_lens' => array(self::BELONGS_TO, 'OEModule\OphCiExamination\models\OphCiExamination_OpticDisc_Lens', 'left_lens_id'),
                'right_lens' => array(self::BELONGS_TO, 'OEModule\OphCiExamination\models\OphCiExamination_OpticDisc_Lens', 'right_lens_id'),
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
                'left_diameter' => 'Vertical Diameter',
                'right_diameter' => 'Vertical Diameter',
                'left_description' => 'Description',
                'right_description' => 'Description',
                'left_eyedraw' => 'EyeDraw',
                'right_eyedraw' => 'EyeDraw',
                'left_cd_ratio_id' => 'C/D Ratio',
                'right_cd_ratio_id' => 'C/D Ratio',
                'left_lens_id' => 'Lens',
                'right_lens_id' => 'Lens',
        );
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
     */
    public function search()
    {
        $criteria = new \CDbCriteria;

        $criteria->compare('id', $this->id, true);
        $criteria->compare('event_id', $this->event_id, true);
        $criteria->compare('left_diameter', $this->left_diameter, true);
        $criteria->compare('right_diameter', $this->right_diameter, true);
        $criteria->compare('left_description', $this->left_description, true);
        $criteria->compare('right_description', $this->right_description, true);
        $criteria->compare('left_eyedraw', $this->left_eyedraw, true);
        $criteria->compare('right_eyedraw', $this->right_eyedraw, true);

        return new \CActiveDataProvider(get_class($this), array(
                'criteria' => $criteria,
        ));
    }

    public function getDiameterOptions()
    {
        $range = range(0, 4, 0.1);
        foreach ($range as $key => $value) {
            $range[$key] = sprintf('%01.1f', $value);
        }
        return array_combine($range, $range);
    }

    public function sidedDefaults()
    {
        return array(
            'cd_ratio_id' => OphCiExamination_OpticDisc_CDRatio::model()->findByAttributes(array('name' => '0.3'))->id,
        );
    }
}
