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

/**
 * This is the model class for table "ophtroperationbooking_operation_ward".
 *
 * The followings are the available columns in table:
 *
 * @property int $id
 * @property int $site_id
 * @property string $name
 * @property string $long_name
 * @property string $directions
 * @property int $restriction
 * @property string $code
 * @property int $display_order
 *
 * The followings are the available model relations:
 * @property Site $site
 * @property OphTrOperationbooking_Operation_Theatre $theatre
 */
class OphTrOperationbooking_Operation_Ward extends BaseActiveRecordVersioned
{
    const RESTRICTION_MALE = 1;
    const RESTRICTION_FEMALE = 2;
    const RESTRICTION_CHILD = 4;
    const RESTRICTION_ADULT = 8;
    const RESTRICTION_OBSERVATION = 16;

    /**
     * Returns the static model of the specified AR class.
     *
     * @return OphTrOperationbooking_Operation_Ward|BaseActiveRecord the static model class
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
        return 'ophtroperationbooking_operation_ward';
    }

    public function defaultScope()
    {
        return array('order' => $this->getTableAlias(true, false).'.name');
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('site_id, name, long_name, directions, theatre_id, code, restriction, display_order', 'safe'),
            array('site_id, name', 'required'),
            array('restriction', 'numerical', 'integerOnly' => true),
            array('site_id', 'length', 'max' => 10),
            array('name', 'length', 'max' => 255),
            // The following rule is used by search().
            // Please remove those attributes thaSt should not be searched.
            array('id, site_id, name, long_name, restriction', 'safe', 'on' => 'search'),
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
            'element_type' => array(self::HAS_ONE, 'ElementType', 'id', 'on' => "element_type.class_name='".get_class($this)."'"),
            'eventType' => array(self::BELONGS_TO, 'EventType', 'event_type_id'),
            'event' => array(self::BELONGS_TO, 'Event', 'event_id'),
            'user' => array(self::BELONGS_TO, 'User', 'created_user_id'),
            'usermodified' => array(self::BELONGS_TO, 'User', 'last_modified_user_id'),
            'site' => array(self::BELONGS_TO, 'Site', 'site_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'site_id' => 'Site',
            'theatre_id' => 'Theatre',
            'long_name' => 'Long name',
            'restriction_male' => 'Male only',
            'restriction_female' => 'Female only',
            'restriction_child' => 'Children only',
            'restriction_adult' => 'Adult only',
            'restriction_observation' => 'Observation only',
        );
    }

    public function behaviors()
    {
        return array(
            'LookupTable' => 'LookupTable',
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

        $criteria = new CDbCriteria();

        $criteria->compare('id', $this->id, true);
        $criteria->compare('name', $this->name, true);

        return new CActiveDataProvider(get_class($this), array(
                'criteria' => $criteria,
            ));
    }

    public function getLongName()
    {
        return $this->long_name ? $this->long_name : $this->name.' ward';
    }

    public function getDirectionsText()
    {
        return $this->directions ? $this->directions : $this->getLongName();
    }

    public function getRestrictionText()
    {
        $restrictions = array();

        if ($this->restriction & self::RESTRICTION_MALE) {
            $restrictions[] = '[MALE]';
        }
        if ($this->restriction & self::RESTRICTION_FEMALE) {
            $restrictions[] = '[FEMALE]';
        }
        if ($this->restriction & self::RESTRICTION_CHILD) {
            $restrictions[] = '[CHILD]';
        }
        if ($this->restriction & self::RESTRICTION_ADULT) {
            $restrictions[] = '[ADULT]';
        }
        if ($this->restriction & self::RESTRICTION_OBSERVATION) {
            $restrictions[] = '[OBS]';
        }

        if (empty($restrictions)) {
            return 'None';
        }

        return implode(' ', $restrictions);
    }

    public function getRestriction_male()
    {
        return $this->restriction & self::RESTRICTION_MALE ? 1 : 0;
    }

    public function getRestriction_female()
    {
        return $this->restriction & self::RESTRICTION_FEMALE ? 1 : 0;
    }

    public function getRestriction_child()
    {
        return $this->restriction & self::RESTRICTION_CHILD ? 1 : 0;
    }

    public function getRestriction_adult()
    {
        return $this->restriction & self::RESTRICTION_ADULT ? 1 : 0;
    }

    public function getRestriction_observation()
    {
        return $this->restriction & self::RESTRICTION_OBSERVATION ? 1 : 0;
    }
}
