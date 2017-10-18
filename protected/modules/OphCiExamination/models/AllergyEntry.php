<?php
/**
 * OpenEyes.
 *
 * (C) OpenEyes Foundation, 2017
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2017, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

namespace OEModule\OphCiExamination\models;


/**
 * Class FamilyHistory_Entry
 * @package OEModule\OphCiExamination\models
 *
 * @property int $id
 * @property int $element_id
 * @property int $allergy_id
 * @property string $other
 * @property string $comments
 *
 * @property Allergy $allergy
 * @property Allergies $element
 */
class AllergyEntry extends \BaseElement
{
    /**
     * Returns the static model of the specified AR class.
     *
     * @return PreviousOperation the static model class
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
        return 'ophciexamination_allergy_entry';
    }
    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('element_id, allergy_id, other, comments', 'safe'),
            array('allergy_id', 'required'),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, element_id, allergy_id, other, comments', 'safe', 'on' => 'search'),
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
            'element' => array(self::BELONGS_TO, 'OEModule\OphCiExamination\models\Element_OphCiExamination_Allergy', 'element_id'),
            'allergy' => array(self::BELONGS_TO, 'OEModule\OphCiExamination\models\OphCiExaminationAllergy', 'allergy_id'),
        );
    }
    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'allergy_id' => 'Allergy',
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
        $criteria->compare('allergy_id', $this->allergy_id, true);
        $criteria->compare('other', $this->other, true);
        $criteria->compare('comments', $this->comments, true);
        return new \CActiveDataProvider(get_class($this), array(
            'criteria' => $criteria,
        ));
    }

    protected function beforeSave()
    {
        if ($this->isModelDirty()) {
            $this->element->addAudit('edited-allergies');
        }
        return parent::beforeSave();
    }

    /**
     * @return string
     */
    public function getDisplayAllergy()
    {
        if ($this->other) {
            return $this->other;
        }
        return $this->allergy ? $this->allergy->name : '';
    }

    /**
     * @return string
     */
    public function __toString()
    {
        $res = $this->getDisplayAllergy();
        if ($this->comments) {
            $res .= ' (' . $this->comments . ')';
        }
        return $res;
    }
}