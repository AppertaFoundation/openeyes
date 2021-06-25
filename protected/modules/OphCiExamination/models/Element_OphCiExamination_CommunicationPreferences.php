<?php
/**
 * OpenEyes.
 *
 * (C) OpenEyes Foundation, 2019
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2019, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

namespace OEModule\OphCiExamination\models;

use Yii;

/**
 * This is the model class for table "et_ophciexamination_communication_preferences".
 *
 * The followings are the available columns in table:
 *
 * @property string $id
 * @property int $event_id
 * @property int $correspondence_in_large_letters
 * @property int $agrees_to_insecure_email_correspondence
 * @property \Language $language
 * @property \Language $interpreter_required
 *
 */
class Element_OphCiExamination_CommunicationPreferences extends \BaseEventTypeElement
{
    use traits\CustomOrdering;
    public $service;

    protected $default_from_previous = true;

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
        return 'et_ophciexamination_communication_preferences';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('correspondence_in_large_letters, agrees_to_insecure_email_correspondence', 'safe'),
            array('language_id, interpreter_required_id', 'numerical', 'integerOnly' => true),
            array('correspondence_in_large_letters, agrees_to_insecure_email_correspondence', 'required'),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, event_id, correspondence_in_large_letters, agrees_to_insecure_email_correspondence, anticoagulant,language_id, interpreter_required_id', 'safe', 'on' => 'search'),
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
            'event' => array(self::BELONGS_TO, 'Event', 'event_id'),
            'user' => array(self::BELONGS_TO, 'User', 'created_user_id'),
            'language' => array(self::BELONGS_TO, 'Language', 'language_id'),
            'interpreter_required' => array(self::BELONGS_TO, 'Language', 'interpreter_required_id'),
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
            'correspondence_in_large_letters' => 'Large print for correspondence',
            'agrees_to_insecure_email_correspondence' => 'Agrees to insecure email correspondence',
            'language_id' => 'Language',
            'interpreter_required_id' => 'Interpreter required',
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

        $criteria->compare('correspondence_in_large_letters', $this->correspondence_in_large_letters);
        $criteria->compare('agrees_to_insecure_email_correspondence', $this->agrees_to_insecure_email_correspondence);

        return new \CActiveDataProvider(get_class($this), array(
            'criteria' => $criteria,
        ));
    }

    public function getLetter_string()
    {
        return "Large print for correspondence: $this->correspondence_in_large_letters\n Agrees to insecure email correspondence: $this->agrees_to_insecure_email_correspondence";
    }

    public function canCopy()
    {
        return true;
    }
}
