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

class OphTrIntravitrealinjection_ReportInjections extends BaseReport
{
	public $date_from;
	public $date_to;
	public $given_by_id;
	public $summary;
	public $pre_va;
	public $post_va;
	public $injections;

	private $patient_id = null;
	protected $_drug_cache = array();
	protected $_examination_event_type_id;
	protected $_current_patient_id;
	protected $_patient_vas;

	public function attributeNames()
	{
		return array(
			'date_from',
			'date_to',
			'given_by_id',
			'summary',
			'pre_va',
			'post_va',
		);
	}

	public function attributeLabels()
	{
		return array(
			'date_from' => 'Date from',
			'date_to' => 'Date to',
			'given_by_id' => 'Given by',
			'summary' => 'Summarise patient data',
			'pre_va' => 'Pre injection VA',
			'post_va' => 'Post injection VA',
		);
	}

	public function rules()
	{
		return array(
			array('date_from, date_to, given_by_id, summary, pre_va, post_va', 'safe'),
			array('date_from, date_to, summary, pre_va, post_va', 'required'),
		);
	}

	public function run()
	{
		if (!$this->date_from) {
			$this->date_from = date('Y-m-d',strtotime("-1 year"));
		} else {
			$this->date_from = date('Y-m-d',strtotime($this->date_from));
		}

		if (!$this->date_to) {
			$this->date_to = date('Y-m-d');
		} else {
			$this->date_to = date('Y-m-d',strtotime($this->date_to));
		}

		if ($this->given_by_id) {
			if (!$user = User::model()->findByPk($this->given_by_id)) {
				throw new Exception("User not found: ".$this->given_by_id);
			}
		}

		if ($this->summary) {
			$this->injections = $this->getSummaryInjections($this->date_from, $this->date_to, @$user);
			$this->view = '_summary_injections';
		} else {
			$this->injections = $this->getInjections($this->date_from, $this->date_to, @$user);
			$this->view = '_injections';
		}
	}

	protected function extractSummaryData($patient_data)
	{
		$records = array();
		foreach (array('left', 'right') as $side) {
			if (@$patient_data[$side]) {
				foreach (array_keys($patient_data[$side]) as $drug) {
					foreach (array_keys($patient_data[$side][$drug]) as $site) {
						$records[] = array(
							'patient_hosnum' => $patient_data['patient_hosnum'],
							'patient_firstname' => $patient_data['patient_firstname'],
							'patient_surname' => $patient_data['patient_surname'],
							'patient_gender' => $patient_data['patient_gender'],
							'patient_dob' => $patient_data['patient_dob'],
							'eye' => $side,
							'drug' => $drug,
							'site' => $site,
							'first_injection_date' => $patient_data[$side][$drug][$site]['first_injection_date'],
							'last_injection_date' => $patient_data[$side][$drug][$site]['last_injection_date'],
							'injection_number' => $patient_data[$side][$drug][$site]['injection_number']
						);
					}
				}
			}
		}
		return $records;
	}

	protected function getSummaryInjections($date_from, $date_to, $given_by_user)
	{
		$patient_data = array();
		$where = '';
		$command = Yii::app()->db->createCommand()
				->select(
						"p.id as patient_id, treat.left_drug_id, treat.right_drug_id, treat.left_number, treat.right_number, e.id,
						e.created_date, c.first_name, c.last_name, e.created_date, p.hos_num,p.gender, p.dob, eye.name AS eye, site.name as site_name"
				)
				->from("et_ophtrintravitinjection_treatment treat")
				->join("event e", "e.id = treat.event_id")
				->join("episode ep", "e.episode_id = ep.id")
				->join("patient p", "ep.patient_id = p.id")
				->join("contact c", "p.contact_id = c.id")
				->join("eye", "eye.id = treat.eye_id")
				->join("et_ophtrintravitinjection_site insite", "insite.event_id = treat.event_id")
				->leftJoin("site", "insite.site_id = site.id")
				->order("p.id, e.created_date asc");
		// for debug
		if ($this->patient_id) {
			$where = "ep.patient_id = :pat_id and e.deleted = 0 and ep.deleted = 0 and e.created_date >= :from_date and e.created_date < (:to_date + interval 1 day)";
			$params = array(':from_date' => $date_from, ':to_date' => $date_to, ':pat_id' => $this->patient_id);
		} else {
			$where = "e.deleted = 0 and ep.deleted = 0 and e.created_date >= :from_date and e.created_date < (:to_date + interval 1 day)";
			$params = array(':from_date' => $date_from, ':to_date' => $date_to);
		}

		if ($given_by_user) {
			$where .= " and (treat.right_injection_given_by_id = :user_id or treat.left_injection_given_by_id = :user_id)";
			$params[':user_id'] = $given_by_user->id;
		}

		$command->where($where);

		$results = array();
		foreach ($command->queryAll(true, $params) as $row) {
			if (@$patient_data['id'] != $row['patient_id']) {
				if (@$patient_data['id']) {
					foreach ($this->extractSummaryData($patient_data) as $record) {
						$results[] = $record;
					}
				}
				$patient_data = array(
					"id" => $row['patient_id'],
					"patient_hosnum" => $row['hos_num'],
					"patient_firstname" => $row['first_name'],
					"patient_surname" => $row['last_name'],
					"patient_gender" => $row['gender'],
					"patient_dob" => date('j M Y', strtotime($row['dob'])),
				);
			}
			if (!$site = @$row['site_name']) {
				$site = 'Unknown';
			}
			foreach (array('left', 'right') as $side) {
				$dt = date('j M Y', strtotime($row['created_date']));
				if ($drug = $this->getDrugById($row[$side . '_drug_id'])) {
					$patient_data[$side][$drug->name][$site]['last_injection_date'] = $dt;
					$patient_data[$side][$drug->name][$site]['injection_number'] = $row[$side . '_number'];
					if (!isset($patient_data[$side][$drug->name][$site]['first_injection_date'])) {
						$patient_data[$side][$drug->name][$site]['first_injection_date'] = $dt;
					}
				}
			}
		}
		foreach ($this->extractSummaryData($patient_data) as $record) {
			$results[] = $record;
		}

		return $results;
	}

