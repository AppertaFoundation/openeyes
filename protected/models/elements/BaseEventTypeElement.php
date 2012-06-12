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

		if (Yii::app()->getDb()->getSchema()->getTable("element_type_$table")) {
			foreach (Yii::app()->db->createCommand()
				->select("$table.*")
				->from($table)
				->join("element_type_$table","element_type_$table.{$table}_id = $table.id")
				->where("element_type_id = ".$this->getElementType()->id)
				->order("display_order asc")
				->queryAll() as $option) {

				$options[$option['id']] = $option['name'];
			}
		} else {
			foreach (Yii::app()->db->createCommand()
				->select("$table.*")
				->from($table)
				->queryAll() as $option) {

				$options[$option['id']] = $option['name'];
			}
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
