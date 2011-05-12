<?php

return array(
	'sequence1' => array(
		'theatre_id' => 1,
		'start_date' => date('Y-m-d', strtotime('+1 day')),
		'end_date' => null,
		'start_time' => '09:00:00',
		'end_time' => '13:00:00',
		'frequency' => Sequence::FREQUENCY_1WEEK
	),
	'sequence2' => array(
		'theatre_id' => 1,
		'start_date' => date('Y-m-d', strtotime('+2 days')),
		'end_date' => null,
		'start_time' => '10:00:00',
		'end_time' => '13:00:00',
		'frequency' => Sequence::FREQUENCY_2WEEKS
	),
	'sequence3' => array(
		'theatre_id' => 1,
		'start_date' => date('Y-m-d', strtotime('-1 month')),
		'end_date' => date('Y-m-d', strtotime('-1 day')),
		'start_time' => '10:00:00',
		'end_time' => '13:00:00',
		'frequency' => Sequence::FREQUENCY_1WEEK
	),
);