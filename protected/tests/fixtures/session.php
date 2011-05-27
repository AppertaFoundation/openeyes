<?php

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