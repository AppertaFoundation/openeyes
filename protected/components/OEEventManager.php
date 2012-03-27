<?php
class OEEventManager extends CApplicationComponent {
	
	public $observers;
	
	public function dispatch($event_id, $params) {
		$observers = isset($this->observers[$event_id]) ? $this->observers[$event_id] : array();
		foreach($observers as $observer) {
			$class_name = $observer['class'];
			$method = $observer['method'];
			$object = new $class_name;
			$return = $object->$method($params);
		}
	}
	
}