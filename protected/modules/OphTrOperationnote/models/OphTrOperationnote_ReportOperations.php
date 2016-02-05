<?php /**
 * OpenEyes
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2008-2011, Moorfields Eye Hospital NHS Foundation Trust
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */

class OphTrOperationnote_ReportOperations extends BaseReport
{
	public $surgeon_id;
	public $Procedures_procs;
	public $complications;
	public $date_from;
	public $date_to;
	public $bookingcomments;
	public $booking_diagnosis;
	public $surgerydate;
	public $theatre;
	public $comorbidities;
	public $first_eye;
	public $refraction_values;
	public $target_refraction;
	public $va_values;
	public $cataract_report;
	public $cataract_predicted_refraction;
	public $cataract_iol_type;
	public $cataract_iol_power;
	public $tamponade_used;
	public $anaesthetic_type;
	public $anaesthetic_delivery;
	public $anaesthetic_complications;
	public $anaesthetic_comments;
	public $surgeon;
	public $surgeon_role;
	public $assistant;
	public $assistant_role;
	public $supervising_surgeon;
	public $supervising_surgeon_role;
	public $opnote_comments;
	public $patient_oph_diagnoses;
	public $operations;

	public function attributeNames()
	{
		return array(
			'surgeon_id',
			'Procedures_procs',
			'complications',
			'date_from',
			'date_to',
			'bookingcomments',
			'booking_diagnosis',
			'surgerydate',
			'theatre',
			'comorbidities',
			'first_eye',
			'refraction_values',
			'target_refraction',
			'va_values',
			'cataract_report',
			'tamponade_used',
			'anaesthetic_type',
			'anaesthetic_delivery',
			'anaesthetic_complications',
			'anaesthetic_comments',
			'surgeon',
			'surgeon_role',
			'assistant',
			'assistant_role',
			'supervising_surgeon',
			'supervising_surgeon_role',
			'opnote_comments',
			'patient_oph_diagnoses',
		);
	}

	public function attributeLabels()
	{
		return array(
			'surgeon_id' => 'Surgeon',
			'Procedures_procs' => 'Procedures',
			'complications' => 'Cataract complications',
			'date_from' => 'Date from',
			'date_to' => 'Date to',
			'bookingcomments' => 'Booking comments',
			'booking_diagnosis' => 'Operation booking diagnosis',
			'surgerydate' => 'Surgery date',
			'theatre' => 'Theatre',
			'comorbidities' => 'Comorbidities',
			'first_eye' => 'First or second eye',
			'refraction_values' => 'Refraction values',
			'target_refraction' => 'Target refraction',
			'va_values' => 'VA values',
			'cataract_report' => 'Cataract report',
			'tamponade_used' => 'Tamponade used',
			'anaesthetic_type' => 'Anaesthetic type',
			'anaesthetic_delivery' => 'Anaesthetic delivery',
			'anaesthetic_complications' => 'Anaesthetic complications',
			'anaesthetic_comments' => 'Anaesthetic comments',
			'surgeon' => 'Surgeon',
			'surgeon_role' => 'Surgeon role',
			'assistant' => 'Assistant',
			'assistant_role' => 'Assistant role',
			'supervising_surgeon' => 'Supervising surgeon',
			'supervising_surgeon_role' => 'Supervising surgeon role',
			'opnote_comments' => 'Operation note comments',
			'patient_oph_diagnoses' => 'Patient ophthalmic diagnoses',
		);
	}

	public function rules()
	{
		return array(
			array(implode(',',$this->attributeNames()),'safe'),
			array('date_from, date_to', 'required'),
		);
	}

