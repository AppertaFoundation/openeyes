<?php
class BaseEventTypeElement extends BaseElement
{
	function getElementType() {
		return ElementType::model()->find('class_name=?', array(get_class($this)));
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
}
