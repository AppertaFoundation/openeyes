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

use OE\factories\models\traits\HasFactory;

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
class AllergyEntry extends \BaseActiveRecordVersioned
{
    use HasFactory;

    public static $PRESENT = 1;
    public static $NOT_PRESENT = 0;
    public static $NOT_CHECKED = -9;

    protected $auto_update_relations = true;
    protected $auto_validate_relations = true;

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
            array('element_id, allergy_id, reactions, other, comments, has_allergy', 'safe'),
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
        return [
            'element' => [self::BELONGS_TO, Allergies::class, 'element_id'],
            'allergy' => [self::BELONGS_TO, OphCiExaminationAllergy::class, 'allergy_id'],
            'reactions' => [self::MANY_MANY, OphCiExaminationAllergyReaction::class, 'ophciexamination_allergy_reaction_assignment(allergy_entry_id, reaction_id)']
        ];
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

    public function validateOtherAllergies($attribute)
    {
        if ($this->allergy->name == "Other" && $this->$attribute == "" ) {
            $this->addError($attribute, 'Allergy cannot be blank');
        }
    }
}
