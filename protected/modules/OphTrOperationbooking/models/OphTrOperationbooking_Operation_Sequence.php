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
 * This is the model class for table "ophtroperationbooking_operation_sequence".
 *
 * The followings are the available columns in table:
 *
 * @property int $id
 * @property int $theatre_id
 * @property string $start_date
 * @property string $start_time
 * @property string $end_time
 * @property string $end_date
 * @property string $default_admission_time
 * @property tinyint $max_procedures
 * @property tinyint $max_complex_bookings
 *
 * The followings are the available model relations:
 * @property Site $site
 * @property OphTrOperationbooking_Operation_Theatre $theatre
 */
class OphTrOperationbooking_Operation_Sequence extends BaseActiveRecordVersioned
{
    const SELECT_1STWEEK = 1;
    const SELECT_2NDWEEK = 2;
    const SELECT_3RDWEEK = 4;
    const SELECT_4THWEEK = 8;
    const SELECT_5THWEEK = 16;

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
        return 'ophtroperationbooking_operation_sequence';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('theatre_id, start_date, start_time, end_time, interval_id', 'required'),
            array('end_date, week_selection, consultant, paediatric, anaesthetist, general_anaesthetic, firm_id, theatre_id, start_date, start_time, end_time, interval_id, weekday, default_admission_time', 'safe'),
            array('start_date', 'date', 'format' => 'yyyy-MM-dd'),
            array('start_time', 'date', 'format' => array('H:mm', 'H:mm:ss')),
            array('end_time', 'date', 'format' => array('H:mm', 'H:mm:ss')),
            array('end_date', 'checkDates'),
            array('end_time', 'checkTimes'),
            array('start_date', 'compareStartdateWithWeekday'),
            array('max_procedures', 'numerical', 'integerOnly' => true, 'min' => 1, 'max' => 127),
            array('max_complex_bookings', 'numerical', 'integerOnly' => true, 'min' => 0, 'max' => 127),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, theatre_id, start_date, start_time, end_time, end_date, consultant, paediatric, anaesthetist, interval_id, weekday, week_selection, firm_id, site_id', 'safe', 'on' => 'search'),
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
            'theatre' => array(self::BELONGS_TO, 'OphTrOperationbooking_Operation_Theatre', 'theatre_id'),
            'firmAssignment' => array(self::HAS_ONE, 'SequenceFirmAssignment', 'sequence_id'),
            'firm' => array(self::BELONGS_TO, 'Firm', 'firm_id'),
            'institution' => array(self::BELONGS_TO, 'Institution', 'institution_id'),
            'sessions' => array(self::HAS_MANY, 'OphTrOperationbooking_Operation_Session', 'sequence_id'),
            'interval' => array(self::BELONGS_TO, 'OphTrOperationbooking_Operation_Sequence_Interval', 'interval_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'firm_id' => Firm::contextLabel(),
            'theatre_id' => 'Theatre',
            'start_date' => 'Start date',
            'end_date' => 'End date',
            'start_time' => 'Start time',
            'end_time' => 'End time',
            'interval_id' => 'Interval',
            'general_anaesthetic' => 'General anaesthetic',
            'default_admission_time' => 'Default admission time',
            'max_procedures' => 'Max procedures',
            'max_complex_bookings' => 'Max complex bookings',
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
        $criteria->compare('name', $this->name, true);

        return new CActiveDataProvider(get_class($this), array(
                'criteria' => $criteria,
            ));
    }

    public function checkDates()
    {
        if (!empty($this->end_date)) {
            $start = strtotime($this->start_date);
            $end = strtotime($this->end_date);

            if ($end < $start) {
                $this->addError('end_date', 'End date must be after the start date.');
            }
        }
    }

    public function checkTimes()
    {
        $start = strtotime($this->start_time);
        $end = strtotime($this->end_time);

        if ($end <= $start) {
            $this->addError('end_time', 'End time must be after the start time.');
        }
    }

    public function compareStartdateWithWeekday()
    {
        try {
            $start_date = new DateTime($this->start_date);
        } catch (Exception $e) {
            $this->addError('start_date', 'Start date format error');
        }

        if ($this->weekday != $start_date->format('N')) {
            $this->addError('start_date', 'Start date and weekday must be on the same day of the week');
        }
    }