	public function run()
	{
		$surgeon = null;
		$date_from = date('Y-m-d', strtotime("-1 year"));
		$date_to = date('Y-m-d');

		if ($this->surgeon_id) {
			$surgeon_id = (int)$this->surgeon_id;

			if (!$surgeon = User::model()->findByPk($surgeon_id)) {
				throw new CException("Unknown surgeon $surgeon_id");
			}
		}
		if ($this->date_from && strtotime($this->date_from)) {
			$date_from = date('Y-m-d', strtotime($this->date_from));
		}
		if ($this->date_to && strtotime($this->date_to)) {
			$date_to = date('Y-m-d', strtotime($this->date_to));
		}
		$filter_procedures = null;
		if ($this->Procedures_procs) {
			$filter_procedures = $this->Procedures_procs;
		}
		$filter_complications =  null;
		if ($this->complications) {
			$filter_complications = $this->complications;
		}

		// ensure we don't hit PAS
		Yii::app()->event->dispatch('start_batch_mode');

		$this->operations = $this->getOperations(
			$surgeon,
			$filter_procedures,
			$filter_complications,
			$date_from,
			$date_to,
			$this->patient_oph_diagnoses,
			$this->booking_diagnosis,
			$this->theatre,
			$this->bookingcomments,
			$this->surgerydate,
			$this->comorbidities,
			$this->target_refraction,
			$this->first_eye,
			$this->va_values,
			$this->refraction_values,
			$this->anaesthetic_type,
			$this->anaesthetic_delivery,
			$this->anaesthetic_comments,
			$this->anaesthetic_complications,
			$this->cataract_report,
			$this->cataract_predicted_refraction,
			$this->cataract_iol_type,
			$this->cataract_iol_power,
			$this->tamponade_used,
			$this->surgeon,
			$this->surgeon_role,
			$this->assistant,
			$this->assistant_role,
			$this->supervising_surgeon,
			$this->supervising_surgeon_role,
			$this->opnote_comments,
			$this->surgeon_id
		);

		Yii::app()->event->dispatch('end_batch_mode');
	}

