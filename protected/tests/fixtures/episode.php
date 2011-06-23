<?php
return array(
	'episode1' => array(
		'patient_id' => 1,
		'firm_id' => 1,
		'start_date' => date('Y-m-d H:i:s', strtotime('-30 days')),
	),
	'episode2' => array(
		'patient_id' => 1,
		'firm_id' => 2,
		'start_date' => date('Y-m-d H:i:s', strtotime('-7 days')),
	),
	'episode3' => array(
		'patient_id' => 2,
		'firm_id' => 2,
		'start_date' => date('Y-m-d H:i:s', strtotime('-7 days')),
	),
        'episode4' => array(
                'patient_id' => 3,
                'firm_id' => 2,
                'start_date' => date('Y-m-d H:i:s', strtotime('-7 days')),
        ),
);
