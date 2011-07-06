<?php

return array(
	'sequence1' => array(
		'theatre_id' => 1,
		'start_date' => date('Y-m-d', strtotime('+1 day')),
		'end_date' => null,
		'start_time' => '09:00:00',
		'end_time' => '13:00:00',
		'repeat_interval' => Sequence::FREQUENCY_1WEEK,
		'weekday' => date('N', strtotime('+1 day')),
		'week_selection' => 0,
	),
	'sequence2' => array(
		'theatre_id' => 1,
		'start_date' => date('Y-m-d', strtotime('+2 days')),
		'end_date' => null,
		'start_time' => '10:00:00',
		'end_time' => '13:00:00',
		'repeat_interval' => Sequence::FREQUENCY_2WEEKS,
		'weekday' => date('N', strtotime('+2 days')),
		'week_selection' => 0,
	),
	'sequence3' => array(
		'theatre_id' => 1,
		'start_date' => date('Y-m-d', strtotime('-1 month')),
		'end_date' => date('Y-m-d', strtotime('-1 day')),
		'start_time' => '10:00:00',
		'end_time' => '13:00:00',
		'repeat_interval' => Sequence::FREQUENCY_1WEEK,
		'weekday' => date('N', strtotime('-1 month')),
		'week_selection' => 0,
	),
	'sequence4' => array(
		'theatre_id' => 1,
		'start_date' => date('Y-m-d', strtotime('+5 days')),
		'end_date' => null,
		'start_time' => '13:30:00',
		'end_time' => '18:00:00',
		'repeat_interval' => 0,
		'weekday' => date('N', strtotime('+5 days')),
		'week_selection' => Sequence::SELECT_2NDWEEK + Sequence::SELECT_4THWEEK + Sequence::SELECT_5THWEEK,
	)
);