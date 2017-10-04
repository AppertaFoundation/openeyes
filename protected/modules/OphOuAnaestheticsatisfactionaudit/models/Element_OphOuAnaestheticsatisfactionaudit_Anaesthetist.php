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
 * This is the model class for table "et_ophauanaestheticsataudit_anaesthetis".
 *
 * The followings are the available columns in table:
 *
 * @property string $id
 * @property int $event_id
 * @property int $anaesthetist_id
 * @property bool $non_consultant
 * @property bool $no_anaesthetist
 *
 * The followings are the available model relations:
 * @property ElementType $element_type
 * @property EventType $eventType
 * @property Event $event
 * @property User $user
 * @property User $usermodified
 * @property User $anaesthetist
 */
class Element_OphOuAnaestheticsatisfactionaudit_Anaesthetist extends BaseEventTypeElement
{
    public $service;
    public $anaesthetist_select;

    // form select and display values for exceptional anaesthetist values
    const NONCONSULTANT = 'non';
    const NONCONSULTANT_DISP = 'Non-consultant';
    const NOANAESTHETIST = 'no';
    const NOANAESTHETIST_DISP = 'No anaesthetist';

    public function afterFind()
    {
        if ($this->id) {
            // need to set the value on the anaesthetist_select for use in forms
            if ($this->non_consultant) {
                $this->anaesthetist_select = self::NONCONSULTANT;
            } elseif ($this->no_anaesthetist) {
                $this->anaesthetist_select = self::NOANAESTHETIST;
            } else {
                $this->anaesthetist_select = $this->anaesthetist_id;
            }
        }
    }

    /**
     * override to ensure support for custom attribute of anaesthetist_select
     * otherwise calls parent.
     *
     * @see CActiveRecord::hasAttribute()
     */
    public function hasAttribute($name)
    {
        if ($name == 'anaesthetist_select') {
            return true;
        }

        return parent::hasAttribute($name);
    }
    /**
     * Returns the static model of the specified AR class.
     *
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
        return 'et_ophouanaestheticsataudit_anaesthetis';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('event_id, anaesthetist_id, anaesthetist_select', 'safe'),
            array('anaesthetist_select', 'required'),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            //array('id, event_id, anaesthetist_id, ', 'safe', 'on' => 'search'),
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
            'anaesthetist' => array(self::BELONGS_TO, 'User', 'anaesthetist_id'),
        );
    }

    public function anaesthetistSelectList($selected_id)
    {
        $anaesthetistList = array(
                array('id' => self::NONCONSULTANT, 'text' => self::NONCONSULTANT_DISP),
                array('id' => self::NOANAESTHETIST, 'text' => self::NOANAESTHETIST_DISP),
        );

        foreach (OphOuAnaestheticsatisfactionaudit_AnaesthetistUser::model()->activeOrPk($selected_id)->findAll() as $anaesthetist) {
            $anaesthetistList[] = array('id' => $anaesthetist->user->id, 'text' => $anaesthetist->user->fullNameAndTitle);
        }

        return $anaesthetistList;
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'event_id' => 'Event',
            'anaesthetist_id' => 'Anaesthetist',
            'anaesthetist_select' => 'Anaesthetist',
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
        $criteria->compare('event_id', $this->event_id, true);

        $criteria->compare('anaesthetist_id', $this->anaesthetist_id);

        return new CActiveDataProvider(get_class($this), array(
                'criteria' => $criteria,
            ));
    }

    protected function beforeSave()
    {
        if ($this->anaesthetist_select == self::NONCONSULTANT) {
            $this->non_consultant = true;
            $this->no_anaesthetist = false;
            $this->anaesthetist_id = null;
        } elseif ($this->anaesthetist_select == self::NOANAESTHETIST) {
            $this->no_anaesthetist = true;
            $this->non_consultant = false;
            $this->anaesthetist_id = null;
        } else {
            $this->anaesthetist_id = $this->anaesthetist_select;
            $this->no_anaesthetist = false;
            $this->non_consultant = false;
        }

        return parent::beforeSave();
    }

    protected function afterSave()
    {
        return parent::afterSave();
    }

    protected function beforeValidate()
    {
        return parent::beforeValidate();
    }
}
