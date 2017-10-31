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
 * This is the model class for table "event_type".
 *
 * The followings are the available columns in table 'event_type':
 *
 * @property int     $id
 * @property string  $name
 *
 * The followings are the available model relations:
 * @property Event[] $events
 */
class EventType extends BaseActiveRecordVersioned
{
    /**
     * Returns the static model of the specified AR class.
     *
     * @return EventType the static model class
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
        return 'event_type';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('name', 'required'),
            array('name', 'length', 'max' => 40),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, name', 'safe', 'on' => 'search'),
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
            'events' => array(self::HAS_MANY, 'Event', 'event_type_id'),
            'elementTypes' => array(self::HAS_MANY, 'ElementType', 'event_type_id'),
            'parent' => array(self::BELONGS_TO, 'EventType', 'parent_id'),
            'children' => array(self::HAS_MANY, 'EventType', 'parent_id'),
        );
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
     * Get all the event types for the modules that are currently enabled (and aren't legacy).
     *
     * @return EventType[]
     */
    public function getEventTypeModules()
    {
        $legacy_events = EventGroup::model()->find('code=?', array('Le'));

        $criteria = new CDbCriteria();
        $criteria->condition = "class_name in ('" . implode("','", array_keys(Yii::app()->getModules())) . "') and event_group_id != $legacy_events->id";
        $criteria->order = 'name asc';
        $criteria->addCondition('parent_id is null');

        return self::model()->findAll($criteria);
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

    /**
     * Get the Specialty for this event type (as defined by the first camelcase section of the module name).
     *
     * @return Specialty
     */
    public function getSpecialty()
    {
        preg_match('/^([A-Z][a-z]+)([A-Z][a-z]+)([A-Z][a-z]+)$/', $this->class_name, $m);

        return Specialty::model()->find('code=?', array(strtoupper($m[1])));
    }

    /**
     * Get the EventGroup for the module of the event type (as defined by the second camelcase section of the module name).
     *
     * @return CActiveRecord
     */
    public function getEvent_group()
    {
        preg_match('/^([A-Z][a-z]+)([A-Z][a-z]+)([A-Z][a-z]+)$/', $this->class_name, $m);

        return EventGroup::model()->find('code=?', array($m[2]));
    }

    /*
     * get list data of all the event types that are currently enabled on the instance
     *
     * @return array
     */

    public function getActiveList()
    {
        $legacy = EventGroup::model()->find('name=?', array('Legacy data'));

        $event_types = array_keys(Yii::app()->modules);

        $criteria = new CDbCriteria();
        $criteria->order = 'name asc';
        $criteria->addInCondition('class_name', $event_types);
        $criteria->addCondition('event_group_id != :egi');
        $criteria->params[':egi'] = $legacy->id;

        return CHtml::listData(self::model()->findAll($criteria), 'id', 'name');
    }

    /**
     * get list data of all the event types for which >0 events exist.
     *
     * @return array
     */
    public function getEventTypeInUseList()
    {
        $criteria = new CDbCriteria();
        $criteria->distinct = true;
        $criteria->select = 'event_type_id';

        $event_type_ids = array();
        foreach (Event::model()->findAll($criteria) as $event) {
            $event_type_ids[] = $event->event_type_id;
        }

        $criteria = new CDbCriteria();
        $criteria->addInCondition('id', $event_type_ids);
        $criteria->order = 'name asc';
        $criteria->addCondition('parent_id is null');

        return CHtml::listData(self::model()->findAll($criteria), 'id', 'name');
    }

    /**
     * Check if the event type is disabled.
     *
     * @return bool
     */
    public function getDisabled()
    {
        if (is_array(Yii::app()->params['modules_disabled'])) {
            foreach (Yii::app()->params['modules_disabled'] as $module => $params) {
                if (is_array($params)) {
                    if ($module == $this->class_name) {
                        return true;
                    }
                } else {
                    if ($params == $this->class_name) {
                        return true;
                    }
                }
            }
        }

        return false;
    }

    /**
     * string to display for the event type when it's disabled
     * (note this method assumes the event type is disabled, this should be checked with the disabled attribute).
     *
     * @return string
     */
    public function getDisabled_title()
    {
        if (isset(Yii::app()->params['modules_disabled'][$this->class_name]['title'])) {
            return Yii::app()->params['modules_disabled'][$this->class_name]['title'];
        }

        return 'This module is disabled';
    }

    /**
     * String to display detailed information about a disabled event type
     * (note this method assumes the event type is disabled, this should be checked with the disabled attribute).
     *
     * @return string
     */
    public function getDisabled_detail()
    {
        if (isset(Yii::app()->params['modules_disabled'][$this->class_name]['detail'])) {
            return Yii::app()->params['modules_disabled'][$this->class_name]['detail'];
        }

        return 'The ' . $this->name . ' module will be available in an upcoming release.';
    }

    /**
     * Return the module api for this event type.
     *
     * @return mixed
     */
    public function getApi()
    {
        return Yii::app()->moduleAPI->get($this->class_name);
    }

    /**
     * Register a short code for this event type.
     *
     * @param      $code
     * @param      $method
     * @param bool $description
     *
     * @throws Exception
     */
    public function registerShortCode($code, $method, $description = false)
    {
        if (!preg_match('/^[a-zA-Z]{3}$/', $code)) {
            throw new Exception("Invalid shortcode: $code");
        }

        $default_code = $code;

        if (PatientShortcode::model()->find('code=?', array(strtolower($code)))) {
            $n = '00';
            while (PatientShortcode::model()->find('z' . $n)) {
                $n = str_pad((int)$n + 1, 2, '0', STR_PAD_LEFT);
            }
            $code = "z$n";

            echo "Warning: attempt to register duplicate shortcode '$default_code', replaced with 'z$n'\n";
        }

        $ps = new PatientShortcode();
        $ps->event_type_id = $this->id;
        $ps->code = $code;
        $ps->default_code = $default_code;
        $ps->method = $method;
        $ps->description = $description;

        if (!$ps->save()) {
            throw new Exception('Unable to save PatientShortcode: ' . print_r($ps->getErrors(), true));
        }
    }

    public function getDefaultElements()
    {
        $criteria = new CDbCriteria();
        $criteria->compare('event_type_id', $this->id);
        $criteria->compare('`default`', 1);
        $criteria->order = 'display_order asc';

        $elements = array();
        foreach (ElementType::model()->findAll($criteria) as $element_type) {
            $element_class = $element_type->class_name;
            $elements[] = new $element_class();
        }

        return $elements;
    }

    /**
     * Returns the parent(s) of this event type.
     *
     * @return EventType[]
     */
    public function getParentEventTypes()
    {
        $res = array();
        if ($this->parent_event_type_id) {
            $parent = self::model()->findByPk($this->parent_event_type_id);
            $res = $parent->getParentEventTypes();
            $res[] = $parent;
        }

        return $res;
    }

    /**
     * Get all the element types that are defined for this event type.
     *
     * @return ElementType[]
     */
    public function getAllElementTypes()
    {
        $criteria = new CDbCriteria();
        $ids = array($this->id);
        foreach ($this->getParentEventTypes() as $parent) {
            $ids[] = $parent->id;
        }
        $criteria->addInCondition('event_type_id', $ids);
        $criteria->order = 'display_order asc';

        return ElementType::model()->findAll($criteria);
    }

    /**
     * Get the root event type elements with associated children.
     *
     * @return BaseEventTypeElement[]
     */
    public function getRootElementTypes()
    {
        $criteria = new CDbCriteria();
        $ids = array($this->id);
        foreach ($this->getParentEventTypes() as $parent) {
            $ids[] = $parent->id;
        }
        $criteria->addInCondition('t.event_type_id', $ids);
        $criteria->addCondition('t.parent_element_type_id IS NULL');
        $criteria->order = 't.display_order asc';

        return ElementType::model()->with('child_element_types')->findAll($criteria);
    }

    /**
     * @param string $type
     * @param Event $event
     * @return string
     */
    public function getEventIcon($type='small', Event $event = null)
    {
        $asset_manager = Yii::app()->getAssetManager();
        $module_path = Yii::getPathOfAlias('application.modules.' . $this->class_name . '.assets.img');

        $possible_images = array();
        if ($event && $event->is_automated) {
            $possible_images[] = $type . '-auto.png';
        }
        $possible_images[] = $type . '.png';

        // module specific
        foreach ($possible_images as $possible) {
            $file = $module_path . '/' . $possible;
            if (file_exists($file)) {
                return $asset_manager->publish($file);
            }
        }

        return '';
    }
}
