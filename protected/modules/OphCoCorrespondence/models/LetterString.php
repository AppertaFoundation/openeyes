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
 * The followings are the available columns in table '':.
 *
 * @property string $id
 * @property int $event_id
 *
 * The followings are the available model relations:
 * @property Event $event
 */
class LetterString extends LetterStringBase
{
    use MappedReferenceData;

    protected function getSupportedLevels(): int
    {
        return ReferenceData::LEVEL_INSTITUTION | ReferenceData::LEVEL_SITE;
    }

    protected function mappingColumn(int $level): string
    {
        return 'letter_string_id';
    }

    /**
     * Returns the static model of the specified AR class.
     *
     * @return LetterString|BaseActiveRecord the static model class
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
        return 'ophcocorrespondence_letter_string';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('letter_string_group_id, name, body, display_order, site_id, event_type, element_type', 'safe'),
            array('letter_string_group_id, name, body', 'required'),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('letter_string_group_id, name, body, display_order', 'safe', 'on' => 'search'),
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
            'user' => array(self::BELONGS_TO, 'User', 'created_user_id'),
            'usermodified' => array(self::BELONGS_TO, 'User', 'last_modified_user_id'),
            'letter_string_group' => array(self::BELONGS_TO, 'LetterStringGroup', 'letter_string_group_id'),
            'elementType' => array(self::HAS_ONE, 'ElementType', ['class_name' => 'element_type']),
            'eventType' => array(self::HAS_ONE, 'EventType', ['class_name' => 'event_type']),
            'institutions' => array(self::MANY_MANY, 'Institution', 'ophcocorrespondence_letter_string_institution(letter_string_id,institution_id)'),
            'sites' => array(self::MANY_MANY, 'Site', 'ophcocorrespondence_letter_string_site(letter_string_id,site_id)'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'institutions.name' => 'Institutions',
            'sites.name' => 'Sites',
            'letter_string_group_id' => 'Letter String Group',
            'elementType.name' => 'Element Type',
            'eventType.name' => 'Event Type'
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

        return new CActiveDataProvider(get_class($this), array(
            'criteria' => $criteria,
        ));
    }

    public function substitute($patient)
    {
        $this->body = OphCoCorrespondence_Substitution::replace($this->body, $patient);
    }

    public function getEventTypeName()
    {
        $eventType = EventType::model()->findByAttributes(array('class_name' => $this->event_type));
        if ($eventType) {
            return $eventType->name;
        }

        return '';
    }

    public function getElementTypeName()
    {
        $elementType = ElementType::model()->findByAttributes(array('class_name' => $this->element_type));
        if ($elementType) {
            return $elementType->name;
        }

        return '';
    }
}