	/**
	 * Generate operation report
	 * @param User $surgeon
	 * @param array $filter_procedures
	 * @param array $filter_complications
	 * @param $from_date
	 * @param $to_date
	 * @param array $appenders - list of methods to call with patient id and date to retrieve additional data for each row
	 * @return array
	 */
	protected function getOperations($surgeon = null, $filter_procedures = array(), $filter_complications = array(), $from_date, $to_date, $patient_oph_diagnoses, $booking_diagnosis, $theatre, $bookingcomments, $surgerydate, $comorbidities, $target_refraction, $first_eye, $va_values, $refraction_values, $anaesthetic_type, $anaesthetic_delivery, $anaesthetic_comments, $anaesthetic_complications, $cataract_report, $cataract_predicted_refraction, $cataract_iol_type, $cataract_iol_power, $tamponade_used, $surgeon, $surgeon_role, $assistant, $assistant_role, $supervising_surgeon, $supervising_surgeon_role, $opnote_comments, $surgeon_id)
	{
		$filter_procedures_method = 'OR';
		$filter_complications_method = 'OR';

		$command = Yii::app()->db->createCommand()
			->select(
				"e.id, c.first_name, c.last_name, e.created_date, su.surgeon_id, su.assistant_id, su.supervising_surgeon_id, p.hos_num,p.gender, p.dob, pl.id as plid, cat.id as cat_id, eye.name AS eye"
			)
			->from("event e")
			->join("episode ep", "e.episode_id = ep.id")
			->join("patient p", "ep.patient_id = p.id")
			->join("et_ophtroperationnote_procedurelist pl", "pl.event_id = e.id")
			->join("et_ophtroperationnote_surgeon su", "su.event_id = e.id")
			->join("contact c", "p.contact_id = c.id")
			->join("eye", "eye.id = pl.eye_id")
			->leftJoin("et_ophtroperationnote_cataract cat", "cat.event_id = e.id")
			->where("e.deleted = 0 and ep.deleted = 0 and e.created_date >= :from_date and e.created_date < :to_date + interval 1 day")
			->order("p.id, e.created_date asc");
		$params = array(':from_date' => $from_date, ':to_date' => $to_date);

		if ($surgeon) {
			$command->andWhere(
				"(su.surgeon_id = :user_id or su.assistant_id = :user_id or su.supervising_surgeon_id = :user_id)"
			);
			$params[':user_id'] = $surgeon_id;
		}

		$results = array();
		$cache = array();
		foreach ($command->queryAll(true, $params) as $row) {
			set_time_limit(1);
			$complications = array();
			if ($row['cat_id']) {
				foreach (OphTrOperationnote_CataractComplication::model()->findAll('cataract_id = ?', array($row['cat_id'])) as $complication) {
					if (!isset($cache['complications'][$complication->complication_id])) {
						$cache['complications'][$complication->complication_id] = $complication->complication->name;
					}
					$complications[(string)$complication->complication_id] = $cache['complications'][$complication->complication_id];
				}
			}

			$matched_complications = 0;
			if ($filter_complications) {
				foreach ($filter_complications as $filter_complication) {
					if (isset($complications[$filter_complication])) {
						$matched_complications++;
					}
				}
				if (($filter_complications_method == 'AND' && $matched_complications < count(
							$filter_complications
						)) || !$matched_complications
				) {
					continue;
				}
			}

			$procedures = array();
			foreach (OphTrOperationnote_ProcedureListProcedureAssignment::model()->findAll('procedurelist_id = ?', array($row['plid'])) as $pa) {
				if (!isset($cache['procedures'][$pa->proc_id])) {
					$cache['procedures'][$pa->proc_id] = $pa->procedure->term;
				}
				$procedures[(string)$pa->proc_id] = $cache['procedures'][$pa->proc_id];
			}
			$matched_procedures = 0;
			if ($filter_procedures) {
				foreach ($filter_procedures as $filter_procedure) {
					if (isset($procedures[$filter_procedure])) {
						$matched_procedures++;
					}
				}
				if (($filter_procedures_method == 'AND' && $matched_procedures < count(
							$filter_procedures
						)) || !$matched_procedures
				) {
					continue;
				}
			}

			$record = array(
				"operation_date" => date('j M Y', strtotime($row['created_date'])),
				"patient_hosnum" => $row['hos_num'],
				"patient_firstname" => $row['first_name'],
				"patient_surname" => $row['last_name'],
				"patient_gender" => $row['gender'],
				"patient_dob" => date('j M Y', strtotime($row['dob'])),
				"eye" => $row['eye'],
				"procedures" => implode(', ', $procedures),
				"complications" => implode(', ', $complications),
			);

			if ($surgeon) {
				if ($row['surgeon_id'] == $surgeon_id) {
					$record['surgeon_role'] = 'Surgeon';
				} else {
					if ($row['assistant_id'] == $surgeon_id) {
						$record['surgeon_role'] = 'Assistant surgeon';
					} else {
						if ($row['supervising_surgeon_id'] == $surgeon_id) {
							$record['surgeon_role'] = 'Supervising surgeon';
						}
					}
				}
			}

			//appenders
			$this->appendPatientValues($record, $row['id'], $patient_oph_diagnoses);
			$this->appendBookingValues($record, $row['id'], $booking_diagnosis, $theatre, $bookingcomments, $surgerydate);
			$this->appendOpNoteValues($record, $row['id'], $anaesthetic_type, $anaesthetic_delivery, $anaesthetic_comments, $anaesthetic_complications, $cataract_report, $cataract_predicted_refraction, $cataract_iol_type, $cataract_iol_power, $tamponade_used, $surgeon, $surgeon_role, $assistant, $assistant_role, $supervising_surgeon, $supervising_surgeon_role, $opnote_comments);
			$this->appendExaminationValues($record, $row['id'], $comorbidities, $target_refraction, $first_eye, $va_values, $refraction_values);

			$results[] = $record;
		}

		return $results;
	}

	protected function appendPatientValues(&$record, $event_id, $patient_oph_diagnoses)
	{
		$event = Event::model()->findByPk($event_id);
		$patient = $event->episode->patient;
		if ($patient_oph_diagnoses) {
			$diagnoses = array();
			foreach ($patient->episodes as $ep) {
				if ($ep->diagnosis) {
					$diagnoses[] = (($ep->eye) ? $ep->eye->adjective . " " : "") . $ep->diagnosis->term;
				}
			}
			foreach ($patient->getOphthalmicDiagnoses() as $sd) {
				$diagnoses[] = $sd->eye->adjective . " " . $sd->disorder->term;
			}
			$record['patient_diagnoses'] = implode(', ', $diagnoses);
		}
	}

