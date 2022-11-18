<?php
/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2019
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2019, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

namespace OEModule\OphCiExamination\models;

use OE\factories\models\traits\HasFactory;

/**
 * Class SystemicDiagnoses_Diagnosis
 * @package OEModule\OphCiExamination\models
 *
 * @property int $element_id
 * @property int $side_id
 * @property int $disorder_id
 * @property string $date
 * @property int $secondary_diagnosis_id
 *
 * @property \Eye $side
 * @property SystemicDiagnoses $element
 * @property \Disorder $disorder
 * @property \SecondaryDiagnosis $secondary_diagnosis
 */
class SystemicDiagnoses_Diagnosis extends \BaseEventTypeElement
{
    use HasFactory;

    public static $PRESENT = 1;
    public static $NOT_PRESENT = 0;
    public static $NOT_CHECKED = -9;

    protected static $sd_attribute_ignore = array('id', 'element_id', 'secondary_diagnosis_id', 'has_disorder');
    protected static $sd_attribute_map = array('side_id' => 'eye_id');

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
            array('has_disorder', 'required', 'message' => 'Status cannot be blank'),
            array('date, side_id, disorder, has_disorder', 'safe'),
            array('side_id', 'sideValidator'),
            array('date', 'OEFuzzyDateValidatorNotFuture'),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, date, disorder, has_disorder', 'safe', 'on' => 'search'),
        );
    }

    public function sideValidator($attribute, $params)
    {
        if (!$this->side_id) {
            $this->addError($attribute, "Eye must be selected");
        }

    }

    public function beforeSave()
    {
        //-9 is the N/A option but we do not save it, if null is posted that means
        //the user did not checked any checkbox so we return error in the validation part
        if ($this->side_id == -9) {
            $this->side_id = null;
        }

        return parent::beforeSave();
    }

    protected function getSecondaryDiagnosisRelation()
    {
        return array(self::BELONGS_TO, 'SecondaryDiagnosis', 'secondary_diagnosis_id');
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
            'secondary_diagnosis' => $this->getSecondaryDiagnosisRelation(),
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
        $criteria->compare('has_disorder', $this->has_disorder, true);

        return new \CActiveDataProvider(get_class($this), array(
            'criteria' => $criteria,
        ));
    }

    /**
     * @return string
     */
    public function getDisplayHasDisorder()
    {
        if ($this->has_disorder === (string) static::$PRESENT) {
            return 'Present';
        } elseif ($this->has_disorder === (string) static::$NOT_PRESENT) {
            return 'Not present';
        }
        return 'Not checked';
    }

    /**
     * @return bool
     */
    public function isAtTip()
    {
        return ($this->secondary_diagnosis !== null
            && $this->secondary_diagnosis->last_modified_date <= $this->last_modified_date);
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
        return '<strong>' . $this->getDisplayHasDisorder() . ':</strong> ' . $this->getDisplayDate() . ' ' . $this->getDisplayDisorder();
    }

    /**
     * Create a new instance from the given secondary diagnosis
     *
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

    /**
     * @return \SecondaryDiagnosis
     */

    protected function getNewSecondaryDiagnosis()
    {
        return new \SecondaryDiagnosis();
    }

    /**
     * @param \Patient $patient
     * @return \SecondaryDiagnosis
     * @throws \Exception
     */
    public function updateAndGetSecondaryDiagnosis(\Patient $patient)
    {
        if (!$sd = $this->secondary_diagnosis) {
            $sd = $this->getNewSecondaryDiagnosis();
            $sd->patient_id = $patient->id;
        }

        foreach ($this->getAttributes() as $attr => $val) {
            if (in_array($attr, static::$sd_attribute_ignore)) {
                continue;
            }
            $key = array_key_exists($attr, static::$sd_attribute_map) ? static::$sd_attribute_map[$attr] : $attr;
            $sd->$key = $val;
        }
        $sd->save();
        $this->secondary_diagnosis_id = $sd->id;
        $this->save(false);
        return $sd;
    }

    /**
     * @param $status_id
     * @return string
     */

    public static function getStatusNameEditMode($status_id)
    {
        switch ($status_id) {
            case self::$PRESENT:
                return 'Yes';
                break;
            case self::$NOT_PRESENT:
                return 'No';
                break;
            case self::$NOT_CHECKED:
                return 'Not checked';
                break;
            default:
                return 'Unknown';
                break;
        }
    }
}
