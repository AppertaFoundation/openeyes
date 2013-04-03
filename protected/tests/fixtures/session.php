<?php
/**
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


$sequences = $this->getRows('sequences');
$sessions = array();

$today = date('Y-m-d');

if (!empty($sequences)) {
	foreach ($sequences as $sequence) {
		$endDate = strtotime('+1 year');
		$startDate = ($today < $sequence['start_date']) ? strtotime($sequence['start_date']) : strtotime($today);
		$sequenceEnd = empty($sequence['end_date']) ? $endDate : strtotime($sequence['end_date']);

		if ($endDate > $sequenceEnd) {
			$endDate = $sequenceEnd;
		}

		$dateList = array();
		if (empty($sequence['week_selection'])) {
			$interval = Sequence::model()->getFrequencyInteger($sequence['repeat_interval'], $endDate);
			$days = $interval / 24 / 60 / 60;

			$date = date('Y-m-d', $startDate);
			$time = $startDate;
			// get the next occurrence of the sequence on/after the start date
			while (date('N', $time) != date('N', strtotime($sequence['start_date']))) {
				$date = date('Y-m-d', mktime(0,0,0, date('m', $time), date('d', $time) + 1, date('Y', $time)));
				$time = strtotime($date);
			}

			while ($time <= $endDate) {
				$dateList[] = $date;

				$date = date('Y-m-d', mktime(0,0,0, date('m', $time), date('d', $time) + $days, date('Y', $time)));
				$time = strtotime($date);
			}
		} else {
			$date = date('Y-m-d', $startDate);
			$time = $startDate;
			// get the next occurrence of the sequence on/after the start date
			while (date('N', $time) != date('N', strtotime($sequence['start_date']))) {
				$date = date('Y-m-d', mktime(0,0,0, date('m', $time), date('d', $time) + 1, date('Y', $time)));
				$time = strtotime($date);
			}				

			$dateList = Sequence::model()->getWeekOccurrences($sequence['weekday'], $sequence['week_selection'], $time, $endDate, $date, date('Y-m-d', $endDate));
		}

		if (!empty($dateList)) {
			foreach ($dateList as $date) {
				$sessions[] = array(
					'sequence_id' => $sequence['id'],
					'date' => $date,
					'start_time' => $sequence['start_time'],
					'end_time' => $sequence['end_time'],
				);
			}
		}
	}
}

return $sessions;