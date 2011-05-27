<?php

$operations = $this->getRows('operations');
$sessions = $this->getRows('sessions');

$monthStart = date('Y-m-01');

$appointments = array();

if (!empty($operations)) {
	$sessionId = -1;
	foreach ($operations as $operation) {
		if (!empty($sessions)) {
			foreach ($sessions as $session) {
				if ($session['id'] > $sessionId && $session['date'] >= $monthStart) {
					$sessionId = $session['id'];
					break;
				}
			}
			$appointments[] = array(
				'element_operation_id' => $operation['id'],
				'session_id' => $sessionId,
				'display_order' => 1
			);
		}
		$sessionId++;
	}
}

return $appointments;