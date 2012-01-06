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
http://www.openeyes.org.uk	 info@openeyes.org.uk
--
*/

class GenerateSessionsCommand extends CConsoleCommand
{
	public function getName()
	{
		return 'Generate Session Data Command.';
	}
	public function getHelp()
	{
		$help = "A script to generate session data based on sequences in the database for future dates.\n
Optional parameters to 1) specify the end date for the script, 2) specify whether output should be returned rather than displayed.\n";

		return $help;
	}

	public function run($args)
	{
		$output = '';
		$today = date('Y-m-d');
		$initialEndDate = empty($args) ? strtotime('+13 months') : strtotime($args[0]);
		$sequences = Sequence::model()->findAll(
			'start_date <= :end_date AND end_date IS NULL or end_date > :today',
			array(':end_date'=>date('Y-m-d', $initialEndDate), ':today'=>$today));

		foreach ($sequences as $sequence) {
			$endDate = $initialEndDate;

			$session = Yii::app()->db->createCommand()
				->select('date')
				->from('session')
				->where('sequence_id=:id', array(':id'=>$sequence->id))
				->order('date DESC')
				->queryRow();

			// The date of the most recent session for this sequence plus one day, or today if no sessions for this sequence yet
//			$startDate = empty($session) ? strtotime($today) : strtotime($session['date']) + (60 * 60 * 24);

			// The date of the most recent session for this sequence plus one day, or the seqeunce start date if no sessions
			//	for this sequence yet
			$startDate = empty($session) ? strtotime($sequence->start_date) : strtotime($session['date']) + (60 * 60 * 24);

			// The date to generate sessions until - the sequence end date if there is one, else (the date provided on the command
			//	line OR 13 months from now).
			$sequenceEnd = empty($sequence->end_date) ? $initialEndDate : strtotime($sequence->end_date);

			if ($endDate > $sequenceEnd) {
				// @todo - is this code for anything? endDate is the same as initialEndDate so the above
				//	ternary operator should make this impossible
				$endDate = $sequenceEnd;
			}

			$dateList = array();
			if (empty($sequence->week_selection)) {
				// No week selection, e.g. 1st on month, 2nd in month
				if (empty($sequence['repeat_interval'])) {
					// No repeat interval means it's one-off, so we only concern ourselves with the start date

					// If a session already exists for this one off there's no point creating another
					if (empty($session)) {
						$dateList[] = $sequence->start_date;
					}
				} else {
					// There is a repeat interval, e.g. once every two weeks. In the instance of two weeks, the
					//	function below returns 60 * 60 * 24 * 14, i.e. two weeks
					$interval = $sequence->getFrequencyInteger($sequence['repeat_interval'], $endDate);

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
			} else {
				$date = date('Y-m-d', $startDate);
				$time = $startDate;
				// get the next occurrence of the sequence on/after the start date
				while (date('N', $time) != date('N', strtotime($sequence->start_date))) {
					$date = date('Y-m-d', mktime(0,0,0, date('m', $time), date('d', $time) + 1, date('Y', $time)));
					$time = strtotime($date);
				}

				$dateList = $sequence->getWeekOccurrences($sequence->weekday, $sequence->week_selection, $time, $endDate, $date, date('Y-m-d', $endDate));
			}

			if (!empty($dateList)) {
				$insert = 'INSERT IGNORE INTO session (sequence_id, date, start_time, end_time, consultant, anaesthetist, paediatric) VALUES ';
				foreach ($dateList as $date) {
					$insert .= "({$sequence->id}, '$date', '{$sequence->start_time}', '{$sequence->end_time}', '{$sequence->consultant}', '{$sequence->anaesthetist}', '{$sequence->paediatric}')";
					if ($date != end($dateList)) {
						$insert .= ', ';
					}
					$insert .= "\n";
				}

				$output .= "\nSequence ID {$sequence->id}: Created " . count($dateList) . " session(s).\n";

				$command = Yii::app()->db->createCommand($insert);
				$command->execute();
			}
		}

		if (!empty($args[1])) {
			return $output;
		}
	}
}
