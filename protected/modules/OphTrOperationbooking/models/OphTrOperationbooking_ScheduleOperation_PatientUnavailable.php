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
class OphTrOperationbooking_ScheduleOperation_PatientUnavailable extends BaseActiveRecordVersioned
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
        return 'ophtroperationbooking_scheduleope_patientunavail';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('start_date, end_date, reason_id', 'safe'),
            array('start_date, end_date, reason_id', 'required'),
            array('start_date', 'validateEarlierOrEqualDate', 'later_date' => 'end_date'),
            array('reason_id', 'validateReasonIsEnabled',
                    'model' => 'OphTrOperationbooking_ScheduleOperation_PatientUnavailableReason', 'on' => 'insert', ),
            array('start_date, end_date', 'dateFormatValidator'),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, start_date, end_date, reason', 'safe', 'on' => 'search'),
        );
    }

    //@TODO: move this to some Helper calss
    public function dateFormatValidator($attribute, $params)
    {
        $format_check = preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $this->$attribute);
        $date_format = DateTime::createFromFormat('Y-m-d', $this->$attribute);

        if ( ($date_format && ($date_format->format('Y-m-d') !== $this->$attribute)) || !$format_check) {
            if (!$this->hasErrors($attribute)) {
                $this->addError($attribute, $this->getAttributeLabel($attribute) . ': Wrong date format.');
            }
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
                'element' => array(self::BELONGS_TO, 'Element_OphTrOperationbooking_ScheduleOperation', 'element_id'),
                'reason' => array(self::BELONGS_TO, 'OphTrOperationbooking_ScheduleOperation_PatientUnavailableReason', 'reason_id'),
                'user' => array(self::BELONGS_TO, 'User', 'created_user_id'),
                'usermodified' => array(self::BELONGS_TO, 'User', 'last_modified_user_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
                'reason_id' => 'Reason',
                'start_date' => 'Start Date',
                'end_date' => 'End Date',
        );
    }

    /**
     * Retrieves all valid OphTrOperationBooking_ScheduleOperation_PatientUnavailableReason that can be used for this
     * instance (i.e. includes the current value even if its no longer active).
     *
     * @return OphTrOperationBooking_ScheduleOperation_PatientUnavailableReason[]
     */
    public function getPatientUnavailbleReasons()
    {
        $criteria = new CDbCriteria();
        $criteria->order = 'display_order asc';
        $criteria->join = 'join ophtroperationbooking_patientunavailreason_institution inst on inst.patientunavailreason_id = t.id';
        $criteria->addCondition('institution_id = :institution_id');
        $criteria->params[':institution_id'] = Institution::model()->getCurrent()->id;

        $reasons = OphTrOperationbooking_ScheduleOperation_PatientUnavailableReason::model()->findAll($criteria);
        // just use standard list
        if (!$this->reason_id) {
            return $reasons;
        }

        $all_reasons = array();
        $r_ids = array();

        foreach ($reasons as $reason) {
            $all_reasons[] = $reason;
            $r_ids[] = $reason->id;
        }

        if (!in_array($this->reason_id, $r_ids)) {
            $all_reasons[] = $this->reason;
        }

        return $all_reasons;
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
     * validate the related object for $attribute is enabled.
     *
     * @param $attribute
     * @param $params - 'model' is the model that the $attribute is the id for
     * @throws Exception
     */
    public function validateReasonIsEnabled($attribute, $params)
    {
        $model = $params['model'];
        if ($this->$attribute) {
            $obj = $model::model()->findByPk($this->$attribute);
            if (!$obj->hasMapping(ReferenceData::LEVEL_INSTITUTION, Institution::model()->getCurrent()->id)) {
                $this->addError($attribute, $this->getAttributeLabel($attribute).' must be active for current institution for new entry');
            }
        }
    }
}
