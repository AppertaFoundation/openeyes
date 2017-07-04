<?php
/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2017
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2017, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */

namespace OEModule\OphCiExamination\models;

/**
 * Class SystemicDiagnoses_Diagnosis
 * @package OEModule\OphCiExamination\models
 *
 * @property int $element_id
 * @property int $side_id
 * @property int $disorder_id
 * @property string $date
 *
 * @property \Eye $side
 * @property SystemicDiagnoses $element
 * @property \Disorder $disorder
 */
class SystemicDiagnoses_Diagnosis extends \BaseEventTypeElement
{
    /**
     * Returns the static model of the specified AR class.
     *
     * @return static
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
        return 'ophciexamination_systemic_diagnoses_diagnosis';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('disorder', 'required'),
            array('date, side_id, disorder', 'safe'),
            array('date', 'OEFuzzyDateValidatorNotFuture'),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, date, disorder', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        return array(
            'element' => array(self::BELONGS_TO, 'OEModule\OphCiExamination\models\SystemicDiagnoses', 'element_id'),
            'disorder' => array(self::BELONGS_TO, 'Disorder', 'disorder_id'),
            'side' => array(self::BELONGS_TO, 'Eye', 'side_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'disorder' => 'Disorder',
            'date' => 'Date',
        );
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     *
     * @return \CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
     */
    public function search()
    {
        // Warning: Please modify the following code to remove attributes that
        // should not be searched.

        $criteria = new \CDbCriteria();

        $criteria->compare('id', $this->id, true);
        $criteria->compare('disorder_id', $this->disorder_id, true);
        $criteria->compare('date', $this->date);

        return new \CActiveDataProvider(get_class($this), array(
            'criteria' => $criteria,
        ));
    }

    /**
     * @return mixed
     */
    public function getDisplayDate()
    {
        return \Helper::formatFuzzyDate($this->date);
    }

    /**
     * @return string
     */
    public function getDisplayDisorder()
    {
        return ($this->side ? $this->side->adjective  . ' ' : '') . $this->disorder;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getDisplayDate() . ' ' . $this->getDisplayDisorder();
    }

    /**
     * @param \SecondaryDiagnosis $sd
     * @return static
     */
    public static function fromSecondaryDiagnosis(\SecondaryDiagnosis $sd)
    {
        $diagnosis = new static;
        foreach ($diagnosis->getAttributes() as $attr => $value) {
            if (in_array($attr, static::$sd_attribute_ignore)) {
                continue;
            }
            $key = array_key_exists($attr, static::$sd_attribute_map) ? static::$sd_attribute_map[$attr] : $attr;
            $diagnosis->$attr = $sd->$key;
        }
        return $diagnosis;
    }

    protected static $sd_attribute_ignore = array('id', 'element_id');
    protected static $sd_attribute_map = array('side_id' => 'eye_id');

    /**
     * @param \Patient $patient
     * @return \SecondaryDiagnosis
     */
    public function createSecondaryDiagnosis(\Patient $patient)
    {
        $sd = new \SecondaryDiagnosis();
        foreach ($this->getAttributes() as $attr => $val) {
            if (in_array($attr, static::$sd_attribute_ignore)) {
                continue;
            }
            $key = array_key_exists($attr, static::$sd_attribute_map) ? static::$sd_attribute_map[$attr] : $attr;
            $sd->$key = $val;
        }
        $sd->patient_id = $patient->id;
        return $sd;
    }
}