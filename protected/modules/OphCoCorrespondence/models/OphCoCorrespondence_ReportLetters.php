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

class OphCoCorrespondence_ReportLetters extends BaseReport
{
	public $match_correspondence;
	public $match_legacy_letters;
	public $phrases;
	public $condition_type;
	public $start_date;
	public $end_date;
	public $author_id;
	public $letters;

	public function attributeNames()
	{
		return array(
			'match_correspondence',
			'match_legacy_letters',
			'phrases',
			'condition_type',
			'start_date',
			'end_date',
			'author_id',
		);
	}

	public function attributeLabels()
	{
		return array(
			'match_correspondence' => 'Match correspondence',
			'match_legacy_letters' => 'Match legacy letters',
			'phrases' => 'Phrases',
			'condition_type' => 'Search method',
			'start_date' => 'Date from',
			'start_end' => 'Date end',
			'author_id' => 'Author',
		);
	}

	public function rules()
	{
		return array(
			array('match_correspondence, match_legacy_letters, phrases, condition_type, start_date, end_date, author_id', 'safe'),
			array('phrases, condition_type', 'required'),
		);
	}

	public function afterValidate()
	{
		if (!empty($this->phrases)) {
			$has_phrases = false;

			foreach ($this->phrases as $phrase) {
				$phrase && $has_phrases = true;
			}

			if (!$has_phrases) {
				$this->addError('phrases','Phrases cannot be blank.');
			}
		}

		if (!$this->match_correspondence && !$this->match_legacy_letters) {
			$this->addError('match_correspondence','Please select which type of letters you want to search');
		}

		return parent::afterValidate();
	}

	public function run()
	{
		$params = array();

		$where_clauses = array();
		$where_params = array();
		$type_clauses = array();
		$where_operator = ' ' . ($this->condition_type == 'and' ? 'and' : 'or') . ' ';

		$select = array('c.first_name','c.last_name','p.dob','p.hos_num','e.created_date','ep.patient_id');

		$data = $this->getDbCommand();

		if ($this->match_correspondence) {
			$this->joinLetters('Correspondence',$data,$select,$where_clauses,$where_params,$where_operator);
		}

		if ($this->match_legacy_letters) {
			$this->joinLetters('Legacy',$data,$select,$where_clauses,$where_params,$where_operator);
		}

		$where = " ( ".implode(' or ',$where_clauses)." ) ";

		if ($this->start_date) {
			$this->applyStartDate($where, $where_params);
		}

		if ($this->end_date) {
			$this->applyEndDate($where, $where_params);
		}

		$this->letters = array();

		$data->where($where,$where_params);
		$data->select(implode(',',$select));

		$this->executeQuery($data);
	}

	public function executeQuery($data)
	{
		foreach ($data->queryAll() as $i => $row) {
			if (@$row['lid']) {
				$row['type'] = 'Correspondence';
				$row['link'] = 'http'. (@$_SERVER['https'] ? 's' : '').'://'.@$_SERVER['SERVER_NAME'].'/OphCoCorrespondence/default/view/'.$row['event_id'];
			} else {
				$row['type'] = 'Legacy letter';
				$row['link'] = 'http'. (@$_SERVER['https'] ? 's' : '').'://'.@$_SERVER['SERVER_NAME'].'/OphLeEpatientletter/default/view/'.$row['l2_event_id'];
			}

			$this->letters[] = $row;
		}
	}

	public function getDbCommand()
	{
		return Yii::app()->db->createCommand()
			->from("event e")
			->join("episode ep","e.episode_id = ep.id")
			->join("patient p","ep.patient_id = p.id")
			->join("contact c","p.contact_id = c.id")
			->order("e.created_date asc");
	}

