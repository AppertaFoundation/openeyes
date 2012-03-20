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

class GenerateSessionsCommand extends CConsoleCommand {
	
	public function getName() {
		return 'Generate Session Data Command.';
	}
	
	public function getHelp() {
		return "A script to generate session data based on sequences in the database for future dates.\n
			Optional parameters to 1) specify the end date for the script, 2) specify whether output should be returned rather than displayed.\n";
	}

	public function run($args) {

		$output = '';
		
		// Get sequences
		$today = date('Y-m-d');
		$initialEndDate = empty($args) ? strtotime('+13 months') : strtotime($args[0]);
		$sequences = Sequence::model()->findAll(
			'start_date <= :end_date AND (end_date IS NULL or end_date >= :today)',
			array(':end_date'=>date('Y-m-d', $initialEndDate), ':today'=>$today)
		);

		foreach($sequences as $sequence) {

			// Find most recent session for sequence
			$session = Yii::app()->db->createCommand()
			->select('date')
			->from('session')
			->where('sequence_id=:id', array(':id' => $sequence->id))
			->order('date DESC')
			->queryRow();

			// The date of the most recent session for this sequence plus one day, or the sequence start date if no sessions for this sequence yet
			$startDate = empty($session) ? strtotime($sequence->start_date) : strtotime($session['date']) + (60 * 60 * 24);

			// Sessions should be generated up to the smaller of initialEndDate (+13 months or command line) and sequence end_date
			if($sequence->end_date && strtotime($sequence->end_date) < $initialEndDate) {
				$endDate = strtotime($sequence->end_date);
			} else {
				$endDate = $initialEndDate;
			}

			$dateList = array();
			if($sequence->repeat_interval == Sequence::FREQUENCY_ONCE) {
				// NO REPEAT (single session)
				// If a session already exists for this one off there's no point creating another
				if (empty($session)) {
					$dateList[] = $sequence->start_date;
				}
			} else if($sequence->repeat_interval == Sequence::FREQUENCY_MONTHLY && $sequence->week_selection) {
				// MONTHLY REPEAT (weeks x,y of month)
				$date = date('Y-m-d', $startDate);
				$time = $startDate;
				// Get the next occurrence of the sequence on/after the start date
				while (date('N', $time) != date('N', strtotime($sequence->start_date))) {
					$date = date('Y-m-d', mktime(0,0,0, date('m', $time), date('d', $time) + 1, date('Y', $time)));
					$time = strtotime($date);
				}
				$dateList = $sequence->getWeekOccurrences($sequence->weekday, $sequence->week_selection, $time, $endDate, $date, date('Y-m-d', $endDate));
			} else {
				// WEEKLY REPEAT (every x weeks)
				// There is a repeat interval, e.g. once every two weeks. In the instance of two weeks, the
				//	function below returns 60 * 60 * 24 * 14, i.e. two weeks
				$interval = $sequence->getFrequencyInteger($sequence->repeat_interval, $endDate);

				// The number of days in the interval - 14 in the case of two week interval
				$days = $interval / 24 / 60 / 60;

				// IF there's no session use the sequence start date. If there is use the most recent
				//	session date plus the interval (e.g. two weeks)
				if (empty($session)) {
					$nextStartDate = $startDate;
				} else {
					$nextStartDate = $startDate + $interval - 86400;
				}

				// Convert $nextStartDate (a timestamp of the seqence start date or the most recent session date plus the interval to a date.
				$date = date('Y-m-d', $nextStartDate);

				// The timestamp of the start date
				$time = $nextStartDate;

				// get the next occurrence of the sequence on/after the start date

				// Check to see if the day of the week for the time is the same day of the week as the sequence start date
				//	Process loop if it isn't
				while (date('N', $time) != date('N', strtotime($sequence->start_date))) {
					// Set the date to $time + 1 day
					$date = date('Y-m-d', mktime(0,0,0, date('m', $time), date('d', $time) + 1, date('Y', $time)));

					// Set the time to the timstamp for the date + 1 day
					$time = strtotime($date);
				}

				while ($time <= $endDate) {
					$dateList[] = $date;

					$date = date('Y-m-d', mktime(0,0,0, date('m', $time), date('d', $time) + $days, date('Y', $time)));
					$time = strtotime($date);
				}
			}

			if(!empty($dateList)) {
				// Process dateList into sessions
				foreach($dateList as $date) {
					// TODO: Check for collisions, maybe in Session validation code
					$new_session = new Session();
					foreach(array('start_time','end_time','consultant','anaesthetist','paediatric','general_anaesthetic','theatre_id') as $attribute) {
						$new_session->$attribute = $sequence->$attribute;
					}
					$new_session->date = $date;
					$new_session->sequence_id = $sequence->id;
					$new_session->save();
					if($sequence->firmAssignment) {
						$new_firm_assignment = new SessionFirmAssignment();
						$new_firm_assignment->session_id = $new_session->id;
						$new_firm_assignment->firm_id = $sequence->firmAssignment->firm_id;
						$new_firm_assignment->save();
					}
				}
				$output .= "Sequence ID {$sequence->id}: Created " . count($dateList) . " session(s).\n";
			}
		}

		if (!empty($args[1])) {
			return $output;
		}
	}
}
