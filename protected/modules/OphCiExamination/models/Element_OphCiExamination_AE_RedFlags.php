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
 * This is the model class for table "et_ophciexamination_ae_red_flags".
 *
 * The followings are the available columns in table:
 *
 * @property string $id
 * @property int $event_id
 * @property string $comments
 *
 * The followings are the available model relations:
 */
class Element_OphCiExamination_AE_RedFlags extends \BaseEventTypeElement
{
    use traits\CustomOrdering;
    public $service;

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
        return 'et_ophciexamination_ae_red_flags';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
                // The following rule is used by search().
                // Please remove those attributes that should not be searched.
                array('id, event_id, nrf_check, flags', 'safe'),
                array('nrf_check', 'required'),
                array('id, event_id, nrf_check', 'safe', 'on' => 'search'),
                array('flags', 'RequiredIfFieldValidator', 'field' => 'nrf_check', 'value' => '0'),
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
                'flags' => array(self::HAS_MANY,'OEModule\OphCiExamination\models\OphCiExamination_AE_RedFlags_Options_Assignment', 'element_id'),
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
                'nrf_check' => 'No red flags for current attendance',
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

    public function afterSave()
    {
        // get current flags to check for deletion
        $curCriteria = new \CDbCriteria();
        $curCriteria->compare('element_id', $this->id, true);
        $allCurrentFlags = OphCiExamination_AE_RedFlags_Options_Assignment::model()->findAll($curCriteria);

        // if we have no new flags to check, we just delete all of them
        if ($this->flags && !empty($this->flags[0])&& is_object($this->flags[0])) {
            foreach ($allCurrentFlags as $curFlag) {
                    $curFlag->delete();
            }
        }
        // if we have flags to check, go through them.
        elseif ($this->flags && !empty($this->flags[0])&& !is_object($this->flags[0])) {
             // check each current flag
            foreach ($allCurrentFlags as $curFlag) {
                //if the flag is not in the new flags list, delete it.
                if (!in_array($curFlag->id, $this->flags)) {
                    $curFlag->delete();
                }
            }
        }

        // if we have flags to add
        if ($this->flags && !empty($this->flags[0])&& !is_object($this->flags[0])) {
            foreach ($this->flags as $flag) {
                // find all
                $criteria = new \CDbCriteria();
                $criteria->compare('element_id', $this->id, true);
                $criteria->compare('red_flag_id', $flag, true);
                $currentFlags = OphCiExamination_AE_RedFlags_Options_Assignment::model()->findAll($criteria);
                // if the element doesn't already contain this element
                if (empty($currentFlags)) {
                    $newFlagObj = new OphCiExamination_AE_RedFlags_Options_Assignment();
                    $newFlagObj->red_flag_id = $flag;
                    $newFlagObj->element_id = $this->id;
                    $newFlagObj->save();
                }
            }
        }
        parent::afterSave();
    }

    public function getFlagOptions()
    {
        $criteria = new \CDbCriteria();
        $criteria->condition = "active = 1";
        return OphCiExamination_AE_RedFlags_Options::model()->findAll($criteria);
    }

    public function getMyFlagOptions()
    {
        $criteria = new \CDbCriteria();
        $criteria->condition = "active = 1";
        $options = OphCiExamination_AE_RedFlags_Options::model()->findAll($criteria);
        $levels = OphCiExamination_AE_RedFlags_Options::model()->enumerateSupportedLevels();
        $output = [];
        foreach ($options as $option) {
            foreach ($levels as $level) {
                if ($option->hasMapping($level, OphCiExamination_AE_RedFlags_Options::model()->getIdForLevel($level))) {
                    $output[]= $option;
                }
            }
        }
        return array_unique($output, SORT_REGULAR);
    }

    public function getCurrentFlagOptionIDs()
    {
        return array_map(function ($flag) {
            return $flag->red_flag_id;
        }, $this->flags);
    }

    public function getMyCurrentFlagOptionIDs()
    {
        $criteria = new \CDbCriteria();
        $criteria->condition = "active = 1";
        $options = OphCiExamination_AE_RedFlags_Options::model()->findAll($criteria);
        $levels = OphCiExamination_AE_RedFlags_Options::model()->enumerateSupportedLevels();
        $output = [];
        foreach ($options as $option) {
            foreach ($levels as $level) {
                if ($option->hasMapping($level, OphCiExamination_AE_RedFlags_Options::model()->getIdForLevel($level))) {
                    $output[]= $option->id;
                }
            }
        }
        return array_unique($output);
    }
}
