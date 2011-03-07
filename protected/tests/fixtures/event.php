<?php
$episodes = $this->getRows('episodes');
$users = $this->getRows('users');
$eventTypes = $this->getRows('event_types');

return array(
	'event1' => array(
		'episode_id' => $episodes['episode1']['id'],
		'user_id' => $users['user1']['id'],
		'event_type_id' => $eventTypes['eventType1']['id'],
		'datetime' => date('Y-m-d H:i:s')
	),
);