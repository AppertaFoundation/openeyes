<?php
/*
_____________________________________________________________________________
(C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
(C) OpenEyes Foundation, 2011
This file is part of OpenEyes.
OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
_____________________________________________________________________________
http://www.openeyes.org.uk   info@openeyes.org.uk
--
*/

/**
 * This is the model class for table "episode".
 *
 * The followings are the available columns in table 'episode':
 * @property string $id
 * @property string $patient_id
 * @property string $firm_id
 * @property string $start_date
 * @property string $end_date
 *
 * The followings are the available model relations:
 * @property Patient $patient
 * @property Firm $firm
 * @property Event[] $events
 */
class Episode extends BaseActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return Episode the static model class
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
		return 'episode';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('patient_id', 'required'),
			array('patient_id, firm_id', 'length', 'max'=>10),
			array('end_date', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, patient_id, firm_id, start_date, end_date', 'safe', 'on'=>'search'),
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
			'patient' => array(self::BELONGS_TO, 'Patient', 'patient_id'),
			'firm' => array(self::BELONGS_TO, 'Firm', 'firm_id'),
			'events' => array(self::HAS_MANY, 'Event', 'episode_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'patient_id' => 'Patient',
			'firm_id' => 'Firm',
			'start_date' => 'Start Date',
			'end_date' => 'End Date',
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
		$criteria->compare('patient_id',$this->patient_id,true);
		$criteria->compare('firm_id',$this->firm_id,true);
		$criteria->compare('start_date',$this->start_date,true);
		$criteria->compare('end_date',$this->end_date,true);

		return new CActiveDataProvider(get_class($this), array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns true if an event of the given type exists within this episode
	 */
	public function hasEventOfType($eventTypeId, $currentEvent = null)
	{
		foreach ($this->events as $event) {
			if ($event->event_type_id == $eventTypeId) {
				if (!$currentEvent || $currentEvent->id != $event->id) {
					return true;
				}
			}
		}
		return false;
	}

	/**
	 * Returns the episode for a patient and specialty if there is one.
	 *
	 * @param integer $specialtyId	    id of the specialty
	 * @param integer $patientId	    id of the patient
	 *
	 * @return object $episode if found, null otherwise
	 */
	public function getBySpecialtyAndPatient($specialtyId, $patientId, $onlyReturnOpen = true)
	{
		$criteria = new CDbCriteria;
		$criteria->join = 'LEFT JOIN firm ON t.firm_id = firm.id
			LEFT JOIN service_specialty_assignment serviceSpecialtyAssignment ON
				serviceSpecialtyAssignment.id = firm.service_specialty_assignment_id
			LEFT JOIN patient ON t.patient_id = patient.id';
		$criteria->addCondition('serviceSpecialtyAssignment.specialty_id = :specialty_id');
		$criteria->addCondition('patient.id = :patient_id');
		if ($onlyReturnOpen) {
			$criteria->addCondition('t.end_date IS NULL');
		}
		$criteria->params = array(
			':specialty_id' => $specialtyId,
			':patient_id' => $patientId
		);

		return Episode::model()->find($criteria);
	}

	/**
	 * Return the eye of the most recent diagnosis for the episode
	 *
	 * return @string
	 */
	public function getPrincipalDiagnosis()
	{
		$result = Yii::app()->db->createCommand()
			->select('ed.id AS id')
			->from('element_diagnosis ed')
			->join('event ev', 'ed.event_id = ev.id')
			->join('episode ep', 'ev.episode_id = ep.id')
			->where('ep.id = :ep_id', array(
				':ep_id' => $this->id
			))
			->order('ed.id DESC')
			->queryRow();

		if (empty($result)) {
			return null;
		} else {
			return ElementDiagnosis::model()->findByPk($result['id']);
		}
	}

	public function getPrincipalDiagnosisEyeText() {
		if ($diagnosis = $this->getPrincipalDiagnosis()) {
			return $diagnosis->getEyeText();
		} else {
			return 'none';
		}
	}

        public function getPrincipalDiagnosisDisorderTerm() {
                if ($diagnosis = $this->getPrincipalDiagnosis()) {
                        return $diagnosis->disorder->term;
                } else {
                        return 'none';
                }
        }

	public static function getCurrentEpisodeByFirm($patientId, $firm)
	{
		// Check for an open episode for this patient and firm's service with a referral
		$episode = Yii::app()->db->createCommand()
			->select('e.id AS eid')
			->from('episode e')
			->join('firm f', 'e.firm_id = f.id')
			->join('service_specialty_assignment s_s_a', 'f.service_specialty_assignment_id = s_s_a.id')
			->where('e.end_date IS NULL AND e.patient_id = :patient_id AND s_s_a.specialty_id = :specialty_id', array(
				':patient_id' => $patientId, ':specialty_id' => $firm->serviceSpecialtyAssignment->specialty_id
			))
			->queryRow();

		if (!$episode['eid']) {
			// There is an open episode and it has a referral, no action required
			return null;
		}

		return Episode::model()->findByPk($episode['eid']);
	}
}