    public function getWeekOccurrences($weekday, $weekSelection, $startTimestamp, $endTimestamp, $startDate, $endDate)
    {
        $dates = array();
        $month = strtotime(date('Y-m-01', $startTimestamp));
        $weekday_options = $this->getWeekdayOptions();
        $weekday_string = $weekday_options[$weekday];
        while ($month <= $endTimestamp) {
            $day = strtotime("first $weekday_string of", $month);
            for ($i = self::SELECT_1STWEEK; $i <= self::SELECT_5THWEEK; $i *= 2) {
                // Only add date if it is between start and end dates, and is a selected week. Also check we haven't rolled over into the next month (4 week months)
                if ($day >= $startTimestamp && $day <= $endTimestamp && $day <= strtotime('last day of', $month) && ($weekSelection & $i)) {
                    $dates[] = date('Y-m-d', $day);
                }
                $day = strtotime('+1 week', $day);
            }
            $month = strtotime('+1 month', $month);
        }

        return $dates;
    }

    public function getWeekdayOptions()
    {
        return array(
            1 => 'Monday',
            2 => 'Tuesday',
            3 => 'Wednesday',
            4 => 'Thursday',
            5 => 'Friday',
            6 => 'Saturday',
            7 => 'Sunday',
        );
    }

    public function getDates()
    {
        if ($this->end_date) {
            return $this->NHSDate('start_date').' - '.$this->NHSDate('end_date');
        }

        return $this->NHSDate('start_date').' onwards';
    }

    public function getWeekdayText()
    {
        $options = $this->weekdayOptions;

        return isset($options[$this->weekday]) ? $options[$this->weekday] : 'None';
    }

    protected function beforeValidate()
    {
        if ($this->start_date && !preg_match('/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/', $this->start_date)) {
            $this->start_date = date('Y-m-d', strtotime($this->start_date));
        }
        if ($this->end_date && !preg_match('/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/', $this->end_date)) {
            $this->end_date = date('Y-m-d', strtotime($this->end_date));
        }

        // Verify that this session doesn't conflict with any other sequences or sessions
        $criteria = new CDbCriteria();
        if ($this->id) {
            $criteria->addCondition('id <> :id');
            $criteria->params[':id'] = $this->id;
        }

        $criteria->addCondition('theatre_id = :theatre_id');
        $criteria->params[':theatre_id'] = $this->theatre_id;

        $criteria->addCondition('weekday = :weekday');
        $criteria->params[':weekday'] = $this->weekday;

        $criteria->addCondition('end_date is null or end_date >= :start_date');
        $criteria->params[':start_date'] = $this->start_date;

        $dateList = $this->getDateListForMonths(12);

        $conflicts = array();

        foreach (self::model()->findAll($criteria) as $sequence) {
            $s_dateList = $sequence->getDateListForMonths(12);

            foreach ($s_dateList as $date) {
                if (in_array($date, $dateList)) {
                    $start = strtotime("$date $this->start_time");
                    $end = strtotime("$date $this->end_time");

                    $s_start = strtotime("$date $sequence->start_time");
                    $s_end = strtotime("$date $sequence->end_time");

                    if ($start < $s_end && $start >= $s_start) {
                        if (!isset($conflicts[$sequence->id]['start_time'])) {
                            $this->addError('start_time', "This start time conflicts with sequence $sequence->id");
                            $conflicts[$sequence->id]['start_time'] = 1;
                        }
                    }

                    if ($end > $s_start && $end <= $s_end) {
                        if (!isset($conflicts[$sequence->id]['end_time'])) {
                            $this->addError('end_time', "This end time conflicts with sequence $sequence->id");
                            $conflicts[$sequence->id]['end_time'] = 1;
                        }
                    }
                    if ($start < $s_start && $end > $s_end) {
                        if (!isset($conflicts[$sequence->id]['end_time']) || !isset($conflicts[$sequence->id]['start_time'])) {
                            $this->addError('start_time', "This start time conflicts with sequence $sequence->id");
                            $conflicts[$sequence->id]['start_time'] = 1;
                            $this->addError('end_time', "This end time conflicts with sequence $sequence->id");
                            $conflicts[$sequence->id]['end_time'] = 1;
                        }
                    }
                }
            }
        }

        $criteria = new CDbCriteria();

        $criteria->addCondition('sequence_id <> :sequence_id or sequence_id is null');
        $criteria->params[':sequence_id'] = $this->id;

        $criteria->addCondition('theatre_id = :theatre_id');
        $criteria->params[':theatre_id'] = $this->theatre_id;

        $criteria->addInCondition('date', $dateList);

        $conflicts = array();
        foreach (OphTrOperationbooking_Operation_Session::model()->findAll($criteria) as $session) {
            $start = strtotime("$session->date $this->start_time");
            $end = strtotime("$session->date $this->end_time");

            $s_start = strtotime("$session->date $session->start_time");
            $s_end = strtotime("$session->date $session->end_time");

            if ($start < $s_end && $start >= $s_start) {
                if (!isset($conflicts[$session->id]['start_time'])) {
                    $this->addError('start_time', "This start time conflicts with session $session->id");
                    $conflicts[$session->id]['start_time'] = 1;
                }
            }

            if ($end > $s_start && $end <= $s_end) {
                if (!isset($conflicts[$session->id]['end_time'])) {
                    $this->addError('end_time', "This end time conflicts with session $session->id");
                    $conflicts[$session->id]['end_time'] = 1;
                }
            }

            if ($start < $s_start && $end > $s_end) {
                if (!isset($conflicts[$session->id]['end_time']) || !isset($conflicts[$session->id]['start_time'])) {
                    $this->addError('start_time', "This start time conflicts with session $session->id");
                    $conflicts[$session->id]['start_time'] = 1;
                    $this->addError('end_time', "This end time conflicts with session $session->id");
                    $conflicts[$session->id]['end_time'] = 1;
                }
            }
        }

        return parent::beforeValidate();
    }