	protected function appendBookingValues(&$record, $event_id, $booking_diagnosis, $theatre, $bookingcomments, $surgerydate)
	{
		if ($api = Yii::app()->moduleAPI->get('OphTrOperationbooking')) {
			$procedure = Element_OphTrOperationnote_ProcedureList::model()->find('event_id=:event_id',array(':event_id'=>$event_id));
			$bookingEventID = $procedure['booking_event_id'];
			foreach (array('booking_diagnosis', 'theatre', 'bookingcomments','surgerydate') as $k) {
				if (${$k}) {
					$record[$k] = '';
				}
			}
			if (isset($bookingEventID)) {
				$operationElement = $api->getOperationForEvent($bookingEventID);
				$latestBookingID = $operationElement['latest_booking_id'];
				$operationBooking = OphTrOperationbooking_Operation_Booking::model()->find('id=:id',array('id'=>$latestBookingID));

				if ($booking_diagnosis) {
					$diag_el = $operationElement->getDiagnosis();
					$disorder = $diag_el->disorder();
					if ($disorder) {
						$record['booking_diagnosis'] = $diag_el->eye->adjective  . " " . $disorder->term;
					} else {
						$record['booking_diagnosis'] = 'Unknown';
					}
				}

				if ($theatre) {
					$theatreName = $operationElement->site['name'].' '.$operationBooking->theatre['name'];
					$record['theatre'] = $theatreName;
				}

				if ($this->bookingcomments) {
					$record['bookingcomments'] = $operationElement['comments'];
				}

				if ($this->surgerydate) {
					$record['surgerydate'] = $operationBooking['session_date'];
				}
			}
		}
	}

	protected function appendExaminationValues(&$record, $event_id, $comorbidities, $target_refraction, $first_eye, $va_values, $refraction_values)
	{
		$event = Event::model()->with('episode')->findByPk($event_id);
			
		if ($api = Yii::app()->moduleAPI->get('OphCiExamination')) {

			$preOpCriteria = $this->preOperationNoteCriteria($event);
			$postOpCriteria = $this->postOperationNoteCriteria($event);

			if ($this->comorbidities) {
				$record['comorbidities'] = $this->getComorbidities($preOpCriteria);
			}

			if ($this->target_refraction) {
				$record['target_refraction'] = $this->getTargetRefraction($preOpCriteria);
			}

			if ($this->first_eye) {
				$record['first_or_second_eye'] = $this->getFirstEyeOrSecondEye($preOpCriteria);
			}

			if ($this->va_values) {
				$record['pre-op va'] = $this->getVaReading($preOpCriteria,$record);
				$record['most recent post-op va'] = $this->getVaReading($postOpCriteria,$record);
			}

			if ($this->refraction_values) {
				$record['pre-op refraction'] = $this->getRefractionReading($preOpCriteria,$record);
				$record['most recent post-op refraction'] = $this->getRefractionReading($postOpCriteria,$record);
			}
		}
	}

	protected function preOperationNoteCriteria($event)
	{
		return $this->operationNoteCriteria($event, true);
	}

	public function postOperationNoteCriteria($event)
	{
		return $this->operationNoteCriteria($event,false);
	}

	public function operationNoteCriteria($event, $searchBackwards)
	{
		$criteria = new CDbCriteria();
		if ($searchBackwards) {
			$criteria->addCondition('event.created_date < :op_date');
		}
		else {
			$criteria->addCondition('event.created_date > :op_date');
		}
		$criteria->addCondition('event.episode_id = :episode_id');
		$criteria->params[':episode_id'] = $event->episode_id;
		$criteria->params[':op_date'] = $event->created_date;
		$criteria->order = 'event.created_date desc';
		$criteria->limit = 1;
		return $criteria;
	}

	protected function eyesCondition($record)
	{
		if (strtolower($record['eye']) == 'left') {
			$eyes = array(Eye::LEFT,Eye::BOTH);
		}
		else {
			$eyes = array(Eye::RIGHT, Eye::BOTH);
		}
		return $eyes;
	}

	protected function getComorbidities($criteria)
	{
		$comorbiditiesElement = \OEModule\OphCiExamination\models\Element_OphCiExamination_Comorbidities::model()->with(array('event'))->find($criteria);

		$comorbidities = array();
		if (isset($comorbiditiesElement->items)) {
			foreach($comorbiditiesElement->items as $comorbiditity) {
				$comorbidities[] = $comorbiditity['name'];
			}
			return implode(',', $comorbidities);
		}
	}

