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
 * This is the model class for table "et_ophtrintravitinjection_complicat".
 *
 * The followings are the available columns in table:
 *
 * @property string $id
 * @property int $event_id
 * @property string $oth_descrip
 *
 * The followings are the available model relations:
 * @property ElementType $element_type
 * @property EventType $eventType
 * @property Event $event
 * @property User $user
 * @property User $usermodified
 * @property array(OphTrIntravitrealinjection_Complication) $left_complications
 * @property array(OphTrIntravitrealinjection_Complication) $right_complications
 */
class Element_OphTrIntravitrealinjection_Complications extends SplitEventTypeElement
{
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
        return 'et_ophtrintravitinjection_complications';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('event_id, eye_id, left_oth_descrip, right_oth_descrip', 'safe'),
            array('left_complications', 'complicationsOtherValidation', 'side' => 'left'),
            array('right_complications', 'complicationsOtherValidation', 'side' => 'right'),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, event_id, eye_id, left_oth_descrip, right_oth_descrip', 'safe', 'on' => 'search'),
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
            'eye' => array(self::BELONGS_TO, 'Eye', 'eye_id'),
            'complication_assignments' => array(self::HAS_MANY, 'OphTrIntravitrealinjection_ComplicationAssignment', 'element_id'),
            'left_complications' => array(self::HAS_MANY, 'OphTrIntravitrealinjection_Complication', 'complication_id', 'through' => 'complication_assignments', 'on' => 'complication_assignments.eye_id = '.SplitEventTypeElement::LEFT),
            'right_complications' => array(self::HAS_MANY, 'OphTrIntravitrealinjection_Complication', 'complication_id', 'through' => 'complication_assignments', 'on' => 'complication_assignments.eye_id = '.SplitEventTypeElement::RIGHT),
        );
    }

    public function sidedFields()
    {
        return array('oth_descrip');
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'event_id' => 'Event',
            'left_complications' => 'Complications',
            'right_complications' => 'Complications',
            'left_oth_descrip' => 'Other Description',
            'right_oth_descrip' => 'Other Description',
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
        $criteria->compare('oth_descrip', $this->oth_descrip);

        return new CActiveDataProvider(get_class($this), array(
            'criteria' => $criteria,
        ));
    }

    public function getophtrintravitinjection_complication_defaults()
    {
        $ids = array();
        foreach (OphTrIntravitrealinjection_Complication::model()->active()->findAll('`default` = 1') as $item) {
            $ids[] = $item->id;
        }

        return $ids;
    }

    /*
     * only need a text "other" description for complications that are flagged "other"
    */
    public function complicationsOtherValidation($attribute, $params)
    {
        $other_comp = null;
        $complications = $this->{$params['side'].'_complications'};
        if ( is_array($complications) ) {
            foreach ($complications as $comp) {
                if ($comp->description_required) {
                    $other_comp = $comp;
                }
            }
        }
        if ($other_comp) {
            $v = CValidator::createValidator(
                'requiredIfSide',
                $this,
                array($params['side'].'_oth_descrip'),
                array('side' => $params['side'], 'message' => ucfirst($params['side']).' {attribute} required when '.$other_comp->name.' is selected')
            );
            $v->validate($this);
        }
    }

    /**
     * update the complications for the given side.
     *
     * @param string $side
     * @param int[]  $complication_ids - array of complication ids to assign to the element
     */
    public function updateComplications($side, $complication_ids)
    {
        $current_complications = array();
        $save_complications = array();

        foreach ($this->complication_assignments as $curr_comp) {
            if ($curr_comp->eye_id == $side) {
                $current_complications[$curr_comp->complication_id] = $curr_comp;
            }
        }

        // go through each update complication id, if it isn't assigned for this element,
        // create assignment and store for saving
        // if there is, remove from the current complications array
        // anything left in current complications at the end is ripe for deleting
        if ( is_array($complication_ids) ) {
            foreach ($complication_ids as $comp_id) {
                if (!array_key_exists($comp_id, $current_complications)) {
                    $s = new OphTrIntravitrealinjection_ComplicationAssignment();
                    $s->attributes = array('element_id' => $this->id, 'eye_id' => $side, 'complication_id' => $comp_id);
                    $save_complications[] = $s;
                } else {
                    // don't want to delete later
                    unset($current_complications[$comp_id]);
                }
            }
        }
        // save what needs saving
        foreach ($save_complications as $save) {
            $save->save();
        }
        // delete the rest
        foreach ($current_complications as $curr) {
            $curr->delete();
        }
    }

    /**
     * Get the ids of the complications currently associated with the element.
     */
    public function getComplicationValues()
    {
        $complication_values = array();

        foreach ($this->complication_assignments as $complication_assignment) {
            $complication_values[] = $complication_assignment->complication_id;
        }

        return $complication_values;
    }
}
