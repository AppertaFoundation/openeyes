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
 * This is the model class for table "ophciexamination_diagnosis".
 *
 * @property int $id
 * @property int $element_diagnoses_id
 * @property int $disorder_id
 * @property int $eye_id
 * @property bool $principal
 * @property string $date
 * @property string $time
 */
class OphCiExamination_Diagnosis extends \BaseActiveRecordVersioned
{
    /**
     * Returns the static model of the specified AR class.
     *
     * @return OphCiExamination_Diagnosis the static model class
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
        return 'ophciexamination_diagnosis';
    }

    /**
     * @return array validation rules for model diagnosiss.
     */
    public function rules()
    {
        return array(
                array('element_diagnoses_id,disorder_id,eye_id,time', 'required'),
                array('element_diagnoses_id,disorder_id,eye_id,principal,date,time', 'safe'),
                array('id, name', 'safe', 'on' => 'search'),
                array('date', 'OEFuzzyDateValidator'),
                array('time', 'CDateValidator', 'format' => array('h:m:s', 'h:m')),
        );
    }

    public function attributeLabels()
    {
        return array(
            'eye_id' => 'Eye',
            'date' => 'Date',
            'time' => 'Time'
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        return array(
            'element_diagnoses' => array(self::BELONGS_TO, 'OEModule\OphCiExamination\models\Element_OphCiExamination_Diagnoses', 'element_diagnoses_id'),
            'eye' => array(self::BELONGS_TO, 'Eye', 'eye_id'),
            'disorder' => array(self::BELONGS_TO, 'Disorder', 'disorder_id'),
        );
    }

    public function behaviors()
    {
        return array(
            'OeDateFormat' => array(
                'class' => 'application.behaviors.OeDateFormat',
                'date_columns' => [],
                'fuzzy_date_field' => 'date',
            ),
        );
    }


    /**
     * @param \BaseEventTypeElement $element
     *
     * @return OphCiExamination_Diagnosis
     */
    public function findAllByElement($element)
    {
        $element_type = $element->getElementType();

        return $this->findAll('element_type_id = :element_type_id', array(':element_type_id' => $element_type->id));
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     *
     * @return \CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
     */
    public function search()
    {
        $criteria = new \CDbCriteria();
        $criteria->compare('id', $this->id, true);
        $criteria->compare('name', $this->name, true);

        return new \CActiveDataProvider(get_class($this), array(
                'criteria' => $criteria,
        ));
    }

    /**
     * Return the disorder with the side it applies to if the side is not in disorder name.
     *
     * @return string
     */
    public function __toString()
    {

        if (strpos($this->disorder->term, $this->eye->adjective . ' ') === 0) {
            $term = $this->disorder->term;
        } else {
            $term = $this->eye->adjective . ' ' . $this->disorder->term;
        }

        return $term;
    }

    /**
     * @return mixed
     */
    public function getDisplayDate()
    {
        return \Helper::formatFuzzyDate($this->date);
    }
}
