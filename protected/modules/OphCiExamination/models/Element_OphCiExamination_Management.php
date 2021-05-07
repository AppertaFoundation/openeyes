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
 * This is the model class for table "et_ophciexamination_management".
 *
 * The followings are the available columns in table:
 *
 * @property string $id
 * @property int $event_id
 *
 * The followings are the available model relations:
 */
class Element_OphCiExamination_Management extends \BaseEventTypeElement
{
    use traits\CustomOrdering;
    const ELEMENT_CHILDREN = [
        'Element_OphCiExamination_CataractSurgicalManagement',
        'Element_OphCiExamination_OverallManagementPlan',
        'Element_OphCiExamination_CurrentManagementPlan',
        'Element_OphCiExamination_LaserManagement',
        'Element_OphCiExamination_InjectionManagement'
    ];

    /**
     * Returns the static model of the specified AR class.
     *
     * @return Element_OphCiExamination_Management the static model class
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
        return 'et_ophciexamination_management';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('comments', 'safe'),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, event_id, comments', 'safe', 'on' => 'search'),
        );
    }

    public function eventScopeValidation($elements)
    {
        $element_names = array_map(
            function ($element) {
                return \Helper::getNSShortname($element);
            },
            $elements
        );

        if (empty(array_intersect($element_names, self::ELEMENT_CHILDREN)) && !$this->comments) {
            $this->addError('comments', 'Comments cannot be blank when there are no child elements');
        }
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
            'comments' => 'Comments',
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
        $criteria->compare('comments', $this->comments);

        return new \CActiveDataProvider(get_class($this), array(
            'criteria' => $criteria,
        ));
    }

    /**
     * retrieves a string summary of this element (intended for correspondence).
     *
     * @return string
     */
    public function getLetter_string()
    {
        $res = '';
        if ($this->comments) {
            $res .= 'Clinical management: ' . $this->comments . "\n";
        }

        foreach ($this->getSiblings() as $el) {
            if (method_exists($el, 'getLetter_string')) {
                $res .= $el->getLetter_string() . "\n";
            }
        }

        return $res;
    }

    /**
     * @param string $delimiter what to put between the names of siblings
     * @return string
     */
    public function getSiblingString($delimiter = ' // ')
    {
        // To handle the bug reported in OE-11241
        $siblingsWithoutMedManagement = [];
        foreach ($this->getSiblings() as $sibling) {
            if (get_class($sibling) !== MedicationManagement::class) {
                $siblingsWithoutMedManagement[] = $sibling;
            }
        }
        return implode(
            $delimiter,
            array_map(
                function ($el) {
                    return $el->elementType->name . ': ' . $el;
                },
                $siblingsWithoutMedManagement
            )
        );
    }

    public function canCopy()
    {
        return true;
    }
}
