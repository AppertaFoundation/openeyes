<?php

class BaseEventTypeController extends Controller
{
	public function actionIndex()
	{
		$this->render('index');
	}

	/**
	 * Get all the elements for an event, the current module or an event_type
	 *
	 * @return array
	 */
	public function getDefaultElements($event=false, $event_type_id=false) {
		if ($event and isset($event->event_type_id)) {
			$event_type = EventType::model()->find('id = ?',array($event->event_type_id));
		} else if ($event_type_id) {
			$event_type = EventType::model()->find('id = ?',array($event_type_id));
		} else {
			$event_type = EventType::model()->find('class_name = ?',array($this->getModule()->name));
		}
		$criteria = new CDbCriteria;
		$criteria->compare('event_type_id',$event_type->id);
		$criteria->order = 'display_order asc';

		$elements = array();

		if ($event and isset($event->event_type_id)) {
			foreach (ElementType::model()->findAll($criteria) as $element_type) {
				$element_class = $element_type->class_name;
				if ($element = $element_class::model()->find('event_id = ?',array($event->id))) {
					$elements[] = new $element_class;
				}
			}
		} else {
			$criteria->compare('`default`',1);

			foreach (ElementType::model()->findAll($criteria) as $element_type) {
				$element_class = $element_type->class_name;

				$elements[] = new $element_class;
			}
		}

		return $elements;
	}

	/**
	 * Get the optional elements for the current module's event type
	 * This will be overriden by the module
	 *
	 * @return array
	 */
	public function getOptionalElements($action, $event=false) {
		switch ($action) {
			case 'create':
			case 'view':
				return array();
			case 'update':
				$event_type = EventType::model()->find('id = ?',array($event->event_type_id));

				$criteria = new CDbCriteria;
				$criteria->compare('event_type_id',$event_type->id);
				$criteria->compare('default',1);
				$criteria->order = 'display_order asc';

				$elements = array();
				foreach (ElementType::model()->findAll($criteria) as $element_type) {
					$element_class = $element_type->class_name;
					if (!$element_class::model()->find('event_id = ?',array($id))) {
						$elements[] = new $element_class;
					}
				}
				
				return $elements;
		}
	}

	public function actionCreate() {
                $this->renderPartial(
			'create',
                        array('elements' => $this->getDefaultElements(), 'eventId' => null, 'editable' => true),
			false, true
		);
	}

	public function renderDefaultElements($action, $event=false, $data=false) {
		foreach ($this->getDefaultElements($action, $event=false, $data=false) as $element) {
                        $this->renderPartial(
				$action . '_' . get_class($element),
                        	array('event' => $event, 'element' => $element, 'data' => $data),
                        	false, true
			);
		}
	}

	public function renderOptionalElements($action, $event=false, $data=false) {
		foreach ($this->getOptionalElements($action, $event=false, $data=false) as $element) {
                        $this->renderPartial(
				$action . '_' . get_class($element),
                        	array('event' => $event, 'element' => $element, 'data' => $data),
                        	false, true
			);
		}
	}
}
