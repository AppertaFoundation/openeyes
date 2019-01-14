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
 * The followings are the available columns in table 'et_ophciexamination_comorbidities':.
 *
 * @property int $id
 * @property int $event_id
 *
 * The followings are the available model relations:
 * @property Event $event
 * @property OphCiExamination_Comorbidities_Item[] $items
 */
class Element_OphCiExamination_Comorbidities extends \BaseEventTypeElement
{
    protected $auto_update_relations = true;

    /**
     * Returns the static model of the specified AR class.
     *
     * @return Element_OphCiExamination_Comorbidities the static model class
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
        return 'et_ophciexamination_comorbidities';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
                array('comments, items', 'safe'),
                // The following rule is used by search().
                // Please remove those attributes that should not be searched.
                array('id, event_id', 'safe', 'on' => 'search'),
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
                'eventType' => array(self::BELONGS_TO, 'EventType', 'event_type_id'),
                'event' => array(self::BELONGS_TO, 'Event', 'event_id'),
                'user' => array(self::BELONGS_TO, 'User', 'created_user_id'),
                'usermodified' => array(self::BELONGS_TO, 'User', 'last_modified_user_id'),
                'items' => array(self::MANY_MANY, 'OEModule\OphCiExamination\models\OphCiExamination_Comorbidities_Item', 'ophciexamination_comorbidities_assignment(element_id, item_id)', 'order' => 'display_order, name'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
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

        return new \CActiveDataProvider(get_class($this), array(
                'criteria' => $criteria,
        ));
    }

    public function getComorbidityItemValues()
    {
        $item_values = array();

        if ($this->id) {
            foreach (OphCiExamination_Comorbidities_Assignment::model()->findAll('element_id=?', array($this->id)) as $ca) {
                $item_values[] = $ca->item_id;
            }
        }

        return $item_values;
    }

    public function getItemIds()
    {
        return \CHtml::listData($this->items, 'id', 'id');
    }

    public function getSummary()
    {
        $return = array();
        foreach ($this->items as $item) {
            $return[] = $item->name;
        }
        if ($return) {
            return implode(', ', $return);
        } else {
            return 'None';
        }
    }

    public function canCopy()
    {
        return true;
    }

    public function loadFromExisting($element)
    {
        parent::loadFromExisting($element);

        $this->items = $element->items;
    }

    public function getDisplayOrder($action)
    {
        return $action == 'view' ? 45 : parent::getDisplayOrder($action);
    }
}
