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
 * This is the model class for table "ophciexamination_element_set".
 *
 * @property int $id
 * @property string $name
 * @property OphCiExamination_Workflow $workflow
 * @property int $position
 * @property OphCiExamination_ElementSetItem[] $items
 */
class OphCiExamination_ElementSet extends \BaseActiveRecordVersioned
{
    /**
     * Returns the static model of the specified AR class.
     *
     * @return OphCiExamination_ElementSet the static model class
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
        return 'ophciexamination_element_set';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        return array(
                array('name', 'required'),
                array('id, name', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        return array(
                'workflow' => array(self::BELONGS_TO, 'OEModule\OphCiExamination\models\OphCiExamination_Workflow', 'workflow_id'),
                'items' => array(self::HAS_MANY, 'OEModule\OphCiExamination\models\OphCiExamination_ElementSetItem', 'set_id',
                        'with' => 'element_type',
                        'order' => 'element_type.name',
                ),
        );
    }

    public function getNextStep()
    {
        $criteria = new \CDbCriteria(array(
            'condition' => 'workflow_id = :workflow_id AND position >= :position AND id <> :id',
            'order' => 'position, id',
            'params' => array(':position' => $this->position, ':workflow_id' => $this->workflow_id, ':id' => $this->id),
        ));

        return $this->find($criteria);
    }

    /**
     * Get an array of ElementTypes corresponding with the items in this set.
     *
     * @return ElementType[]
     */
    public function getDefaultElementTypes()
    {
        $default_element_types = \ElementType::model()->findAll(array(
                'condition' => 'ophciexamination_element_set_item.set_id = :set_id AND ophciexamination_element_set_item.is_hidden = 0',
                'join' => 'JOIN ophciexamination_element_set_item ON ophciexamination_element_set_item.element_type_id = t.id',
                'order' => 'display_order',
                'params' => array(':set_id' => $this->id),
        ));

        return $default_element_types;
    }

    /**
     * Get an array of ElementTypes corresponding with the items NOT in this set.
     *
     * @return ElementType[]
     */
    public function getOptionalElementTypes()
    {
        $optional_element_types = \ElementType::model()->findAll(array(
                'condition' => "event_type.class_name = 'OphCiExamination' AND
					ophciexamination_element_set_item.id IS NULL
					OR ophciexamination_element_set_item.is_hidden = 0",
                'join' => 'JOIN event_type ON event_type.id = t.event_type_id
					LEFT JOIN ophciexamination_element_set_item ON (ophciexamination_element_set_item.element_type_id = t.id
					AND ophciexamination_element_set_item.set_id = :set_id)',
                'order' => 'display_order',
                'params' => array(':set_id' => $this->id),
        ));

        return $optional_element_types;
    }

    public function getHiddenElementTypes()
    {
        $hiddenElementTypes = \ElementType::model()->findAll(array(
            'condition' => "event_type.class_name = 'OphCiExamination' AND
					 ophciexamination_element_set_item.is_hidden = 1",
            'join' => 'JOIN event_type ON event_type.id = t.event_type_id
					LEFT JOIN ophciexamination_element_set_item ON (ophciexamination_element_set_item.element_type_id = t.id
					AND ophciexamination_element_set_item.set_id = :set_id)',
            'params' => array(':set_id' => $this->id),
        ));

        return $hiddenElementTypes;
    }

    public function getMandatoryElementTypes()
    {
        $mandatoryElementTypes = \ElementType::model()->findAll(array(
            'condition' => "event_type.class_name = 'OphCiExamination' AND
					 ophciexamination_element_set_item.is_mandatory = 1",
            'join' => 'JOIN event_type ON event_type.id = t.event_type_id
					LEFT JOIN ophciexamination_element_set_item ON (ophciexamination_element_set_item.element_type_id = t.id
					AND ophciexamination_element_set_item.set_id = :set_id)',
            'params' => array(':set_id' => $this->id),
        ));

        return $mandatoryElementTypes;
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
                'id' => 'ID',
                'name' => 'Name',
        );
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     *
     * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
     */
    public function search()
    {
        $criteria = new \CDbCriteria();
        $criteria->compare('id', $this->id, true);
        $criteria->compare('name', $this->name, true);

        return new \CActiveDataProvider(get_class($this), array(
                'criteria' => $criteria,
        ));
    }
}
