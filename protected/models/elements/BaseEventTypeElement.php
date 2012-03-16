<?php
class BaseEventTypeElement extends BaseElement
{
	function getElementType() {
		return ElementType::model()->find('class_name=?', array(get_class($this)));
	}
	function render($action) {
		$this->Controller->renderPartial();
	}
}
