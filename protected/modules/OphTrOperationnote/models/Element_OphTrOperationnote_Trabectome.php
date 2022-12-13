<?php

/**
 * OpenEyes.
 *
 * (C) OpenEyes Foundation, 2014
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2014, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

/**
 * This is the model class for table "et_ophtroperationnote_trabectome".
 *
 * The followings are the available columns in table 'et_ophtroperationnote_trabectome':
 *
 * @property string $id
 * @property int $event_id
 * @property int $power_id
 * @property bool $blood_reflux
 * @property bool $hpmc
 * @property string $description
 * @property string $complication_other
 * @property json $eyedraw
 * @property int $last_modified_user_id
 * @property datetime $last_modified_date
 * @property int $created_user_id
 * @property datetime $created_date
 *
 * The followings are the available model relations:
 * @property Event $event
 * @property OphTrOperationnote_Trabectome_Power $power
 * @property OphTrOperationnote_Trabectome_Complication[] $complications
 * @property OphTrOperationnote_Trabectome_ComplicationAssignment $complication_assigments - DO NOT use outside model
 */
class Element_OphTrOperationnote_Trabectome extends Element_OnDemand
{
    public $service;

    /**
     * Returns the static model of the specified AR class.
     *
     * @return Element_OphTrOperationnote_Trabectome the static model class
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
        return 'et_ophtroperationnote_trabectome';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('power_id, blood_reflux, hpmc, description', 'required'),
            array('event_id, power_id, blood_reflux, hpmc, description, eyedraw, complication_other', 'safe'),
            array('complication_other', 'requiredIfComplicationOther'),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, event_id, power_id, blood_reflux, hpmc, report', 'safe', 'on' => 'search'),
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
                'element_type' => array(self::HAS_ONE, 'ElementType', 'id', 'on' => "element_type.class_name='" . get_class($this) . "'"),
                'event' => array(self::BELONGS_TO, 'Event', 'event_id'),
                'user' => array(self::BELONGS_TO, 'User', 'created_user_id'),
                'usermodified' => array(self::BELONGS_TO, 'User', 'last_modified_user_id'),
                'power' => array(self::BELONGS_TO, 'OphTrOperationnote_Trabectome_Power', 'power_id'),
                'complication_assignments' => array(self::HAS_MANY, 'OphTrOperationnote_Trabectome_ComplicationAssignment', 'element_id'),
                'complications' => array(self::HAS_MANY, 'OphTrOperationnote_Trabectome_Complication', 'complication_id', 'through' => 'complication_assignments'),
        );
    }

    public function beforeDelete()
    {
        OphTrOperationnote_Trabectome_ComplicationAssignment::model()->deleteAll('element_id = ?', array($this->id));

        return parent::beforeDelete();
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'power_id' => 'Power',
            'blood_reflux' => 'Blood reflux',
            'hpmc' => 'HPMC',
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
        $criteria->compare('gauge_id', $this->gauge_id);
        $criteria->compare('pvd_induced', $this->pvd_induced);

        return new CActiveDataProvider(get_class($this), array(
                'criteria' => $criteria,
        ));
    }

    public function getPrefillableAttributeSet()
    {
        $attributes = [
            'power_id',
            'blood_reflux',
            'hpmc',
            'eyedraw',
            'description'
        ];


        if (SettingMetadata::model()->checkSetting('allow_complications_in_pre_fill_templates', 'on')) {
            $attributes['complications'] = 'id';
            $attributes[] = 'complication_other';
        }

        return $attributes;
    }

    /**
     * Ensures the attribute is provided when an 'other' complication is selected.
     *
     * @param $attribute
     * @param $params
     */
    public function requiredIfComplicationOther($attribute, $params)
    {
        if ($this->hasOtherComplication() && !$this->$attribute) {
            $this->addError($attribute, $this->getAttributeLabel($attribute) . ' cannot be blank.');
        }
    }

    /**
     * Check if any of the complications on the element is an "other" complication (i.e. in need of further information).
     *
     * @return bool
     */
    public function hasOtherComplication()
    {
        foreach ($this->complications as $comp) {
            if ($comp->other) {
                return true;
            }
        }

        return false;
    }
    /**
     * Get a list of the ids of the currently assigned complications on this element.
     *
     * @return array
     */
    public function getComplicationIDs()
    {
        $res = array();
        foreach ($this->complications as $comp) {
            $res[] = $comp->id;
        }

        return $res;
    }

    /**
     * Returns comma separated list of complications on this procedure note.
     *
     * @param $default
     *
     * @return string
     */
    public function getComplicationsString($default = 'None')
    {
        $res = array();
        $other = false;
        foreach ($this->complications as $comp) {
            if ($comp->other) {
                $other = true;
            } else {
                $res[] = $comp->name;
            }
        }
        if ($other) {
            $res[] = $this->complication_other;
        }
        if ($res) {
            return implode(', ', $res);
        } else {
            return $default;
        }
    }

    /**
     * Update the complication assignments for this element.
     *
     * @param array $ids - OphTrOperationnote_Trabectome_Complication ids
     *
     * @throws Exception
     */
    public function updateComplications($ids)
    {
        $curr_by_id = array();
        $save = array();

        foreach ($this->complication_assignments as $ca) {
            $curr_by_id[$ca->complication_id] = $ca;
        }
        if (!empty($ids)) {
            foreach ($ids as $id) {
                if (!array_key_exists($id, $curr_by_id)) {
                    $ass = new OphTrOperationnote_Trabectome_ComplicationAssignment();
                    $ass->attributes = array('element_id' => $this->id, 'complication_id' => $id);
                    $save[] = $ass;
                } else {
                    unset($curr_by_id[$id]);
                }
            }
        }

        foreach ($save as $s) {
            if (!$s->save()) {
                throw new Exception('Unable to save complication assignment:' . print_r($s->getErrors(), true));
            };
        }

        foreach ($curr_by_id as $curr) {
            if (!$curr->delete()) {
                throw new Exception('unable to delete complication assignment:' . print_r($curr->getErrors(), true));
            }
        }
    }

    protected function applyComplexData($data, $index): void
    {
        $model_name = CHtml::modelName($this);
        $complications = array();
        if (@$data[$model_name]['complications']) {
            foreach ($data[$model_name]['complications'] as $id) {
                $complications[] = OphTrOperationnote_Trabectome_Complication::model()->findByPk($id);
            }
        }
        $this->complications = $complications;
    }
}