	protected function getInjections($date_from, $date_to, $given_by_user)
	{
		$where = "e.deleted = 0 and ep.deleted = 0 and e.created_date >= :from_date and e.created_date < (:to_date + interval 1 day)";

		$command = Yii::app()->db->createCommand()
				->select(
						"p.id as patient_id, treat.left_drug_id, treat.right_drug_id, treat.left_number, treat.right_number, e.id,
						e.created_date, c.first_name, c.last_name, e.created_date, p.hos_num,p.gender, p.dob, eye.name AS eye, site.name as site_name"
				)
				->from("et_ophtrintravitinjection_treatment treat")
				->join("event e", "e.id = treat.event_id")
				->join("episode ep", "e.episode_id = ep.id")
				->join("patient p", "ep.patient_id = p.id")
				->join("contact c", "p.contact_id = c.id")
				->join("eye", "eye.id = treat.eye_id")
				->join("et_ophtrintravitinjection_site insite", "insite.event_id = treat.event_id")
				->join("site", "insite.site_id = site.id")
				->order("p.id, e.created_date asc");
		$params = array(':from_date' => $date_from, ':to_date' => $date_to);

		if ($given_by_user) {
			$where .= " and (treat.right_injection_given_by_id = :user_id or treat.left_injection_given_by_id = :user_id)";
			$params[':user_id'] = $given_by_user->id;
		}

		$command->where($where);

		$results = array();
		foreach ($command->queryAll(true, $params) as $row) {
			$record = array(
					"injection_date" => date('j M Y', strtotime($row['created_date'])),
					"patient_hosnum" => $row['hos_num'],
					"patient_firstname" => $row['first_name'],
					"patient_surname" => $row['last_name'],
					"patient_gender" => $row['gender'],
					"patient_dob" => date('j M Y', strtotime($row['dob'])),
					"eye" => $row['eye'],
					"site_name" => $row['site_name'],
					'left_drug' => $this->getDrugString($row['left_drug_id']),
					'left_injection_number' => $row['left_number'],
					'right_drug' => $this->getDrugString($row['right_drug_id']),
					'right_injection_number' => $row['right_number'],
			);

			$this->appendExaminationValues($record, $row['patient_id'], $row['created_date']);

			$results[] = $record;
		}

		return $results;
	}

