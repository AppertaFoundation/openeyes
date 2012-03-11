<?php

class BaseEventTypeController extends Controller
{
	public function actionIndex()
	{
		$this->render('index');
	}

	// Uncomment the following methods and override them if needed
	/*
	public function filters()
	{
		// return the filter configuration for this controller, e.g.:
		return array(
			'inlineFilterName',
			array(
				'class'=>'path.to.FilterClass',
				'propertyName'=>'propertyValue',
			),
		);
	}

	public function actions()
	{
		// return external action classes, e.g.:
		return array(
			'action1'=>'path.to.ActionClass',
			'action2'=>array(
				'class'=>'path.to.AnotherActionClass',
				'propertyName'=>'propertyValue',
			),
		);
	}
	*/

	/**
	 * Get all the elements for an event, the current module or an event_type
	 *
	 * @return array
	 */
	public function getDefaultElements($event=false, $event_type_id=false) {
		if ($event) {
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

		if ($event) {
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
       		 
	}	
}
