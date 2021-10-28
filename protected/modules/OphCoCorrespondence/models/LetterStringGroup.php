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
 * @property int $institution_id
 *
 * The followings are the available model relations:
 * @property Event $event
 */
class LetterStringGroup extends BaseEventTypeElement
{
    protected $auto_update_relations = true;

    /**
     * Returns the static model of the specified AR class.
     *
     * @param string $className
     * @return LetterStringGroup the static model class
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
        return 'ophcocorrespondence_letter_string_group';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('name, display_order, institution_id', 'safe'),
            array('name', 'required'),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('name, display_order, institution_id', 'safe', 'on' => 'search'),
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
            'letterStrings' => array(self::HAS_MANY, 'LetterString', 'letter_string_group_id'),
            'firmLetterStrings' => array(self::HAS_MANY, 'FirmLetterString', 'letter_string_group_id'),
            'subspecialtyLetterStrings' => array(self::HAS_MANY, 'SubspecialtyLetterString', 'letter_string_group_id'),
            'siteLetterStrings' => array(self::HAS_MANY, 'LetterString_Site', 'letter_string_group_id', 'through' => 'letterStrings'),
            'institutionLetterStrings' => array(self::HAS_MANY, 'LetterString_Institution', 'letter_string_group_id', 'through' => 'letterStrings'),
            'institution' => array(self::BELONGS_TO, 'Institution', 'institution_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'institution.name' => 'Institution',
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
        $criteria->compare('institution_id', $this->institution_id, true);

        return new CActiveDataProvider(get_class($this), array(
            'criteria' => $criteria,
        ));
    }

    public function getStrings($patient, $event_types)
    {
        if ($this->name === 'Findings') {
            if ($api = Yii::app()->moduleAPI->get('OphCiExamination')) {
                $strings = array();
                foreach ($api->getElementsForLatestVisibleEvent($patient) as $element_type) {
                    $strings['examination'.$element_type->id] = $element_type->name;
                }
                return $strings;
            }
        }

        $strings = array();
        $string_names = array();

        foreach ($this->firmLetterStrings as $flm) {
            if (!in_array($flm->name, $string_names)) {
                if ($flm->shouldShow($patient, $event_types)) {
                    $strings['firm'.$flm->id] = $string_names[] = $flm->name;
                }
            }
        }

        foreach ($this->subspecialtyLetterStrings as $slm) {
            if (!in_array($slm->name, $string_names)) {
                if ($slm->shouldShow($patient, $event_types)) {
                    $strings['subspecialty'.$slm->id] = $string_names[] = $slm->name;
                }
            }
        }

        foreach ($this->letterStrings as $lm) {
            if (!in_array($lm->name, $string_names)) {
                if ($lm->shouldShow($patient, $event_types)) {
                    $strings['site'.$lm->id] = $string_names[] = $lm->name;
                }
            }
        }

        return $strings;
    }
}
