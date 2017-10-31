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
 * This is the model class for table "et_ophindnaextraction_dnaextraction".
 *
 * The followings are the available columns in table:
 *
 * @property string $id
 * @property int $event_id
 * @property int $box_id
 * @property int $letter_id
 * @property int $number_id
 * @property string $extracted_date
 * @property int $extracted_by_id
 * @property string $comments
 * @property int $volume
 *
 * The followings are the available model relations:
 * @property ElementType $element_type
 * @property EventType $eventType
 * @property Event $event
 * @property User $user
 * @property User $usermodified
 * @property User $extracted_by
 */
class Element_OphInDnaextraction_DnaExtraction extends BaseEventTypeElement
{
    /**
     * Returns the static model of the specified AR class.
     *
     * @return the static model class
     */
    public $box_id;
    public $letter;
    public $number;
    
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'et_ophindnaextraction_dnaextraction';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('event_id, storage, extracted_date, extracted_by,extracted_by_id, comments, dna_concentration, volume,', 'safe'),
            array('dna_quantity', 'safe'),
            array('dna_quality', 'numerical', 'min' => 0.2, 'max' => 2.5),
            array('storage_id', 'required'),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, event_id,storage, key_address, extracted_date, extracted_by_id, comments, dna_concentration, volume, ', 'safe', 'on' => 'search'),
            array('dna_concentration', 'numerical', 'numberPattern' => '/^\s*[\+\-]?\d+\.?\d*\s*$/'),
            //array('number_id', 'boxAvailable'),
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
            'extracted_by' => array(self::BELONGS_TO, 'User', 'extracted_by_id'),
            'storage' => array(self::BELONGS_TO, 'OphInDnaextraction_DnaExtraction_Storage', 'storage_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'storage_id' => 'Storage',
            'event_id' => 'Event',
            'box' => 'Box',
            'letter' => 'Letter',
            'number' => 'Number',
            'key_address' => 'Key address',
            'extracted_date' => 'Extracted Date',
            'extracted_by_id' => 'Extracted By',
            'comments' => 'Comments',
            'dna_concentration' => 'DNA Concentration (ng/ul)',
            'volume' => 'Volume (microlitres ul)',
            'dna_quantity' => '260x230nm',
            'dna_quality' => '260x280nm',
            'box_id' => 'Box',
            'letter_id' => 'Letter',
            'number_id' => 'Number',
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
        $criteria->compare('box', $this->box);
        $criteria->compare('letter', $this->letter);
        $criteria->compare('number', $this->number);
        $criteria->compare('key_address', $this->key_address);
        $criteria->compare('extracted_date', $this->extracted_date);
        $criteria->compare('extracted_by_id', $this->extracted_by_id);
        $criteria->compare('comments', $this->comments);
        $criteria->compare('dna_concentration', $this->dna_concentration);
        $criteria->compare('volume', $this->volume);
        
        return new CActiveDataProvider(get_class($this), array(
            'criteria' => $criteria,
        ));
    }

    public function beforeValidate()
    {
        return parent::beforeValidate();
    }

}