	public function joinLetters($type, $data, &$select, &$where_clauses, &$where_params, $where_operator)
	{
		$et = ($type == 'Correspondence')
			? EventType::model()->find('class_name=?',array('OphCoCorrespondence'))
			: EventType::model()->find('class_name=?',array('OphLeEpatientletter'));

		$letter_table = ($type == 'Correspondence')
			? array('et_ophcocorrespondence_letter','l')
			: array('et_ophleepatientletter_epatientletter','l2');

		$text_field = ($type == 'Correspondence') ? 'body' : 'letter_html';

		$data->leftJoin("{$letter_table[0]} {$letter_table[1]}","{$letter_table[1]}.event_id = e.id");

		$clause = "({$letter_table[1]}.id is not null and e.event_type_id = :et_{$letter_table[1]}_id and ( ";
		$where_params[":et_{$letter_table[1]}_id"] = $et->id;

		foreach ($this->phrases as $i => $phrase) {
			$where_params[":body{$letter_table[1]}".$i] = '%'.strtolower($phrase).'%';
			if ($i >0) {
				$clause .= $where_operator;
			}
			$clause .= " lower({$letter_table[1]}.$text_field) like :body{$letter_table[1]}$i";
		}

		$clause .= " )";

		if ($this->author_id) {
			if (!$author = User::model()->findByPk($this->author_id)) {
				throw new Exception("User not found: $this->author_id");
			}

			if ($type == 'Correspondence') {
				$clause .= " and {$letter_table[1]}.created_user_id = :authorID";
				$where_params[':authorID'] = $this->author_id;
			} else {
				$clause .= " and lower({$letter_table[1]}.$text_field) like :authorName";
				$where_params[':authorName'] = '%'.strtolower($author->fullName).'%';
			}
		}

		$where_clauses[] = $clause." )";
		$select[] = "{$letter_table[1]}.id as {$letter_table[1]}id";

		if ($type == 'Correspondence') {
			$select[] = "{$letter_table[1]}.event_id";
		} else {
			$select[] = "{$letter_table[1]}.event_id as l2_event_id";
		}
	}

	public function applyStartDate(&$where, &$where_params)
	{
		$where .= " and e.created_date >= :dateFrom";
		$where_params[':dateFrom'] = date('Y-m-d',strtotime($this->start_date))." 00:00:00";
	}

	public function applyEndDate(&$where, &$where_params)
	{
		$where .= " and e.created_date <= :dateTo";
		$where_params[':dateTo'] = date('Y-m-d',strtotime($this->end_date))." 23:59:59";
	}

	public function description()
	{
		if ($this->match_correspondence) {
			$description = 'Correspondence';
		}

		if ($this->match_legacy_letters) {
			if (@$description) {
				$description .= ' and legacy letters';
			} else {
				$description = 'Legacy letters';
			}
		}

		$description .= ' containing '.($this->condition_type == 'and' ? 'all' : 'any')." of these phrases:\n";

		foreach ($this->phrases as $phrase) {
			if ($phrase) {
				$description .= $phrase."\n";
			}
		}

		if ($this->start_date || $this->end_date || $this->author_id) {
			$description .= "written";

			if ($this->start_date && $this->end_date) {
				$description .= " between ".$this->start_date." and ".$this->end_date;
			} else if ($this->start_date) {
				$description .= " after ".$this->start_date;
			} else if ($this->end_date) {
				$description .= " before ".$this->end_date;
			}

			if ($this->author_id) {
				$description .= " by ".User::model()->findByPk($this->author_id)->fullName;
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

		$output .= Patient::model()->getAttributeLabel('hos_num').",".Patient::model()->getAttributeLabel('dob').",".Patient::model()->getAttributeLabel('first_name').",".Patient::model()->getAttributeLabel('last_name').",Date,Type,Link\n";

		foreach ($this->letters as $letter) {
			$output .= "\"{$letter['hos_num']}\",\"".($letter['dob'] ? date('j M Y',strtotime($letter['dob'])) : 'Unknown')."\",\"{$letter['first_name']}\",\"{$letter['last_name']}\",\"".date('j M Y',strtotime($letter['created_date']))."\",\"".$letter['type']."\",\"".$letter['link']."\"\n";
		}

		return $output;
	}
}
