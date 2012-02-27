<?php
/**
 * OpenEyes
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2012
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2008-2011, Moorfields Eye Hospital NHS Foundation Trust
 * @copyright Copyright (c) 2011-2012, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */

/**
 * This is the model class for table "session".
 *
 * The followings are the available columns in table 'session':
 * @property string $id
 * @property string $sequence_id
 * @property string $date
 * @property string $start_time
 * @property string $end_time
 * @property string $comments
 * @property integer $status
 * @property boolean $consultant
 * @property boolean $paediatric
 * @property boolean $anaesthetist
 *
 * The followings are the available model relations:
 * @property Booking[] $bookings
 * @property Sequence $sequence
 */
class Session extends BaseActiveRecord
{
	const STATUS_AVAILABLE = 0;
	const STATUS_UNAVAILABLE = 1;

	public $firm_id;
	public $site_id;
	public $weekday;

	/**
	 * Returns the static model of the specified AR class.
	 * @return Session the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'session';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('sequence_id, date, start_time, end_time', 'required'),
			array('sequence_id', 'length', 'max'=>10),
			array('comments, status, consultant, paediatric, anaesthetist', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, sequence_id, date, start_time, end_time, comments, status, firm_id, site_id, weekday', 'safe', 'on'=>'search'),
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
			'bookings' => array(self::HAS_MANY, 'Booking', 'session_id'),
			'sequence' => array(self::BELONGS_TO, 'Sequence', 'sequence_id'),
			'bookingCount' => array(self::STAT, 'Booking', 'session_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'sequence_id' => 'Sequence',
			'date' => 'Date',
			'start_time' => 'Start Time',
			'end_time' => 'End Time',
			'comments' => 'Comments',
			'status' => 'Status',
			'anaesthetist' => 'Anaesthetist present',
			'consultant' => 'Consultant present',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id,true);
		$criteria->compare('t.sequence_id',$this->sequence_id,true);
		$criteria->compare('date',$this->date,true);
		$criteria->compare('start_time',$this->start_time,true);
		$criteria->compare('end_time',$this->end_time,true);
		$criteria->compare('comments',$this->comments,true);
		$criteria->with = array();
		if ($this->firm_id) {
			$criteria->together = true;
			$criteria->with[] = 'sequence';
			$criteria->with[] = 'sequence.sequenceFirmAssignment';
			$criteria->compare('sequenceFirmAssignment.firm_id', (int)$this->firm_id);
		}
		if ($this->site_id) {
			$criteria->together = true;
			$criteria->with[] = 'sequence';
			$criteria->with[] = 'sequence.theatre';
			$criteria->compare('theatre.site_id', $this->site_id);
		}
		if ($this->weekday) {
			$criteria->together = true;
			$criteria->with[] = 'sequence';
			$criteria->compare('sequence.weekday', $this->weekday);
		}

		return new CActiveDataProvider(get_class($this), array(
			'criteria'=>$criteria,
		));
	}

	public function getSiteListByFirm($firmId)
	{
		$sites = Yii::app()->db->createCommand()
			->select('site.id, site.short_name')
			->from('site')
			->join('theatre t', 'site.id = t.site_id')
			->join('sequence s', 's.theatre_id = t.id')
			->join('sequence_firm_assignment sfa', 'sfa.sequence_id = s.id')
			->where('sfa.firm_id = :id', array(':id'=>$firmId))
			->order('site.name ASC')
			->queryAll();

		$data = array();

		foreach ($sites as $site) {
			$data[$site['id']] = $site['short_name'];
		}

		return $data;
	}

	public function getFirmName()
	{
		$sequence = $this->sequence;
		if (!empty($sequence->sequenceFirmAssignment)) {
			$name = $sequence->sequenceFirmAssignment->firm->name;
		} else {
			$name = 'None';
		}

		return $name;
	}

	public function getAssociatedBookings()
	{
		$results = Yii::app()->db->createCommand()
			->select('COUNT(b.id) AS bookings_count')
			->from('session s')
			->join('booking b', 's.id = b.session_id')
			->where('s.id = :id' , array(':id' => $this->id))
			->queryRow();

		return $results['bookings_count'];
	}

	public function getStatusOptions()
	{
		return array(
			self::STATUS_AVAILABLE => 'Available',
			self::STATUS_UNAVAILABLE => 'Unavailable'
		);
	}

	public function getStatusText()
	{
		switch($this->status) {
			case self::STATUS_UNAVAILABLE:
				$text = 'Unavailable';
				break;
			default:
				$text = 'Available';
				break;
		}

		return $text;
	}
}