	protected function getTargetRefraction($criteria)
	{
		$cataractManagementElement = \OEModule\OphCiExamination\models\Element_OphCiExamination_CataractSurgicalManagement::model()->with(array('event'))->find($criteria);

		if ($cataractManagementElement) {
			return $cataractManagementElement['target_postop_refraction'];
		}
	}

	public function getFirstEyeOrSecondEye($criteria)
	{
		$cataractManagementElement = \OEModule\OphCiExamination\models\Element_OphCiExamination_CataractSurgicalManagement::model()->with(array('event'))->find($criteria);

		if ($cataractManagementElement) {
			return $cataractManagementElement->eye['name'];
		}
	}

	public function getVAReading($criteria,$record)
	{
		$criteria->addInCondition('eye_id', $this->eyesCondition($record));
		$va = \OEModule\OphCiExamination\models\Element_OphCiExamination_VisualAcuity::model()->with(array('event'))->find($criteria);
		$reading = null;
		$sides = array(strtolower($record['eye']));
		if ($sides[0] == 'both') {
			$sides = array('left', 'right');
		}

		if ($va) {
			$res = '';
			foreach ($sides as $side) {
				$reading = $va->getBestReading($side);
				if ($res) {
					$res .= " ";
				}
				if ($reading) {
					$res .= ucfirst($side) . ": " . $reading->convertTo($reading->value, $va->unit_id) . ' (' . $reading->method->name . ')';
				}
				else {
					$res .= ucfirst($side) . ": Unknown";
				}
			}
			return $res;
		}
		return "Unknown";
	}

	public function getRefractionReading($criteria,$record)
	{
		$criteria->addInCondition('eye_id', $this->eyesCondition($record));
		$refraction = \OEModule\OphCiExamination\models\Element_OphCiExamination_Refraction::model()->with('event')->find($criteria);
		if ($refraction) {
			return $refraction->getCombined(strtolower($record['eye']));
		}
		else {
			return 'Unknown';
		}
	}

	protected function appendOpNoteValues(&$record, $event_id, $anaesthetic_type, $anaesthetic_delivery, $anaesthetic_comments, $anaesthetic_complications, $cataract_report, $cataract_predicted_refraction, $cataract_iol_type, $cataract_iol_power, $tamponade_used, $surgeon, $surgeon_role, $assistant, $assistant_role, $supervising_surgeon, $supervising_surgeon_role, $opnote_comments)
	{
		$anaesthetic = Element_OphTrOperationnote_Anaesthetic::model()->find('event_id = :event_id',array(':event_id'=>$event_id));

		if ($anaesthetic_type) {
			$record['anaesthetic_type'] = $anaesthetic->anaesthetic_type['name'];
		}

		if ($anaesthetic_delivery) {
			$record['anaesthetic_delivery'] = $anaesthetic->anaesthetic_delivery['name'];
		}

		if ($anaesthetic_comments) {
			$record['anaesthetic_comments'] = $anaesthetic['anaesthetic_comment'];
		}

		if ($anaesthetic_complications) {
			$complications = array();
			if (isset($anaesthetic->anaesthetic_complications)) {
				foreach($anaesthetic->anaesthetic_complications as $complication) {
					$complications[] = $complication['name'];
				}
				$record['anaesthetic_complications'] = implode(',',$complications);
			}
		}

		if ($cataract_report) {
			foreach (array('cataract_report', 'cataract_predicted_refraction', 'cataract_iol_type', 'cataract_iol_power') as $k) {
				$record[$k] = '';
			}
			if ($cataract_element = Element_OphTrOperationnote_Cataract::model()->find('event_id = :event_id',array(':event_id'=>$event_id))) {
				$record['cataract_report'] = trim(preg_replace('/\s\s+/', ' ', $cataract_element['report']));
				$record['cataract_predicted_refraction'] = $cataract_element->predicted_refraction;
				if ($cataract_element->iol_type) {
					$record['cataract_iol_type'] = $cataract_element->iol_type->name;
				}
				else {
					$record['cataract_iol_type'] = 'None';
				}
				$record['cataract_iol_power'] = $cataract_element->iol_power;
			}
		}

		if ($tamponade_used) {
			if ($tamponade_element = Element_OphTrOperationnote_Tamponade::model()->find('event_id = :event_id', array(':event_id'=>$event_id))) {
				$record['tamponade_used'] = $tamponade_element->gas_type->name;
			}
			else {
				$record['tamponade_used'] = 'None';
			}
		}

		if ($surgeon || $surgeon_role || $assistant || $assistant_role || $supervising_surgeon || $supervising_surgeon_role) {
			$surgeon_element = Element_OphTrOperationnote_Surgeon::model()->findByAttributes(array('event_id' => $event_id));

			foreach (array('surgeon', 'assistant', 'supervising_surgeon') as $surgeon_type) {
				$role = $surgeon_type.'_role';
				if (${$surgeon_type} || ${$role}) {
					$surgeon = $surgeon_element->{$surgeon_type};
					if (${$surgeon_type}) $record[$surgeon_type] = $surgeon ? $surgeon->getFullName() : 'None';
					if (${$role}) $record["{$surgeon_type}_role"] = $surgeon ? $surgeon->role : 'None';
				}
			}
		}

		if ($this->opnote_comments) {
			$comments = Element_OphTrOperationnote_Comments::model()->find('event_id = :event_id',array(':event_id'=>$event_id));
			$record['opnote_comments'] = trim(preg_replace('/\s\s+/', ' ', $comments['comments']));
		}
	}

