<?php
/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2019
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2019, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */


namespace OEModule\OphCiExamination\models;

/**
 * Class FamilyHistory_Entry
 * @package OEModule\OphCiExamination\models
 *
 * @property int $id
 * @property int $element_id
 * @property int $relative_id
 * @property string $other_relative
 * @property int $side_id
 * @property int $condition_id
 * @property string $other_condition
 * @property string $comments
 *
 * @property FamilyHistoryRelative $relative
 * @property FamilyHistorySide $side
 * @property FamilyHistoryCondition $condition
 * @property FamilyHistory $element
 */
class FamilyHistory_Entry extends \BaseEventTypeElement
{
    /**
     * Returns the static model of the specified AR class.
     *
     * @return FamilyHistory_Entry the static model class
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
        return 'ophciexamination_familyhistory_entry';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('element_id, relative_id, other_relative, side_id, condition_id, other_condition, comments', 'safe'),
            array('relative_id, side_id, condition_id', 'required'),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, element_id, relative_id, other_relative, side_id, condition_id, other_condition, comments', 'safe', 'on' => 'search'),
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
            'element' => array(self::BELONGS_TO, 'OEModule\OphCiExamination\models\Element_OphCiExamination_PastSurgery', 'element_id'),
            'relative' => array(self::BELONGS_TO, 'OEModule\OphCiExamination\models\FamilyHistoryRelative', 'relative_id'),
            'side' => array(self::BELONGS_TO, 'OEModule\OphCiExamination\models\FamilyHistorySide', 'side_id'),
            'condition' => array(self::BELONGS_TO, 'OEModule\OphCiExamination\models\FamilyHistoryCondition', 'condition_id')
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'relative_id' => 'Relative',
            'side_id' => 'Side',
            'condition_id' => 'Condition'
        );
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     *
     * @return \CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
     */
    public function search()
    {
        // Warning: Please modify the following code to remove attributes that
        // should not be searched.

        $criteria = new \CDbCriteria();

        $criteria->compare('id', $this->id, true);
        $criteria->compare('relative_id', $this->relative_id, true);
        $criteria->compare('other_relative', $this->other_relative, true);
        $criteria->compare('side_id', $this->side_id, true);
        $criteria->compare('condition_id', $this->condition_id, true);
        $criteria->compare('other_condition', $this->other_condition, true);
        $criteria->compare('comments', $this->comments, true);

        return new \CActiveDataProvider(get_class($this), array(
            'criteria' => $criteria,
        ));
    }

    /**
     * @return bool
     * @inheritdoc
     */
    public function beforeSave()
    {
        $relative = FamilyHistoryRelative::model()->findByPk($this->relative_id);
        if ($relative && !$relative->is_other) {
            $this->other_relative = null;
        }

        $condition = FamilyHistoryCondition::model()->findByPk($this->condition_id);
        if ($condition && !$condition->is_other) {
            $this->other_condition = null;
        }

        return parent::beforeSave();
    }

    /**
     * @return string
     */
    public function getDisplayRelative()
    {
        if ($this->other_relative) {
            return $this->other_relative;
        }
        return $this->relative ? $this->relative->name : '';
    }

    /**
     * @return string
     */
    public function getDisplayCondition()
    {
        if ($this->other_condition) {
            return $this->other_condition;
        }
        return $this->condition ? $this->condition->name : '';
    }

    /**
     * @return string
     */
    public function __toString()
    {
        $side = '';
        if ($this->side && substr($this->side->name, -3) == 'nal' ) {
            $side = $this->side->name . ' ';
        }
        $res = $side . $this->getDisplayRelative() . ': ' . $this->getDisplayCondition();

        if ($this->comments) {
            $res .= ' (' . $this->comments . ')';
        }
        return $res;
    }
}
