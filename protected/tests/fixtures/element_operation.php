<?php
return array(
	'element1' => array(
		'event_id' => 1,
		'eye' => ElementOperation::EYE_BOTH,
		'total_duration' => 90,
		'consultant_required' => true,
		'anaesthetist_required' => false,
		'anaesthetic_type' => ElementOperation::ANAESTHETIC_TOPICAL,
		'overnight_stay' => false,
		'comments' => 'foo',
	),
	'element2' => array(
		'event_id' => 2,
		'eye' => ElementOperation::EYE_LEFT,
		'total_duration' => 120,
		'consultant_required' => true,
		'anaesthetist_required' => false,
		'anaesthetic_type' => ElementOperation::ANAESTHETIC_TOPICAL,
		'overnight_stay' => false,
		'comments' => 'bar',
	)
);
