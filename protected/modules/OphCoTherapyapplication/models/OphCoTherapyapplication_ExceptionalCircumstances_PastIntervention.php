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
 * This is the model class for table "ophcotherapya_exceptional_interventions".
 *
 * The followings are the available columns in table:
 *
 * @property string $id
 * @property int $exceptional_id
 * @property int $exceptional_side_id
 * @property date $start_date
 * @property date $end_date
 * @property int $treatment_id
 * @property int $relevanttreatment_id
 * @property string $relevanttreatment_other
 * @property string $start_va
 * @property string $end_va
 * @property bool $is_relevant
 * @property int $stopreason_id
 * @property string $stopreason_other
 * @property string $comments
 *
 * The followings are the available model relations:
 * @property Element_OphCoTherapyapplication_ExceptionalCircumstances $exceptionalcircumstances
 * @property OphCoTherapyapplication_Treatment $treatment
 * @property OphCoTherapyapplication_RelevantTreatment $relevanttreatment
 * @property OphCoTherapyapplication_ExceptionalCircumstances_PastIntervention_StopReason $stop_reason
 */
class OphCoTherapyapplication_ExceptionalCircumstances_PastIntervention extends BaseActiveRecordVersioned
{
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
        return 'ophcotherapya_exceptional_pastintervention';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('start_date, end_date, treatment_id, relevanttreatment_id, relevanttreatment_other, start_va, end_va,
				stopreason_id, stopreason_other, comments, is_relevant, exceptional_side_id', 'safe'),
            array('start_date, end_date, start_va, end_va, stopreason_id', 'required'),
            array('treatment_id', 'requiredDependingOnTreatmentType', 'relevant' => false),
            array('relevanttreatment_id', 'requiredDependingOnTreatmentType', 'relevant' => true),
            array('relevanttreatment_other', 'requiredIfRelevantTreatmentIsOther'),
            array('stopreason_other', 'requiredIfStopReasonIsOther'),
            array('start_date, end_date', 'date', 'format' => 'yyyy-MM-dd'),
            array('start_date', 'validateEarlierOrEqualDate', 'later_date' => 'end_date'),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, start_date, end_date, treatment_id, start_va, end_va, stopreason_id, stopreason_other, comments', 'safe', 'on' => 'search'),
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
            'exceptionalcircumstances' => array(self::BELONGS_TO, 'Element_OphCoTherapyapplication_ExceptionalCircumstances', 'circumstances_id'),
            'treatment' => array(self::BELONGS_TO, 'OphCoTherapyapplication_Treatment', 'treatment_id'),
            'relevanttreatment' => array(self::BELONGS_TO, 'OphCoTherapyapplication_RelevantTreatment', 'relevanttreatment_id'),
            'stopreason' => array(self::BELONGS_TO, 'OphCoTherapyapplication_ExceptionalCircumstances_PastIntervention_StopReason', 'stopreason_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'start_date' => 'Start date',
            'end_date' => 'End date',
            'treatment_id' => 'Treatment',
            'relevanttreatment_id' => 'Treatment',
            'relevanttreatment_other' => 'Please provide treatment name',
            'start_va' => 'Pre treatment VA',
            'end_va' => 'Post treatment VA',
            'stopreason_id' => 'Reason for stopping',
            'stopreason_other' => 'Please describe the reason for stopping',
            'comments' => 'Comments',
        );
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     *
     * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
     */
    public function search()
    {
        $criteria = new CDbCriteria();

        $criteria->compare('id', $this->id, true);
        $criteria->compare('start_date', $this->start_date, true);
        $criteria->compare('end_date', $this->end_date, true);
        $criteria->compare('treatment_id', $this->treatment_id, true);
        $criteria->compare('relevanttreatment_id', $this->relevanttreatment_id, true);
        $criteria->compare('relevanttreatment_other', $this->relevanttreatment_other, true);
        $criteria->compare('start_va', $this->start_va, true);
        $criteria->compare('end_va', $this->end_va, true);
        $criteria->compare('stopreason_id', $this->stopreason_id, true);
        $criteria->compare('stopreason_other', $this->stopreason_other, true);
        $criteria->compare('comments', $this->comments, true);

        return new CActiveDataProvider(get_class($this), array(
                'criteria' => $criteria,
            ));
    }

    /**
     * Set default values for forms on create.
     */
    public function setDefaultOptions(Patient $patient = null)
    {
        $this->start_date = date(Helper::NHS_DATE_FORMAT);
        $this->end_date = date(Helper::NHS_DATE_FORMAT);
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

    // internal store of valid va values that can be selected for start and end VA
    protected $_va_list = null;

    /**
     * gets the valid VA values for use in a form.
     *
     * @return array key, value pair list
     */
    public function getVaOptions()
    {
        if (is_null($this->_va_list)) {
            $va_list = OphCoTherapyapplication_Helper::getInstance()->getVaListForForm();
            if (!$this->isNewRecord) {
                $start_seen = false;
                $end_seen = false;
                foreach ($va_list as $key => $val) {
                    if ($this->start_va == $key) {
                        $start_seen = true;
                    }
                    if ($this->end_va == $key) {
                        $end_seen = true;
                    }
                }
                if (!$start_seen) {
                    $va_list[] = array($this->start_va => $this->start_va);
                }
                if (!$end_seen) {
                    $va_list[] = array($this->end_va => $this->end_va);
                }
            }
            $this->_va_list = $va_list;
        }

        return $this->_va_list;
    }

    /**
     * get the treatment options for this intervention.
     *
     * @return array $options key,value pair list
     */
    public function getTreatmentOptions($selected_id)
    {
        if ($this->is_relevant) {
            return OphCoTherapyapplication_RelevantTreatment::model()->activeOrPk($selected_id)->findAll();
        } else {
            return OphCoTherapyapplication_Treatment::model()->availableOrPk($selected_id)->findAll();
        }
    }

    /**
     * validate the right type of treatment is set on the model depending on the treatment type.
     *
     * @param string $attribute
     * @param array  $params    - must include boolean flag for key of relevant
     */
    public function requiredDependingOnTreatmentType($attribute, $params)
    {
        if ($this->is_relevant == $params['relevant'] && $this->$attribute == null) {
            $this->addError($attribute, $this->getAttributeLabel($attribute).' is required');
        }
    }

    /**
     * validate that a reason is given if the stop reason select is of type other.
     *
     * @param string $attribute
     * @param array  $params
     */
    public function requiredIfStopReasonIsOther($attribute, $params)
    {
        if ($this->stopreason && $this->stopreason->other && $this->$attribute == null) {
            $this->addError($attribute, $this->getAttributeLabel($attribute).' is required when stop reason is set to '.$this->stopreason->name);
        }
    }

    /**
     * validate that a treatment is given if the treatment is 'other'.
     *
     * @param string $attribute
     * @param array  $params
     */
    public function requiredIfRelevantTreatmentIsOther($attribute, $params)
    {
        if ($this->relevanttreatment && $this->relevanttreatment->other && $this->$attribute == null) {
            $this->addError($attribute, $this->getAttributeLabel($attribute).' is required when stop reason is set to '.$this->stopreason->name);
        }
    }

    /**
     * validate a date is earlier or equal to another.
     *
     * @param $attribute - the element attribute that must be an earlier date
     * @param $params - 'later_date' is the attribute to compare it with
     */
    public function validateEarlierOrEqualDate($attribute, $params)
    {
        $later_date = $params['later_date'];
        if ($this->$attribute && $this->$later_date
            && $this->$attribute > $this->$later_date
        ) {
            $this->addError($attribute, $this->getAttributeLabel($attribute).' must be equal to or before '.$this->getAttributeLabel($later_date));
        }
    }

    /**
     * get the treatment name for this past intervention.
     *
     * @return string
     */
    public function getTreatmentName()
    {
        if ($this->is_relevant) {
            if ($this->relevanttreatment->other) {
                return $this->relevanttreatment_other;
            } else {
                return $this->relevanttreatment->name;
            }
        } else {
            return $this->treatment->drug->name;
        }
    }

    /**
     * get the text for the stopping reason for this treatment.
     *
     * @return string
     */
    public function getStopReasonText()
    {
        if ($this->stopreason) {
            if ($this->stopreason->other) {
                return $this->stopreason_other;
            } else {
                return $this->stopreason->name;
            }
        }
    }
}
