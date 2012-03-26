<?php
class OEEventManager extends CApplicationComponent {
	
	public $observers;
	
	public function dispatch($event_id, $params) {
		$observers = $this->observers[$event_id];
		foreach($observers as $observer) {
			$class_name = $observer['class'];
			$method = $observer['method'];
			$object = new $class_name;
			$return = $object->$method($params);
		}
	}
	
}