<?php

return array(
	'booking1' => array(
		'element_operation_id' => 1,
		'date' => date('Y-m-d'),
		'start_time' => '09:00',
		'end_time' => '13:00',
		'theatre_id' => 1,
		'cancelled_date' => date('Y-m-d', strtotime('-7 days')),
		'user_id' => 1,
		'cancelled_reason_id' => 1
	),
	'booking2' => array(
		'element_operation_id' => 2,
		'date' => date('Y-m-d', strtotime('-2 days')),
		'start_time' => '13:30',
		'end_time' => '18:00',
		'theatre_id' => 2,
		'cancelled_date' => date('Y-m-d', strtotime('-30 days')),
		'user_id' => 2,
		'cancelled_reason_id' => 2,
	),
);