	public function description()
	{
		if ($this->summary) {
			$description = 'Summary of injections';
		} else {
			$description = 'Injections';
		}

		if ($this->given_by_id) {
			$description .= ' given by '.User::model()->findByPk($this->given_by_id)->fullName;
		}

		$description .= ' between '.date('j M Y',strtotime($this->date_from)).' and '.date('j M Y',strtotime($this->date_to));

		if ($this->pre_va && $this->post_va) {
			$description .= ' with pre-injection and post-injection VA';
		} else if ($this->pre_va) {
			$description .= ' with pre-injection VA';
		} else if ($this->post_va) {
			$description .= ' with post-injection VA';
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

		if ($this->summary) {
			$output .= Patient::model()->getAttributeLabel('hos_num').','.Patient::model()->getAttributeLabel('first_name').','.Patient::model()->getAttributeLabel('last_name').','.Patient::model()->getAttributeLabel('gender').','.Patient::model()->getAttributeLabel('dob').",Eye,Drug,Site,First injection date,Last injection date,Injection no";

			if ($this->pre_va) {
				$output .= ",Left pre-injection VA,Right pre-injection VA";
			}
			if ($this->post_va) {
				$output .= ",Left post-injection VA,Right post-injection VA";
			}
			$output .= "\n";
		} else {
			$output .= 'Date,'.Patient::model()->getAttributeLabel('hos_num').','.Patient::model()->getAttributeLabel('first_name').','.Patient::model()->getAttributeLabel('last_name').','.Patient::model()->getAttributeLabel('gender').','.Patient::model()->getAttributeLabel('dob').",Eye,Site,Left drug,Left injection no,Right drug,Right injection no";

			if ($this->pre_va) {
				$output .= ",Left pre-injection VA,Right pre-injection VA";
			}
			if ($this->post_va) {
				$output .= ",Left post-injection VA,Right post-injection VA";
			}
			$output .= "\n";
		}

		return $output . $this->array2Csv($this->injections);
	}

	/**
	 * simple cache for drug objects
	 *
	 * @param $drug_id
	 * @return OphTrIntravitrealinjection_Treatment_Drug|null
	 */
	protected function getDrugById($drug_id)
	{
		if (!@$this->_drug_cache[$drug_id]) {
			$this->_drug_cache[$drug_id] = OphTrIntravitrealinjection_Treatment_Drug::model()->findByPk($drug_id);
		}
		return $this->_drug_cache[$drug_id];
	}

	/**
	 * Return the printable string for the drug
	 *
	 * @param $drug_id
	 * @return string
	 */
	protected function getDrugString($drug_id)
	{
		if (!$drug_id) {
			return "N/A";
		}
		if ($drug = $this->getDrugById($drug_id)) {
			return $drug->name;
		}
		else {
			return "UNKNOWN";
		}
	}

	protected function appendExaminationValues(&$record, $patient_id, $event_date)
	{
		if ($this->pre_va || $this->post_va) {
			foreach (array('left_preinjection_va', 'right_preinjection_va', 'left_postinjection_va', 'right_postinjection_va') as $k) {
				$record[$k] = 'N/A';
			}
			$vas = $this->getPatientVAElements($patient_id);
			$before = null;
			$after = null;
			foreach ($vas as $va) {
				if ($va->created_date < $event_date) {
					$before = $va;
				}
				else if ($va->created_date > $event_date) {
					$after = $va;
					break;
				}
			}
			if ($this->pre_va) {
				if ($before) {
					$record['left_preinjection_va'] = $this->getBestVaFromReading('left', $before);
					$record['right_preinjection_va'] = $this->getBestVaFromReading('right', $before);
				}
				else {
					$record['left_preinjection_va'] = 'N/A';
					$record['right_preinjection_va'] = 'N/A';
				}
			}
			if ($this->post_va) {
				if ($after) {
					$record['left_postinjection_va'] = $this->getBestVaFromReading('left', $after);
					$record['right_postinjection_va'] = $this->getBestVaFromReading('right', $after);
				}
				else {
					$record['left_postinjection_va'] = "N/A";
					$record['right_postinjection_va'] = "N/A";
				}
			}
		}
	}

	/**
	 * in order to suck up too much memory for larger reports, when this method receives a call for a new patient, it ditches the cache
	 * it has of the previous patient.
	 *
	 * @param $patient_id
	 * @return Element_OphCiExamination_VisualAcuity[]
	 */
	protected function getPatientVAElements($patient_id)
	{
		if ($patient_id != $this->_current_patient_id) {
			$this->_current_patient_id = $patient_id;
			$command = Yii::app()->db->createCommand()
					->select(
							"e.id"
					)
					->from("event e")
					->join("episode ep", "e.episode_id = ep.id")
					->where("e.deleted = 0 and ep.deleted = 0 and ep.patient_id = :patient_id and e.event_type_id = :etype_id",
							array(':patient_id' => $patient_id, ':etype_id' => $this->getExaminationEventTypeId())
					);
			$event_ids = array();
			foreach ($command->queryAll() as $res) {
				$event_ids[] = $res['id'];
			}
			$criteria = new CDbCriteria();
			$criteria->addInCondition('event_id', $event_ids);
			$this->_patient_vas = OEModule\OphCiExamination\models\Element_OphCiExamination_VisualAcuity::model()->with('right_readings', 'left_readings')->findAll($criteria);
		}
		return $this->_patient_vas;
	}

	/**
	 * Simple wrapper function for getting a string representation of the best VA reading for a side from the given element
	 *
	 * @param $side
	 * @param Element_OphCiExamination_VisualAcuity $va
	 * @return string
	 */
	protected function getBestVaFromReading($side, OEModule\OphCiExamination\models\Element_OphCiExamination_VisualAcuity $va)
	{
		if ($reading = $va->getBestReading($side)) {
			return $reading->convertTo($reading->value, $va->unit_id) . ' (' . $reading->method->name . ')';
		}
		return "N/A";
	}

	protected function getExaminationEventTypeId() {
		if (!$this->_examination_event_type_id) {
			$this->_examination_event_type_id = EventType::model()->findByAttributes(array('class_name' => 'OphCiExamination'))->id;
		}
		return $this->_examination_event_type_id;
	}

}