	public function getColumns()
	{
		$return = array(
			'Operation date',
			Patient::model()->getAttributeLabel('hos_num'),
			Patient::model()->getAttributeLabel('first_name'),
			Patient::model()->getAttributeLabel('last_name'),
			Patient::model()->getAttributeLabel('gender'),
			Patient::model()->getAttributeLabel('dob'),
			'Eye',
			'Procedures',
			'Complications',
		);

		if ($this->surgeon_id) {
			$return[] = 'Role';
		}

		foreach (array(
				'patient_oph_diagnoses',
				'booking_diagnosis',
				'theatre',
				'bookingcomments',
				'surgerydate',
				'anaesthetic_type',
				'anaesthetic_delivery',
				'anaesthetic_comments',
				'anaesthetic_complications',
				'cataract_report' => array(
					'cataract_predicted_refraction',
					'cataract_iol_type',
					'cataract_iol_power',
				),
				'tamponade_used',
				'surgeon',
				'surgeon_role',
				'assistant',
				'assistant_role',
				'supervising_surgeon',
				'supervising_surgeon_role',
				'opnote_comments',
				'comorbidities',
				'target_refraction',
				'first_eye',
				) as $key => $value) {
			if (is_int($key)) {
				if ($this->$value) {
					$return[] = $this->getAttributeLabel($value);
				}
			} else {
				if ($this->$key) {
					$return[] = $this->getAttributeLabel($key);
					foreach ($value as $key2) {
						$return[] = $this->getAttributeLabel($key2);
					}
				}
			}
		}
		if ($this->va_values) {
			$return[] = 'Pre-op refraction';
			$return[] = 'Most recent post-op VA';
		}
		if ($this->refraction_values) {
			$return[] = 'Pre-op refraction';
			$return[] = 'Most recent post-op refraction';
		}

		return $return;
	}

	public function description()
	{
		$description = 'Operations';

		if ($this->surgeon_id) {
			$description .= ' by '.User::model()->find($this->surgeon_id)->fullName;
		}

		$description .= ' between '.date('j M Y',strtotime($this->date_from)).' and '.date('j M Y',strtotime($this->date_to));

		if (!empty($this->Procedures_procs)) {
			$description .= "\nwith procedures: ";

			foreach ($this->Procedures_procs as $i => $proc_id) {
				if ($i) $description .= ', ';
				$description .= Procedure::model()->findByPk($proc_id)->term;
			}
		}

		if (!empty($this->complications)) {
			$description .= "\nwith cataract complications: ";

			foreach ($this->complications as $i => $complication_id) {
				if ($i) $description .= ', ';
				$description .= OphTrOperationnote_CataractComplications::model()->findByPk($complication_id)->name;
			}
		}

		return $description;
	}

	/**
	 * Output the report in CSV format
	 *
	 * @return string
	 */
	public function toCSV()
	{
		$output = $this->description()."\n\n";
		$output .= implode(',',$this->getColumns())."\n";

		return $output . $this->array2Csv($this->operations);
	}
}
