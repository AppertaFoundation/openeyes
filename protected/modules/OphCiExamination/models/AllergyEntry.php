<?php
/**
 * OpenEyes.
 *
 * (C) OpenEyes Foundation, 2019
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
 * @property int $has_allergy
 *
 * @property OphCiExaminationAllergy $allergy
 * @property Allergies $element
 * @property array $reactions
 */
class AllergyEntry extends \BaseElement
{
    public static $PRESENT = 1;
    public static $NOT_PRESENT = 0;
    public static $NOT_CHECKED = -9;
    public static $OTHER_VAL = 17;

    public $reaction_ids = array();

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
            array('element_id, allergy_id, reaction_id, other, comments, has_allergy', 'safe'),
            array('allergy_id', 'required'),
            array('other', 'validateOtherAllergies'),
            array('has_allergy', 'required', 'message'=>'Status cannot be blank'),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, element_id, allergy_id, reaction_id, other, comments, has_allergy', 'safe', 'on' => 'search'),
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
            'element' => array(self::BELONGS_TO, 'OEModule\OphCiExamination\models\Allergies', 'element_id'),
            'allergy' => array(self::BELONGS_TO, 'OEModule\OphCiExamination\models\OphCiExaminationAllergy', 'allergy_id'),
            'reactions' => array(self::MANY_MANY, 'OphCiExaminationAllergyReaction', 'ophciexamination_allergy_reaction_assignment(allergy_entry_id, reaction_id)'),
            'reaction_assignments' => array(self::HAS_MANY, 'AllergyEntryReactionAssignment', 'allergy_entry_id'),
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
        $criteria->compare('has_allergy', $this->has_allergy, true);
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

    protected function afterSave()
    {
        $this->refreshReactionAssignments();
        return parent::afterSave();
    }

    /**
     * @return string
     */
    public function getDisplayHasAllergy()
    {
        if ($this->has_allergy === (string) static::$PRESENT) {
            return 'Present';
        } elseif ($this->has_allergy === (string) static::$NOT_PRESENT) {
            return 'Not present';
        }
        return 'Not checked';
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
    public function getReactionString()
    {
        $reactions = array();
        foreach ($this->reactions as $reaction) {
            $reactions[] = $reaction->name;
        }

        return implode(', ', $reactions);
    }


    /**
     * @return string
     */
    public function __toString()
    {
        $res = '<strong>' . $this->getDisplayHasAllergy() . ':</strong> ' . $this->getDisplayAllergy();
        if ($this->comments) {
            $res .= ' (' . $this->comments . ')';
        }
        return $res;
    }

    /***
     * Checks if this allergy entry is 'Other' so it can be edited
     *
     * @throws Exception if record is new as it does not have a allergy_id to check against
     * @return bool true if entry is 'Other' (ie not in the standard list so needs editable field)
     */
    public function isOther()
    {
        if (!$this->isNewRecord) {
            return $this->allergy_id == AllergyEntry::$OTHER_VAL;
        } else {
            throw new Exception('Cannot check if new allergy entry is other without proposed allergy id,
            new records do not have allergy_id set, please use staticIsOther($allergy_id)');
        }
    }

    public function validateOtherAllergies($attribute)
    {
        if ($this->allergy_id == AllergyEntry::$OTHER_VAL && $this->$attribute == "" ) {
            $this->addError($attribute, 'Allergy cannot be blank');
        }
    }

    /***
     * Assigns an allergy reaction to this entry
     */
    public function assignReaction($reaction_id)
    {
        $assignment = new \AllergyEntryReactionAssignment();

        $assignment->allergy_entry_id = $this->id;
        $assignment->reaction_id = $reaction_id;

        $assignment->save();
    }

    /***
     * Adds allergy reactions that don't already have an assignment, and removes reactions that shouldn't have an assignment
     */
    public function refreshReactionAssignments()
    {
        foreach ($this->reaction_assignments as $assignment) {
            if (!in_array($assignment->reaction_id, $this->reaction_ids)) {
                $assignment->delete();
            }
        }
        foreach ($this->reaction_ids as $reaction_id) {
            if (!\AllergyEntryReactionAssignment::model()->exists("allergy_entry_id = :allergy_entry_id AND reaction_id = :reaction_id", array(':allergy_entry_id' => $this->id, ':reaction_id'=> $reaction_id))) {
                $this->assignReaction($reaction_id);
            }
        }
    }
}
