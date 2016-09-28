<?php
/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2016
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2016, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */

namespace OEModule\OphCoCvi\models;

/**
 * This is the model class for table "ophcocvi_clericinfo_employment_status".
 *
 * The followings are the available columns in table:
 * @property string $id
 * @property string $name
 * @property boolean $child_default
 * @property integer social_history_occupation_id
 *
 * The followings are the available model relations:
 *
 * @property \ElementType $element_type
 * @property \EventType $eventType
 * @property \Event $event
 * @property \User $user
 * @property \User $usermodified
 * @property \SocialHistoryOccupation $social_history_occupation
 */

class OphCoCvi_ClericalInfo_EmploymentStatus extends \BaseActiveRecordVersioned
{
    /**
     * Returns the static model of the specified AR class.
     * @param null|string $className
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
        return 'ophcocvi_clericinfo_employment_status';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        return array(
            array('name, child_default, social_history_occupation_id', 'safe'),
            array('name', 'length', 'max' => 128),
            array('name', 'required'),
            array('id, name, child_default, social_history_occupation_id', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        return array(
            'element_type' => array(
                self::HAS_ONE,
                'ElementType',
                'id',
                'on' => "element_type.class_name='" . get_class($this) . "'"
            ),
            'eventType' => array(self::BELONGS_TO, 'EventType', 'event_type_id'),
            'event' => array(self::BELONGS_TO, 'Event', 'event_id'),
            'user' => array(self::BELONGS_TO, 'User', 'created_user_id'),
            'usermodified' => array(self::BELONGS_TO, 'User', 'last_modified_user_id'),
            'social_history_occupation' => array(self::BELONGS_TO, 'SocialHistoryOccupation', 'social_history_occupation_id')
        );
    }

    /**
     * Add Lookup behaviour
     *
     * @return array
     */
    public function behaviors()
    {
        return array(
            'LookupTable' => 'LookupTable',
        );
    }

    /**
     * always order by display_order
     *
     * @return array
     */
    public function defaultScope()
    {
        return array('order' => $this->getTableAlias(true, false).'.display_order asc');
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'name' => 'Name',
            'child_default' => 'Is Default for Children',
            'social_history_occupation_id' => 'Social History Mapping'
        );
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     * @return \CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
     */
    public function search()
    {
        $criteria = new \CDbCriteria;
        $criteria->compare('id', $this->id, true);
        $criteria->compare('name', $this->name, true);

        return new \CActiveDataProvider(get_class($this), array(
            'criteria' => $criteria,
        ));
    }

    /**
     * @return integer|null
     */
    public static function defaultChildStatusId()
    {
        if ($child_default = self::model()->active()->findByAttributes(array('child_default' => 1))) {
            return $child_default->id;
        }
        return null;
    }

    /**
     * @param \SocialHistory $history
     * @return int|null
     */
    public static function defaultForSocialHistoryId(\SocialHistory $history)
    {
        if ($history->occupation_id !== null) {
            if ($default = self::model()->active()->findByAttributes(array('social_history_occupation_id' => $history->occupation_id))) {
                return $default->id;
            }
        }
        return null;
    }
}