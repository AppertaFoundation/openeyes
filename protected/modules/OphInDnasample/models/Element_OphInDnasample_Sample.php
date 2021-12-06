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
 * This is the model class for table "et_ophinbloodsample_sample".
 *
 * The followings are the available columns in table:
 *
 * @property string                     $id
 * @property int                        $event_id
 * @property int                        $old_dna_no
 * @property string                     $subject_id
 * @property string                     $blood_date
 * @property string                     $blood_location
 * @property string                     $comments
 * @property int                        $type_id
 *
 * The followings are the available model relations:
 * @property ElementType                $element_type
 * @property EventType                  $eventType
 * @property Event                      $event
 * @property User                       $user
 * @property User                       $usermodified
 * @property OphInDnasample_Sample_Type $type
 * @property mixed                      destination
 * @property mixed                      consented_by
 */
class Element_OphInDnasample_Sample extends BaseEventTypeElement
{
    public $service;

    protected $auto_update_relations = true;

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
        return 'et_ophindnasample_sample';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('event_id, old_dna_no,subject_id, blood_date, comments, type_id, volume', 'safe'),
            array('type_id, consented_by, studies', 'required'),
            array('other_sample_type', 'other_type_validator'),
            array('volume', 'volume_validator'),
            //array('destination', 'destination_validator'),
            array('consented_by, destination', 'safe'),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, event_id, old_dna_no, subject_id, blood_date, comments', 'safe', 'on' => 'search'),
        );
    }

    public function other_type_validator($attribute, $params)
    {
        if ($this->type_id === "4" && $this->other_sample_type === '') {
            $this->addError($attribute, 'Please specify sample type');
        }
    }

    public function volume_validator($attribute, $params)
    {

        if (strtolower($this->type->name) == 'blood' && $this->volume == '' ) {
            $this->addError($attribute, 'Please enter a value between 1 and 99');
        }

        if ($this->volume !== "") {
            if ($this->volume <= 0 || $this->volume > 99) {
                $this->addError($attribute, 'Please enter a value between 1 and 99');
            }
        }

        return true;
    }

    public function destination_validator($attribute, $params)
    {
        if ($this->is_local === 0 && $this->destination === '') {
            $this->addError($attribute, 'Please enter Destination');
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
            'element_type' => array(self::HAS_ONE, 'ElementType', 'id', 'on' => "element_type.class_name='" . get_class($this) . "'"),
            'eventType' => array(self::BELONGS_TO, 'EventType', 'event_type_id'),
            'event' => array(self::BELONGS_TO, 'Event', 'event_id'),
            'user' => array(self::BELONGS_TO, 'User', 'created_user_id'),
            'usermodified' => array(self::BELONGS_TO, 'User', 'last_modified_user_id'),
            'studies' => array(self::MANY_MANY, 'GeneticsStudy', 'et_ophindnasample_sample_genetics_studies(et_ophindnasample_sample_id, genetics_study_id)'),
            'type' => array(self::BELONGS_TO, 'OphInDnasample_Sample_Type', 'type_id'),
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
            'old_dna_no' => 'Old DNA no',
            'subject_id' => 'Subject',
            'blood_date' => 'Dna date',
            'comments' => 'Comments',
            'type_id' => 'Type',
            'other_sample_type' => '(if other, please specify)',
            'consented_by' => 'Consented By',
            'studies' => 'Study(s)',
            'destination' => 'Destination if not IOO',
            'volume' => 'Volume (Millilitres)',
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
        $criteria->compare('old_dna_no', $this->old_dna_no);
        $criteria->compare('subject_id', $this->subject_id);
        $criteria->compare('dna_date', $this->dna_date);
        $criteria->compare('comments', $this->comments);
        $criteria->compare('type_id', $this->type_id);
        $criteria->compare('consented_by', $this->consented_by);
        //$criteria->compare('is_local', $this->is_local);
        $criteria->compare('destination', $this->destination);

        return new CActiveDataProvider(get_class($this), array(
            'criteria' => $criteria,
        ));
    }

    protected function beforeSave()
    {
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
