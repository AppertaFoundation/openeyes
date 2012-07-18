<?php
class BaseEventTypeElement extends BaseElement
{
	function getElementType() {
		if (Yii::app()->getController()->getModule()) {
			$event_type = EventType::model()->find('class_name=?',array(Yii::app()->getController()->getModule()->getName()));
			foreach (ElementType::model()->findAll("event_type_id=?",array($event_type->id)) as $element_type) {
				if ($element_type->class_name == get_class($this)) {
					return $element_type;
				}
			}
		} else {
			foreach (ElementType::model()->findAll("event_type_id=?",array(25)) as $element_type) {
				if ($element_type->class_name == get_class($this)) {
					return $element_type;
				}
			}
		}

		return false;
	}
	
	function render($action) {
		$this->Controller->renderPartial();
	}

	function getFormOptions($table) {
		$options = array();

		foreach (Yii::app()->db->createCommand()
			->select("$table.*")
			->from($table)
			->join("element_type_$table","element_type_$table.{$table}_id = $table.id")
			->where("element_type_id = ".$this->getElementType()->id)
			->order("display_order asc")
			->queryAll() as $option) {

			$options[$option['id']] = $option['name'];
		}

		return $options;
	}

	function getInfoText() {
	}

	function getCreate_view() {
		return get_class($this);
	}

	function getUpdate_view() {
		return get_class($this);
	}

	function getView_view() {
		return get_class($this);
	}

	function getPrint_view() {
		return get_class($this);
	}

	function isEditable() {
		return true;
	}
}