    public function getDateListForMonths($num_months)
    {
        $initialEndDate = strtotime('+'.$num_months.' months');

        $startDate = strtotime($this->start_date);

        if ($this->end_date && strtotime($this->end_date) < $initialEndDate) {
            $endDate = strtotime($this->end_date);
        } else {
            $endDate = $initialEndDate;
        }

        $dateList = array();
        if ($this->interval_id == 1) {
            $dateList[] = $this->start_date;
        } elseif ($this->interval_id == 6 && $this->week_selection) {
            $date = date('Y-m-d', $startDate);
            $time = $startDate;

            while (date('N', $time) != date('N', strtotime($this->start_date))) {
                $date = date('Y-m-d', mktime(0, 0, 0, date('m', $time), date('d', $time) + 1, date('Y', $time)));
                $time = strtotime($date);
            }
            $dateList = $this->getWeekOccurrences($this->weekday, $this->week_selection, $time, $endDate, $date, date('Y-m-d', $endDate));
        } else {
            $interval = $this->interval->getInteger($endDate);

            $days = $interval / 24 / 60 / 60;

            $nextStartDate = $startDate;

            $date = date('Y-m-d', $nextStartDate);

            $time = $nextStartDate;

            while (date('N', $time) != date('N', strtotime($this->start_date))) {
                $date = date('Y-m-d', mktime(0, 0, 0, date('m', $time), date('d', $time) + 1, date('Y', $time)));
                $time = strtotime($date);
            }

            while ($time <= $endDate) {
                $dateList[] = $date;

                $date = date('Y-m-d', mktime(0, 0, 0, date('m', $time), date('d', $time) + $days, date('Y', $time)));
                $time = strtotime($date);
            }
        }

        return $dateList;
    }

    protected function beforeSave()
    {
        if ($this->start_date && !preg_match('/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/', $this->start_date)) {
            $this->start_date = date('Y-m-d', strtotime($this->start_date));
        }
        if ($this->end_date && !preg_match('/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/', $this->end_date)) {
            $this->end_date = date('Y-m-d', strtotime($this->end_date));
        }

        $this->default_admission_time = $this->setDefaultAdmissionTime($this->default_admission_time, $this->start_time);

        return parent::beforeSave();
    }

    /**
     * Gets all sequences for theatres with a site bound to the current institution.
     */
    public static function getSequencesForCurrentInstitution()
    {
        $site_id_list = array_map(
            static function ($site) {
                return $site->id;
            },
            Institution::model()->getCurrent()->sites
        );
        return self::model()->with('theatre')->findAll('theatre.site_id IN (' . implode(', ', $site_id_list) . ')');
    }
}
